<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DisposalAsset;
use App\Models\Asset;

class DetailDisposal extends Model
{
    use HasFactory;
    
    protected $table = 'detail_disposals';

    protected $fillable = [
        'disposal_asset_id',
        'asset_id',
        'kurs',
        'njab',
    ];

    public function disposalAsset(): BelongsTo
    {
        return $this->belongsTo(DisposalAsset::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
