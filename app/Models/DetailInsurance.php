<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Insurance;
use App\Models\Asset;

class DetailInsurance extends Model
{
    use HasFactory;

    protected $table = 'detail_insurances';

    protected $fillable = [
        'insurance_id',
        'asset_id',
    ];

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
