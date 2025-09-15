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
        $types = ['commercial', 'fiscal'];

        foreach ($types as $type) {
            $this->info("Processing '{$type}' depreciation for {$today->format('F Y')}...");

            // Tentukan nama kolom dinamis berdasarkan tipe
            $usefulLifeCol = $type . '_useful_life_month';
            $accumDepreCol = $type . '_accum_depre';
            $nbvCol        = $type . '_nbv';

            // Ambil semua aset yang aktif, relevan, dan belum lunas untuk TIPE INI
            $assetsToDepreciate = Asset::withoutGlobalScope(CompanyScope::class)
                ->where('status', 'Active')
                ->where('asset_type', 'FA')
                ->where('start_depre_date', '<=', now())
                ->where($usefulLifeCol, '>', 0) // Pastikan masa manfaat valid untuk tipe ini
                ->whereColumn($accumDepreCol, '<', 'acquisition_value') // Cek jika belum lunas untuk tipe ini
                ->get();

            if ($assetsToDepreciate->isEmpty()) {
                $this->line("No assets to depreciate for '{$type}' type this month.");
                continue; // Lanjut ke tipe berikutnya
            }

            foreach ($assetsToDepreciate as $asset) {
                // Cek apakah depresiasi untuk TIPE ini sudah pernah dijalankan
                $alreadyRun = Depreciation::withoutGlobalScope(CompanyScope::class)
                    ->where('asset_id', $asset->id)
                    ->where('type', $type) // <-- Pengecekan baru
                    ->whereYear('depre_date', $today->year)
                    ->whereMonth('depre_date', $today->month)
                    ->exists();

                if ($alreadyRun) {
                    $this->line("Skipping asset #{$asset->asset_number}: '{$type}' depreciation already run.");
                    continue;
                }

                // Lakukan kalkulasi menggunakan kolom dinamis
                $monthlyDepre = $asset->acquisition_value / $asset->$usefulLifeCol;
                $newAccumDepre = $asset->$accumDepreCol + $monthlyDepre;
                $newBookValue = $asset->acquisition_value - $newAccumDepre;

                // Jangan biarkan book value menjadi negatif
                if ($newBookValue < 0) {
                    $monthlyDepre += $newBookValue;
                    $newBookValue = 0;
                    $newAccumDepre = $asset->acquisition_value;
                }

                // Simpan record depresiasi baru dengan menyertakan tipe
                Depreciation::create([
                    'asset_id'          => $asset->id,
                    'type'              => $type, // <-- Simpan jenisnya
                    'depre_date'        => $today,
                    'monthly_depre'     => $monthlyDepre,
                    'accumulated_depre' => $newAccumDepre,
                    'book_value'        => $newBookValue,
                    'company_id'        => $asset->company_id,
                ]);

                // Update nilai di tabel aset utama menggunakan kolom dinamis
                $asset->update([
                    $accumDepreCol => $newAccumDepre,
                    $nbvCol        => $newBookValue,
                ]);

                $this->info("Success for asset #{$asset->asset_number} ({$type}).");
            }
        }

        $this->info('Monthly depreciation calculation finished.');
        return 0;
    }
}
