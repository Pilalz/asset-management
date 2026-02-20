<?php

namespace App\Exports;

use App\Models\Asset;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CommercialDepreciationsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    use Exportable;

    protected $startYear;
    protected $endYear;
    protected $months = [];

    public function __construct(int $startYear, int $endYear)
    {
        $this->startYear = $startYear;
        $this->endYear = $endYear;

        // Siapkan header bulan dari startYear hingga endYear
        for ($year = $this->startYear; $year <= $this->endYear; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create($year, $month, 1);
                $this->months[$date->format('Y-m')] = $date->format('M-y'); // Jan-25, Feb-25, ..., Dec-26
            }
        }
    }

    // Mulai dari baris ke-2 (Baris 1 buat Header Judul Bulan)
    public function startCell(): string
    {
        return 'A2';
    }

    public function query()
    {
        $startDate = Carbon::create($this->startYear, 1, 1)->startOfMonth();
        $endDate = Carbon::create($this->endYear, 12, 1)->endOfMonth();

        return Asset::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', session('active_company_id'))
            // --- FILTER: Hanya yang PUNYA depresiasi di tahun ini ---
            ->whereHas('depreciations', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('depre_date', [$startDate, $endDate])
                    ->where('type', 'commercial');
            })
            // --------------------------------------------------------
            ->with([
                'assetName', // Cukup load relasi yang perlu ditampilkan
                'depreciations' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('depre_date', [$startDate, $endDate])
                        ->where('type', 'commercial')
                        ->orderBy('depre_date');
                }
            ]);
    }

    public function headings(): array
    {
        // Ini Header Baris Kedua (Sub-Header)
        // No, Asset Name, Asset Number, [Monthly, Accum, Book] x 12
        $headers = [
            'No',
            'Asset Name',
            'Asset Number',
        ];

        foreach ($this->months as $label) {
            $headers[] = 'Monthly Depre';
            $headers[] = 'Accum Depre';
            $headers[] = 'Book Value';
        }

        return $headers;
    }

    public function map($asset): array
    {
        // Data Aset
        // Karena kita pakai FromQuery, kita gak punya index loop otomatis.
        // Kita kosongkan kolom 'No' dulu, nanti bisa diisi manual atau dibiarkan kosong
        // Atau pakai $asset->id sebagai pengganti sementara

        $row = [
            '', // Kolom No (Nanti diisi lewat view atau dibiarkan)
            $asset->assetName->name ?? '-',
            $asset->asset_number,
        ];

        // Pivot Data
        $depres = $asset->depreciations->keyBy(function ($item) {
            return Carbon::parse($item->depre_date)->format('Y-m');
        });

        foreach ($this->months as $keyYm => $label) {
            if (isset($depres[$keyYm])) {
                $data = $depres[$keyYm];
                $row[] = $data->monthly_depre;
                $row[] = $data->accumulated_depre;
                $row[] = $data->book_value;
            } else {
                $row[] = 0;
                $row[] = 0;
                $row[] = 0;
            }
        }

        return $row;
    }

    // --- MAGIC UNTUK HEADER YANG CANTIK ---
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $monthsCount = count($this->months);

                // 1. Tambahkan Header Utama (Baris 1)
                // Kolom A, B, C dibiarkan kosong di baris 1
                $colIndex = 4; // Mulai dari Kolom D (index 4)
    
                foreach ($this->months as $label) {
                    // Konversi index angka ke huruf Excel (D, E, F...)
                    $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 2);

                    // Merge 3 kolom (Monthly, Accum, Book) untuk 1 Bulan
                    $sheet->mergeCells("{$startCol}1:{$endCol}1");

                    // Tulis Nama Bulan (Jan-25)
                    $sheet->setCellValue("{$startCol}1", $label);

                    // Style: Tengah & Bold
                    $sheet->getStyle("{$startCol}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("{$startCol}1")->getFont()->setBold(true);

                    $colIndex += 3;
                }

                // 2. Style Header Baris 2 (Sub-Header)
                $lastColIndex = 3 + ($monthsCount * 3);
                $lastColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

                $sheet->getStyle("A2:{$lastColStr}2")->getFont()->setBold(true);
                $sheet->getStyle("A2:{$lastColStr}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 3. Tambahkan Auto Numbering di Kolom A (No)
                // Kita hitung total baris data
                $highestRow = $sheet->getHighestRow();
                if ($highestRow > 2) {
                    $rowNum = 1;
                    for ($row = 3; $row <= $highestRow; $row++) {
                        $sheet->setCellValue("A{$row}", $rowNum++);
                    }
                }
            },
        ];
    }
}