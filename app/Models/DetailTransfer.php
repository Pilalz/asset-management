<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TransferAsset;
use App\Models\Asset;

class DetailTransfer extends Model
{
    use HasFactory;

    protected $table = 'detail_transfers';

    protected $fillable = [
        'transfer_asset_id',
        'asset_id',
        'origin_loc_id',
        'destination_loc_id',
    ];

    public function transferAsset(): BelongsTo
    {
        return $this->belongsTo(TransferAsset::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
