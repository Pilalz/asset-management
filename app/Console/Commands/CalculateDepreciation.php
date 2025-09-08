<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;
use App\Models\Depreciation;
use App\Models\Location;
use Carbon\Carbon;
use App\Scopes\CompanyScope;

class CalculateDepreciation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calculate-depreciation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and record monthly asset depreciation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly depreciation calculation...');

        // Ambil semua aset yang aktif dan sudah melewati tanggal mulai depresiasi
        $assetsToDepreciate = Asset::withoutGlobalScope(CompanyScope::class)
                                    ->where('status', 'Active')
                                    ->where('asset_type', 'FA')
                                    ->where('start_depre_date', '<=', now())
                                    ->whereColumn('accum_depre', '<', 'acquisition_value') // Hanya proses aset yang belum habis didepresiasi
                                    ->get();

        $today = Carbon::now()->endOfMonth(); // Hitung untuk akhir bulan ini

        foreach ($assetsToDepreciate as $asset) {
            // Cek apakah depresiasi untuk bulan ini sudah pernah dijalankan untuk aset ini
            $alreadyRun = Depreciation::withoutGlobalScope(CompanyScope::class)
                                      ->where('asset_id', $asset->id)
                                      ->whereYear('depre_date', $today->year)
                                      ->whereMonth('depre_date', $today->month)
                                      ->exists();

            if ($alreadyRun) {
                $this->line("Depreciation for asset #{$asset->asset_number} for {$today->format('F Y')} already exists. Skipping.");
                continue; // Lanjut ke aset berikutnya
            }

            // Pastikan umur aset tidak nol untuk menghindari pembagian dengan nol
            if($asset->useful_life_month <= 0) {
                $this->warn("Asset #{$asset->asset_number} has an invalid useful life (0 or less). Skipping.");
                continue;
            }

            // Lakukan kalkulasi
            $monthlyDepre = $asset->acquisition_value / $asset->useful_life_month;
            $newAccumDepre = $asset->accum_depre + $monthlyDepre;
            $newBookValue = $asset->acquisition_value - $newAccumDepre;

            // Jangan biarkan book value menjadi negatif
            if ($newBookValue < 0) {
                $monthlyDepre += $newBookValue; // Sesuaikan depresiasi bulan ini
                $newBookValue = 0;
                $newAccumDepre = $asset->acquisition_value;
            }

            // Simpan record depresiasi baru
            Depreciation::create([
                'asset_id' => $asset->id,
                'depre_date' => $today,
                'monthly_depre' => $monthlyDepre,
                'accumulated_depre' => $newAccumDepre,
                'book_value' => $newBookValue,
                'company_id' => $asset->company_id,
            ]);

            // Update nilai di tabel aset utama
            $asset->update([
                'accum_depre' => $newAccumDepre,
                'net_book_value' => $newBookValue,
            ]);

            $this->info("Successfully calculated depreciation for asset #{$asset->asset_number}.");
        }

        $this->info('Monthly depreciation calculation finished.');
        return 0;
    }
}
