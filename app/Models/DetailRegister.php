<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RegisterAsset;
use App\Models\AssetName;

class DetailRegister extends Model
{
    use HasFactory;

    protected $table = 'detail_registers';

    protected $fillable = [
        'register_asset_id',
        'po_no',
        'invoice_no',
        'commission_date',
        'specification',
        'asset_name_id',
    ];

    public function registerAsset(): BelongsTo
    {
        return $this->belongsTo(RegisterAsset::class);
    }

    public function assetName(): BelongsTo
    {
        return $this->belongsTo(AssetName::class);
    }
}
