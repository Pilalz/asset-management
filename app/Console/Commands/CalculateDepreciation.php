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

        $today = Carbon::now()->endOfMonth();

        $this->info("Processing commercial depreciation for {$today->format('F Y')}...");

        // Ambil semua aset yang aktif, relevan, dan belum lunas
        $assetsToDepreciate = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'Active')
            ->where('asset_type', 'FA')
            ->where('start_depre_date', '<=', now())
            ->where('commercial_useful_life_month', '>', 0) // Pastikan masa manfaat valid
            ->whereColumn('commercial_accum_depre', '<', 'acquisition_value') // Cek jika belum lunas
            ->get();

        if ($assetsToDepreciate->isEmpty()) {
            $this->line("No assets to depreciate for this month.");
        } else {
            foreach ($assetsToDepreciate as $asset) {
                /** @var \App\Models\Asset $asset */
                // Cek apakah depresiasi untuk bulan ini sudah pernah dijalankan
                $alreadyRun = Depreciation::withoutGlobalScope(CompanyScope::class)
                    ->where('asset_id', $asset->id)
                    ->whereYear('depre_date', $today->year)
                    ->whereMonth('depre_date', $today->month)
                    ->exists();

                if ($alreadyRun) {
                    $this->line("Skipping asset #{$asset->asset_number}: depreciation already run.");
                    continue;
                }

                // Lakukan kalkulasi
                $monthlyDepre = $asset->acquisition_value / $asset->commercial_useful_life_month;
                $newAccumDepre = $asset->commercial_accum_depre + $monthlyDepre;
                $newBookValue = $asset->acquisition_value - $newAccumDepre;

                // Jangan biarkan book value menjadi negatif
                if ($newBookValue < 0) {
                    $monthlyDepre += $newBookValue;
                    $newBookValue = 0;
                    $newAccumDepre = $asset->acquisition_value;
                }

                // Simpan record depresiasi baru
                Depreciation::create([
                    'asset_id'          => $asset->id,
                    'depre_date'        => $today,
                    'monthly_depre'     => $monthlyDepre,
                    'accumulated_depre' => $newAccumDepre,
                    'book_value'        => $newBookValue,
                    'company_id'        => $asset->company_id,
                ]);

                // Update nilai di tabel aset utama
                $asset->update([
                    'commercial_accum_depre' => $newAccumDepre,
                    'commercial_nbv'         => $newBookValue,
                ]);

                $this->info("Success for asset #{$asset->asset_number}.");
            }
        }

        $this->info('Monthly depreciation calculation finished.');
        return 0;
    }
}
