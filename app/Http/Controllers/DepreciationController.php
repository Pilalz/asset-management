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
        if ($asset->useful_life_month <= 0) {
            return back()->with('error', 'Masa manfaat aset (useful life month) tidak boleh nol.');
        }
        if ($asset->net_book_value <= 0) {
            return back()->with('info', 'Aset ini sudah terdepresiasi penuh.');
        }

        try {
            DB::transaction(function () use ($asset) {
                // --- Menentukan Periode Pengejaran ---
                $lastDepreciation = Depreciation::where('asset_id', $asset->id)->latest('depre_date')->first();
                
                // Tanggal mulai adalah bulan setelah depresiasi terakhir, atau start_depre_date jika belum pernah.
                $startDate = $lastDepreciation 
                    ? Carbon::parse($lastDepreciation->depre_date)->addMonth()->startOfMonth()
                    : Carbon::parse($asset->start_depre_date)->startOfMonth();

                $endDate = now()->startOfMonth();

                // --- Jika tidak ada periode yang perlu dikejar ---
                if ($startDate->greaterThan($endDate)) {
                    // Menggunakan redirect()->back() agar bisa di-chain dengan with()
                    return redirect()->back()->with('info', 'Tidak ada periode depresiasi yang perlu dijalankan untuk aset ini.');
                }

                // --- Inisialisasi Nilai Awal ---
                $currentBookValue = $asset->net_book_value;
                $currentAccumulatedDepre = $asset->accum_depre;
                $monthlyDepre = round($asset->current_cost / $asset->useful_life_month);
                $newStatus = $asset->status;

                // --- Looping untuk Setiap Bulan yang Terlewat ---
                for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date->addMonth()) {
                    
                    // Jika nilai buku sudah 0, hentikan loop
                    if ($currentBookValue <= 0) {
                        break;
                    }

                    $finalDepreciationAmount = $monthlyDepre;
                    
                    // Cek jika depresiasi bulan ini akan membuat nilai buku menjadi 0 atau minus
                    if (($currentBookValue - $monthlyDepre) <= 0) {
                        $finalDepreciationAmount = $currentBookValue; // Depresiasi terakhir adalah sisa nilai buku
                        $newStatus = 'Fully Depreciated';
                    }

                    // Update nilai berjalan
                    $currentBookValue -= $finalDepreciationAmount;
                    $currentAccumulatedDepre += $finalDepreciationAmount;

                    // Buat log depresiasi untuk bulan ini
                    Depreciation::create([
                        'asset_id' => $asset->id,
                        'depre_date' => $date->copy()->endOfMonth()->toDateString(), // Selalu di akhir bulan
                        'monthly_depre' => $finalDepreciationAmount,
                        'accumulated_depre' => $currentAccumulatedDepre,
                        'book_value' => $currentBookValue,
                        'company_id' => $asset->company_id,
                    ]);

                    // Jika sudah terdepresiasi penuh, keluar dari loop
                    if ($newStatus === 'Fully Depreciated') {
                        break;
                    }
                }

                // --- Update Data Master Aset (hanya sekali setelah loop selesai) ---
                $asset->update([
                    'accum_depre' => $currentAccumulatedDepre,
                    'net_book_value' => $currentBookValue,
                    'status' => $newStatus,
                ]);
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Depresiasi untuk aset ' . ($asset->assetName->name ?? $asset->asset_number) . ' berhasil dicatat.');
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
