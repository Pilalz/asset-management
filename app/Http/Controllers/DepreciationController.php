<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depreciation;
use App\Models\Asset;
use App\Models\Company;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use App\Jobs\RunBulkDepreciation;

use App\Exports\CommercialDepreciationsExport;
use App\Exports\FiscalDepreciationsExport;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

use Symfony\Component\HttpFoundation\StreamedResponse;

class DepreciationController extends Controller
{
    // Tidak digunakan (belum di maintenance)
    // public function depre(Asset $asset)
    // {
    //     Gate::authorize('is-admin');

    //     $periodsProcessed = 0;

    //     try {
    //         DB::transaction(function () use ($asset, &$periodsProcessed) {
                
    //             $types = ['commercial', 'fiscal'];

    //             foreach ($types as $type) {
    //                 // Tentukan nama kolom dinamis
    //                 $usefulLifeCol = $type . '_useful_life_month';
    //                 $nbvCol        = $type . '_nbv';
    //                 $accumDepreCol = $type . '_accum_depre';

    //                 if ($asset->$usefulLifeCol <= 0 || $asset->$nbvCol <= 0) {
    //                     continue; // Lewati tipe ini jika tidak valid
    //                 }

    //                 // Tentukan periode pengejaran untuk tipe ini
    //                 $lastDepreciation = Depreciation::where('asset_id', $asset->id)
    //                     ->where('type', $type)
    //                     ->latest('depre_date')
    //                     ->first();
                    
    //                 $startDate = $lastDepreciation 
    //                     ? Carbon::parse($lastDepreciation->depre_date)->addMonth()->startOfMonth()
    //                     : Carbon::parse($asset->start_depre_date)->startOfMonth();

    //                 $endDate = now()->startOfMonth();

    //                 if ($startDate->greaterThan($endDate)) {
    //                     continue; // Lanjut ke tipe berikutnya jika tidak ada yang perlu dikejar
    //                 }

    //                 // Inisialisasi nilai awal
    //                 $currentBookValue = $asset->$nbvCol;
    //                 $currentAccumulatedDepre = $asset->$accumDepreCol;
    //                 $monthlyDepre = round($asset->acquisition_value / $asset->$usefulLifeCol);
                    
    //                 for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addMonth()) {
    //                     if ($currentBookValue <= 0) break;

    //                     // Tambahkan 1 ke penghitung setiap kali loop berjalan
    //                     $periodsProcessed++; 

    //                     $finalDepreciationAmount = $monthlyDepre;
    //                     if (($currentBookValue - $monthlyDepre) <= 0) {
    //                         $finalDepreciationAmount = $currentBookValue;
    //                     }

    //                     $currentBookValue -= $finalDepreciationAmount;
    //                     $currentAccumulatedDepre += $finalDepreciationAmount;

    //                     Depreciation::create([
    //                         'asset_id'          => $asset->id,
    //                         'type'              => $type,
    //                         'depre_date'        => $date->copy()->endOfMonth()->toDateString(),
    //                         'monthly_depre'     => $finalDepreciationAmount,
    //                         'accumulated_depre' => $currentAccumulatedDepre,
    //                         'book_value'        => $currentBookValue,
    //                         'company_id'        => $asset->company_id,
    //                     ]);
                        
    //                     if ($currentBookValue <= 0) break;
    //                 }

    //                 // Update data master aset dengan nilai final untuk tipe ini
    //                 $asset->update([
    //                     $accumDepreCol => $currentAccumulatedDepre,
    //                     $nbvCol        => $currentBookValue,
    //                 ]);
    //             }
    //         });

    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }

    //     if ($periodsProcessed > 0) {
    //         return back()->with('success', 'Depresiasi yang terlewat untuk aset ' . ($asset->assetName->name ?? $asset->asset_number) . ' berhasil dicatat.');
    //     } else {
    //         return back()->with('info', 'Tidak ada periode depresiasi yang perlu dijalankan untuk aset ini.');
    //     }
    // }

    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $companyId = session('active_company_id');

        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate   = Carbon::create($year, 12, 31)->endOfDay();

        $assets = Asset::where('company_id', $companyId)
            ->where('asset_type', 'FA')
            ->whereNotIn('status', ['Sold', 'Onboard', 'Disposal'])
            ->with(['assetName', 'location', 'department'])
            ->whereHas('depreciations', function($query) use ($startDate, $endDate) {
                $query->where('type', 'commercial')
                      ->whereBetween('depre_date', [$startDate, $endDate]);
            })
            ->orderBy('id')
            ->paginate(15);

        $assetIds = $assets->pluck('id')->toArray();
        
        $depreciations = [];
        if (!empty($assetIds)) {
            $depreciations = Depreciation::whereIn('asset_id', $assetIds)
                ->where('type', 'commercial')
                ->whereBetween('depre_date', [$startDate, $endDate])
                ->get();
        }

        $pivotedData = [];
        foreach ($assets as $asset) {
            $pivotedData[$asset->id] = [
                'master_data' => $asset,
                'schedule' => []
            ];
        }

        foreach ($depreciations as $depre) {
            $monthKey = Carbon::parse($depre->depre_date)->format('Y-m');
            if (isset($pivotedData[$depre->asset_id])) {
                $pivotedData[$depre->asset_id]['schedule'][$monthKey] = (object)[
                    'monthly_depre' => $depre->monthly_depre,
                    'accumulated_depre' => $depre->accumulated_depre,
                    'book_value' => $depre->book_value,
                ];
            }
        }

        return view('depreciation.commercial.index', [
            'pivotedData' => $pivotedData, 
            'paginator' => $assets, 
            'months' => $this->getMonths($year),
            'selectedYear' => $year
        ]);
    }

    public function indexFiscal(Request $request)
    {
        $year = $request->input('year', now()->year);
        $companyId = session('active_company_id');

        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate   = Carbon::create($year, 12, 31)->endOfDay();

        $assets = Asset::where('company_id', $companyId)
            ->where('asset_type', 'FA')
            ->whereNotIn('status', ['Sold', 'Onboard', 'Disposal'])
            ->with(['assetName', 'location', 'department'])
            ->whereHas('depreciations', function($query) use ($startDate, $endDate) {
                $query->where('type', 'fiscal')
                      ->whereBetween('depre_date', [$startDate, $endDate]);
            })
            ->orderBy('id')
            ->paginate(15);

        $assetIds = $assets->pluck('id')->toArray();

        $depreciations = [];
        if (!empty($assetIds)) {
            $depreciations = Depreciation::whereIn('asset_id', $assetIds)
                ->where('type', 'fiscal')
                ->whereBetween('depre_date', [$startDate, $endDate])
                ->get();
        }

        $pivotedData = [];
        foreach ($assets as $asset) {
            $pivotedData[$asset->id] = [
                'master_data' => $asset,
                'schedule' => []
            ];
        }

        foreach ($depreciations as $depre) {
            $monthKey = Carbon::parse($depre->depre_date)->format('Y-m');
            if (isset($pivotedData[$depre->asset_id])) {
                $pivotedData[$depre->asset_id]['schedule'][$monthKey] = (object)[
                    'monthly_depre' => $depre->monthly_depre,
                    'accumulated_depre' => $depre->accumulated_depre,
                    'book_value' => $depre->book_value,
                ];
            }
        }
        
        return view('depreciation.fiscal.index', [
            'pivotedData' => $pivotedData, 
            'paginator' => $assets, 
            'months' => $this->getMonths($year),
            'selectedYear' => $year
        ]);
    }

    public function runAll()
    {
        Gate::authorize('is-admin');
        
        $companyId = session('active_company_id');
        $jobId = 'depreciation_status_' . $companyId;

        $jobStatus = Cache::get($jobId);

        if ($jobStatus && in_array($jobStatus['status'], ['queued', 'running'])) {
            return response()->json([
                'message' => 'Proses depresiasi sedang berjalan atau dalam antrian.'
            ], 409);
        }

        Cache::put($jobId, [
            'status' => 'queued', 
            'progress' => 0, 
            'message' => 'Menunggu antrian worker...'
        ], now()->addMinutes(5));

        RunBulkDepreciation::dispatch($companyId);

        return response()->json(['message' => 'Permintaan depresiasi telah masuk antrian.']);
    }

    public function getStatus()
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return response()->json(['status' => 'idle']);
        }

        $jobId = 'depreciation_status_' . $companyId;

        $status = Cache::get($jobId, ['status' => 'idle']); // Default 'idle' jika tidak ada

        return response()->json($status);
    }

    public function clearStatus()
    {
        $companyId = session('active_company_id');
        if ($companyId) {
            Cache::forget('depreciation_status_' . $companyId);
        }
        return response()->json(['status' => 'cleared']);
    }

    public function stream()
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return; // Hentikan jika tidak ada company
        }
        $jobId = 'depreciation_status_' . $companyId;

        return new StreamedResponse(function() use ($jobId) {
            $lastStatus = null;
            $initialCheck = true;

            while (true) {
                $currentStatus = Cache::get($jobId);

                if ($currentStatus !== $lastStatus || $initialCheck || ($currentStatus && in_array($currentStatus['status'], ['completed', 'failed'])) ) {
                    
                    $statusToSend = $currentStatus ?: ($initialCheck ? null : ['status' => 'idle']);
                    
                    if ($statusToSend !== $lastStatus || $initialCheck) {
                        echo "data: " . json_encode($statusToSend) . "\n\n";
                        ob_flush();
                        flush();

                        $lastStatus = $statusToSend;
                        $initialCheck = false;
                    }
                }

                // Jika job selesai, gagal, atau tidak ada, tutup koneksi
                if (!$currentStatus || ($currentStatus && in_array($currentStatus['status'], ['completed', 'failed']))) {
                    break;
                }
                
                sleep(1); // Tunggu 1 detik sebelum memeriksa lagi
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
            'Cache-Control' => 'no-cache',
        ]);
    }

    public function exportExcelCommercial(Request $request)
    {
        $year = $request->input('year', now()->year);        

        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'Commercial-Depreciations-'. $year. '-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new CommercialDepreciationsExport($year), $fileName);
    }

    public function exportExcelFiscal(Request $request)
    {
        $year = $request->input('year', now()->year);        

        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'Fiscal-Depreciations-'. $year. '-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new FiscalDepreciationsExport($year), $fileName);
    }

    private function getMonths($year)
    {
        $months = [];
        $startDate = Carbon::create($year, 1, 1)->startOfMonth();
        $endDate = Carbon::create($year, 12, 1)->endOfMonth();
        
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        
        foreach ($period as $date) {
            $months[$date->format('Y-m')] = $date->format('M-y');
        }

        return $months;
    }
}
