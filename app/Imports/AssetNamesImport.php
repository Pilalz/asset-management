<?php

namespace App\Imports;

use App\Models\AssetSubClass;
use App\Models\AssetName;
use App\Scopes\CompanyScope;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterChunk;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetNamesImport implements ToModel, WithStartRow, WithValidation, WithChunkReading, ShouldQueue, WithEvents, SkipsOnFailure,WithMapping
{
    use SkipsFailures;

    private $assetSubClassesCache;
    private $companyId;
    private $jobId;
    private $totalRows;

    public function __construct($companyId, $jobId, $totalRows = 0)
    {
        $this->companyId = $companyId;
        $this->jobId = $jobId;
        $this->totalRows = $totalRows;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function startRow(): int
    {
        return 3;
    }

    public function map($row): array
    {
        return [
            $row[0], // Sub Class Name
            $row[1], // Asset Name
            $row[2], // Grouping
            $row[3], // Commercial
            $row[4], // Fiscal
        ];
    }

    public function rules(): array
    {
        return [
            '0' => [
                'required',
                Rule::exists('asset_sub_classes', 'name')->where('company_id', $this->companyId),
            ],
            '1' => [
                'required', 'string', 'max:255',
                Rule::unique('asset_names', 'name')->where('company_id', $this->companyId),
            ],
            '2' => [
                'required', 'max:255',
                Rule::unique('asset_names', 'grouping')->where('company_id', $this->companyId),
            ],
            '3' => 'required',
            '4' => 'required',
        ];
    }

    public function model(array $row)
    {        
        if (!$this->assetSubClassesCache) {
            $this->assetSubClassesCache = AssetSubClass::withoutGlobalScope(CompanyScope::class)->where('company_id', $this->companyId)
                ->pluck('id', 'name');
        }

        $subClassName = $row[0];

        $subClassId = null;
                
        if (isset($this->assetSubClassesCache[$subClassName])) {
            $subClassId = $this->assetSubClassesCache[$subClassName];
        } else {
            foreach ($this->assetSubClassesCache as $nameDB => $idDB) {
                if (strcasecmp($nameDB, $subClassName) == 0) {
                    $subClassId = $idDB;
                    break;
                }
            }
        }

        if (!$subClassId) {
            return null; 
        }

        return new AssetName([
            'sub_class_id' => $subClassId,
            'name'         => $row[1],
            'grouping'     => $row[2],
            'commercial'   => $row[3],
            'fiscal'       => $row[4],
            'company_id'   => $this->companyId,
        ]);
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('Import Gagal Baris ke-' . $failure->row() . ': ' . implode(', ', $failure->errors()));
        }
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $totalRows = $event->getReader()->getTotalRows();
                Log::info('--- DEBUG IMPORT ---');
                Log::info('Total Baris per Sheet: ' . json_encode($totalRows));
            },
            AfterChunk::class => function (AfterChunk $event) {
                $currentProgress = Cache::get($this->jobId);
                if ($currentProgress && $this->totalRows > 0) {
                    $processed = $currentProgress['processed_rows'] ?? 0;
                    $processed += $this->chunkSize(); 
                    if ($processed > $this->totalRows) $processed = $this->totalRows;
                    $percentage = round(($processed / $this->totalRows) * 100);
                    Cache::put($this->jobId, [
                        'status' => 'running', 
                        'progress' => $percentage,
                        'processed_rows' => $processed
                    ], now()->addHour());
                }
            },
        ];
    }
}