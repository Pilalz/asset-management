<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Asset;
use App\Models\DetailDisposal;
use App\Models\DetailTransfer;

class AssetIsAvailable implements Rule
{
    protected $conflictingAssets = [];
    protected $formToIgnoreId = null;
    protected $formType = null;

    /**
     * @param string $formType Tipe form yang akan diabaikan (misal: 'disposal' atau 'transfer')
     * @param int $formToIgnoreId ID dari form yang akan diabaikan
     */
    public function __construct($formType = null, $formToIgnoreId = null)
    {
        $this->formType = $formType;
        $this->formToIgnoreId = $formToIgnoreId;
    }

    public function passes($attribute, $value)
    {
        $assetIds = array_keys($value);
        if (empty($assetIds)) {
            return true; 
        }

        // --- Cek 1: Konflik di Form Disposal ---
        $disposalQuery = DetailDisposal::whereIn('asset_id', $assetIds)
            ->whereHas('disposalAsset', function ($q) {
                $q->where('status', 'Waiting');
                
                if ($this->formType === 'disposal' && $this->formToIgnoreId) {
                    $q->where('id', '!=', $this->formToIgnoreId); 
                }
            });

        if ($disposalQuery->exists()) {
            $conflictIds = $disposalQuery->pluck('asset_id')->unique();
            $this->conflictingAssets = Asset::whereIn('id', $conflictIds)->pluck('asset_number');
            return false;
        }

        // --- Cek 2: Konflik di Form Transfer ---
        $transferQuery = DetailTransfer::whereIn('asset_id', $assetIds)
            ->whereHas('transferAsset', function ($q) {
                $q->where('status', 'Waiting');

                if ($this->formType === 'transfer' && $this->formToIgnoreId) {
                    $q->where('id', '!=', $this->formToIgnoreId); 
                }
            });
            
        if ($transferQuery->exists()) {
            $conflictIds = $transferQuery->pluck('asset_id')->unique();
            $this->conflictingAssets = Asset::whereIn('id', $conflictIds)->pluck('asset_number');
            return false;
        }

        return true;
    }

    public function message()
    {
        return 'Aset berikut sudah ada di form lain yang sedang "Waiting": ' . $this->conflictingAssets->join(', ');
    }
}