<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\AssetLocationHistory;
use App\Models\AssetUserHistory;

class AssetObserver
{
    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset): void
    {
        if ($asset->location_id) {
            AssetLocationHistory::create([
                'asset_id'    => $asset->id,
                'location_id' => $asset->location_id,
                'start_date'  => $asset->created_at ?? now(),
            ]);
        }

        // 2. Catat History Pemegang (User) Pertama
        if ($asset->user_id) {
            AssetUserHistory::create([
                'asset_id'   => $asset->id,
                'user_id'    => $asset->user_id,
                'start_date' => $asset->created_at ?? now(),
            ]);
        }
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        $threshold = 120; // Menit (Ambang batas koreksi typo)

        // 1. LOGIC UNTUK LOKASI
        if ($asset->isDirty('location_id')) {
            $this->handleHistory(
                $asset, 
                AssetLocationHistory::class, 
                'location_id', 
                $threshold
            );
        }

        // 2. LOGIC UNTUK USER
        if ($asset->isDirty('user_id')) {
            $this->handleHistory(
                $asset, 
                AssetUserHistory::class, 
                'user_id', 
                $threshold
            );
        }
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        //
    }

    /**
     * Handle the Asset "restored" event.
     */
    public function restored(Asset $asset): void
    {
        $this->created($asset);
    }

    /**
     * Handle the Asset "force deleted" event.
     */
    public function forceDeleted(Asset $asset): void
    {
        //
    }

    private function handleHistory($asset, $historyModel, $column, $threshold)
    {
        $newValue = $asset->$column;

        // Cari record terakhir yang masih aktif (end_date NULL)
        $lastHistory = $historyModel::where('asset_id', $asset->id)
            ->whereNull('end_date')
            ->first();

        if ($lastHistory) {
            // Jika perubahan terjadi sangat cepat (kurang dari threshold)
            // Kita anggap ini adalah KOREKSI TYPO/SALAH INPUT
            if ($lastHistory->start_date->diffInMinutes(now()) < $threshold) {
                $lastHistory->update([
                    $column => $newValue
                ]);
                return;
            }

            // Jika lewat threshold, tutup record lama
            $lastHistory->update(['end_date' => now()]);
        }

        // Buat record history baru
        $historyModel::create([
            'asset_id' => $asset->id,
            $column => $newValue,
            'start_date' => now(),
        ]);
    }
}
