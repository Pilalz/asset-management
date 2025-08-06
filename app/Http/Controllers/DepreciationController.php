<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depreciation;
use App\Models\Asset;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepreciationController extends Controller
{
    public function depre(Asset $asset)
    {
        $depreDateForPeriod = now()->endOfMonth();
        
        //Validasi agar tidak dijalankan lebih dari 1x
        $alreadyRunThisMonth = Depreciation::where('asset_id', $asset->id)
            ->whereBetween('depre_date', [
                $depreDateForPeriod->copy()->startOfMonth(), 
                $depreDateForPeriod->copy()->endOfMonth()
            ])->exists();

        if ($alreadyRunThisMonth) {
            return back()->with('info', 'Depresiasi untuk aset ini pada periode ini sudah dijalankan.');
        }

        //Rumus dan validasi UL
        $monthly_depre = 0;
        if ($asset->useful_life_month > 0) {
            $monthly_depre = round($asset->current_cost / $asset->useful_life_month);
        } else {
            return back()->with('error', 'Masa manfaat aset (useful_life_month) tidak boleh nol.');
        }

        //Validasi Asset Depresiasi = 0
        if (($asset->net_book_value - $monthly_depre) <= 0) {
            $final_depreciation_amount = $asset->net_book_value;
            $new_book_value = 0;
            $new_status = 'Fully Depreciated';
        } else {
            $final_depreciation_amount = $monthly_depre;
            $new_book_value = $asset->net_book_value - $final_depreciation_amount;
            $new_status = $asset->current_status;
        }

        if ($final_depreciation_amount <= 0) {
            return back()->with('info', 'Aset ini sudah terdepresiasi penuh.');
        }

        try {
            DB::transaction(function () use ($asset, $depreDateForPeriod, $final_depreciation_amount, $new_book_value, $new_status) {
                // Kalkulasi akumulasi depresiasi
                $lastDepreciation = Depreciation::where('asset_id', $asset->id)->latest('depre_date')->first();
                $new_accumulated_depre = $final_depreciation_amount;
                if ($lastDepreciation) {
                    $new_accumulated_depre += $lastDepreciation->accumulated_depre;
                }

                $depre_date_for_db = $depreDateForPeriod->toDateString(); // Format: Y-m-d

                // Buat log depresiasi
                Depreciation::create([
                    'asset_id' => $asset->id,
                    'depre_date' => $depre_date_for_db,
                    'monthly_depre' => $final_depreciation_amount,
                    'accumulated_depre' => $new_accumulated_depre,
                    'book_value' => $new_book_value,
                    'company_id' => Auth::user()->last_active_company_id,
                ]);

                // Update data master aset
                $asset->update([
                    'accum_depre' => $new_accumulated_depre,
                    'net_book_value' => $new_book_value,
                    // 'current_status' => $new_status,
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Depresiasi untuk aset ' . $asset->asset_name . ' berhasil dicatat.');
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
        return view('depreciation.index', [
            'pivotedData' => $pivotedData,
            'months' => $months,
            'selectedYear' => $year
        ]);
    }
}
