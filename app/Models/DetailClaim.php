<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Claim;
use App\Models\Asset;

class DetailClaim extends Model
{
    use HasFactory;

    protected $table = 'detail_claims';

    protected $fillable = [
        'claim_id',
        'asset_id',
        'compensation',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
