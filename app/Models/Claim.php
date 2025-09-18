<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Insurance;
use App\Scopes\CompanyScope;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    protected $fillable = [
        'insurance_id',
        'claim_date',
        'claim_type',
        'description',
    ];

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Insurance::class);
    }

    public function detailClaims(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'detail_claims')
                    ->withPivot('compensation')
                    ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }
}
