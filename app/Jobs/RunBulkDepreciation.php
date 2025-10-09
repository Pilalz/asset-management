<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Asset;
use App\Models\Depreciation;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class RunBulkDepreciation implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $companyId;
    protected $jobId;
    /**
     * Create a new job instance.
     */
    public function __construct($companyId)
    {
        $this->companyId = $companyId;
        $this->jobId = 'depreciation_status_' . $this->companyId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::put($this->jobId, ['status' => 'running', 'progress' => 0], now()->addHour());

        try {
            $assetsToDepreciate = Asset::withoutGlobalScope(CompanyScope::class)
                ->whereNotIn('status', ['Sold', 'Disposal'])
                ->where('asset_type', 'FA')
                ->where('start_depre_date', '<=', now())
                ->where('company_id', $this->companyId)
                ->get();

            $types = ['commercial', 'fiscal'];
            
            $totalOperations = 0;
            foreach ($assetsToDepreciate as $asset) {
                foreach ($types as $type) {
                    $usefulLifeCol = $type . '_useful_life_month';
                    $nbvCol = $type . '_nbv';

                    if ($asset->$usefulLifeCol <= 0 || $asset->$nbvCol <= 0) continue;

                    $lastDepreciation = Depreciation::withoutGlobalScope(CompanyScope::class)
                        ->where('asset_id', $asset->id)
                        ->where('type', $type)
                        ->latest('depre_date')
                        ->first();
                    $startDate = $lastDepreciation ? Carbon::parse($lastDepreciation->depre_date)->addMonth() : Carbon::parse($asset->start_depre_date);
                    $endDate = now();

                    if ($startDate->lessThanOrEqualTo($endDate)) {
                        $totalOperations += $startDate->diffInMonths($endDate) + 1;
                    }
                }
            }

            if ($totalOperations === 0) {
                Cache::put($this->jobId, ['status' => 'completed', 'progress' => 100, 'message' => 'Tidak ada asset yang perlu di depresiasi.'], now()->addHour());
                return;
            }

            $processedCount = 0;
            foreach ($assetsToDepreciate as $asset) {
                foreach ($types as $type) {
                    $usefulLifeCol = $type . '_useful_life_month';
                    $accumDepreCol = $type . '_accum_depre';
                    $nbvCol        = $type . '_nbv';

                    if ($asset->$usefulLifeCol <= 0 || $asset->$nbvCol <= 0) continue;

                    $lastDepreciation = Depreciation::withoutGlobalScope(CompanyScope::class)
                        ->where('asset_id', $asset->id)
                        ->where('type', $type)
                        ->latest('depre_date')
                        ->first();
                    $startDate = $lastDepreciation ? Carbon::parse($lastDepreciation->depre_date)->addMonth()->startOfMonth() : Carbon::parse($asset->start_depre_date)->startOfMonth();
                    $endDate = now()->startOfMonth();

                    if ($startDate->greaterThan($endDate)) {
                        continue;
                    }

                    $monthlyDepre = round($asset->acquisition_value / $asset->$usefulLifeCol);
                    $currentBookValue = $asset->$nbvCol;
                    $currentAccumulatedDepre = $asset->$accumDepreCol;

                    $period = CarbonPeriod::create($startDate, '1 month', $endDate);

                    foreach ($period as $date) {
                        if ($currentBookValue <= 0) break;

                        $finalDepreciationAmount = $monthlyDepre;
                        if (($currentBookValue - $monthlyDepre) <= 0) {
                            $finalDepreciationAmount = $currentBookValue;
                        }
                        
                        $currentBookValue -= $finalDepreciationAmount;
                        $currentAccumulatedDepre += $finalDepreciationAmount;

                        Depreciation::create([
                            'asset_id' => $asset->id, 
                            'type' => $type, 
                            'depre_date' => $date->endOfMonth(),
                            'monthly_depre' => $finalDepreciationAmount, 
                            'accumulated_depre' => $currentAccumulatedDepre,
                            'book_value' => $currentBookValue, 
                            'company_id' => $asset->company_id,
                        ]);
                        
                        $processedCount++;
                        Cache::put($this->jobId, ['status' => 'running', 'progress' => ($processedCount / $totalOperations) * 100]);
                    }

                    $asset->update([
                        $accumDepreCol => $currentAccumulatedDepre,
                        $nbvCol        => $currentBookValue,
                    ]);
                }
            }

            Cache::put($this->jobId, ['status' => 'completed', 'progress' => 100, 'message' => 'Semua asset sudah di depresiasi.'], now()->addHour());

        } catch (Throwable $e) {
            Cache::put($this->jobId, ['status' => 'failed', 'error' => $e->getMessage()], now()->addHour());
            throw $e;
        }
    }
}
