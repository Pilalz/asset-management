<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depreciation;
use App\Models\Asset;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Jobs\RunBulkDepreciation;

class DepreciationController extends Controller
{
    public function depre(Asset $asset)
    {
        $periodsProcessed = 0;

        try {
            DB::transaction(function () use ($asset, &$periodsProcessed) {
                
                if ($asset->commercial_useful_life_month <= 0 || $asset->commercial_nbv <= 0) {
                    return; // Lewati jika tidak valid
                }

                // Tentukan periode pengejaran
                $lastDepreciation = Depreciation::where('asset_id', $asset->id)
                    ->where('type', 'commercial')
                    ->latest('depre_date')
                    ->first();
                
                $startDate = $lastDepreciation 
                    ? Carbon::parse($lastDepreciation->depre_date)->addMonth()->startOfMonth()
                    : Carbon::parse($asset->start_depre_date)->startOfMonth();

                $endDate = now()->startOfMonth();

                if ($startDate->greaterThan($endDate)) {
                    return; // Lanjut ke tipe berikutnya jika tidak ada yang perlu dikejar
                }

                // Inisialisasi nilai awal
                $currentBookValue = $asset->commercial_nbv;
                $currentAccumulatedDepre = $asset->commercial_accum_depre;
                $monthlyDepre = round($asset->acquisition_value / $asset->commercial_useful_life_month);
                
                for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addMonth()) {
                    if ($currentBookValue <= 0) break;

                    // Tambahkan 1 ke penghitung setiap kali loop berjalan
                    $periodsProcessed++; 

                    $finalDepreciationAmount = $monthlyDepre;
                    if (($currentBookValue - $monthlyDepre) <= 0) {
                        $finalDepreciationAmount = $currentBookValue;
                    }

                    $currentBookValue -= $finalDepreciationAmount;
                    $currentAccumulatedDepre += $finalDepreciationAmount;

                    Depreciation::create([
                        'asset_id'          => $asset->id,
                        'type'              => 'commercial',
                        'depre_date'        => $date->copy()->endOfMonth()->toDateString(),
                        'monthly_depre'     => $finalDepreciationAmount,
                        'accumulated_depre' => $currentAccumulatedDepre,
                        'book_value'        => $currentBookValue,
                        'company_id'        => $asset->company_id,
                    ]);
                    
                    if ($currentBookValue <= 0) break;
                }

                // Update data master aset dengan nilai final
                $asset->update([
                    'commercial_accum_depre' => $currentAccumulatedDepre,
                    'commercial_nbv'         => $currentBookValue,
                ]);
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        if ($periodsProcessed > 0) {
            return back()->with('success', 'Depresiasi yang terlewat untuk aset ' . ($asset->assetName->name ?? $asset->asset_number) . ' berhasil dicatat.');
        } else {
            return back()->with('info', 'Tidak ada periode depresiasi yang perlu dijalankan untuk aset ini.');
        }
    }

    public function index(Request $request)
    {
        // Tentukan rentang tahun, default ke tahun ini
        $year = $request->input('year', now()->year);
        $startDate = Carbon::create($year, 1, 1)->startOfMonth();
        $endDate = Carbon::create($year, 12, 1)->endOfMonth();

        // 1. Ambil semua data depresiasi dalam rentang tahun dengan relasinya
        // Eager Loading ('asset.assetName...') untuk menghindari N+1 query problem
        $schedules = Depreciation::with([
                'asset', 
                'asset.assetName.assetSubClass.assetClass'
            ])
            ->whereBetween('depre_date', [$startDate, $endDate])
            ->whereHas('asset', function ($query) {
                $query->where('status', 'Active');
            })
            ->whereHas('asset', function ($query) {
                $query->where('asset_type', 'FA');
            })
            ->where('type', 'commercial')
            ->orderBy('asset_id')
            ->orderBy('depre_date')
            ->get();

        // 2. Lakukan Transformasi Data (Pivot)
        $pivotedData = [];
        foreach ($schedules as $schedule) {
            $assetId = $schedule->asset_id;
            $monthKey = Carbon::parse($schedule->depre_date)->format('Y-m');

            // Jika aset ini belum ada di array, tambahkan data masternya
            if (!isset($pivotedData[$assetId])) {
                $pivotedData[$assetId] = [
                    'master_data' => $schedule->asset,
                    'schedule' => [] // Siapkan array untuk jadwal bulanannya
                ];
            }

            // Isi data depresiasi untuk bulan yang sesuai
            $pivotedData[$assetId]['schedule'][$monthKey] = (object)[
                'monthly_depre' => $schedule->monthly_depre,
                'accumulated_depre' => $schedule->accumulated_depre,
                'book_value' => $schedule->book_value,
            ];
        }

        // 3. Buat daftar bulan untuk header tabel
        $months = [];
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($period as $date) {
            $months[$date->format('Y-m')] = $date->format('M-y'); // Contoh: 'Jan-25'
        }
        
        // 4. Kirim data yang sudah ditransformasi ke view
        return view('depreciation.commercial.index', [
            'pivotedData' => $pivotedData,
            'months' => $months,
            'selectedYear' => $year
        ]);
    }


    public function runAll()
    {
        $companyId = session('active_company_id');
        $jobId = 'depreciation_status_' . $companyId;

        // Cek jika job sudah berjalan
        $status = Cache::get($jobId);
        if ($status && $status['status'] === 'running') {
            return response()->json(['message' => 'Proses depresiasi sudah berjalan.'], 409); // 409 Conflict
        }

        // Kirim tugas ke antrian
        RunBulkDepreciation::dispatch($companyId);

        return response()->json(['message' => 'Proses depresiasi massal telah dimulai.']);
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
}
