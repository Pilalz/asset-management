<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RegisterAsset;
use App\Models\TransferAsset;
use App\Models\Company;
use App\Scopes\CompanyScope;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    public function registerAssets(): HasMany
    {
        return $this->hasMany(RegisterAsset::class, 'location_id', 'id');
    }

    public function transferredAssets(): HasMany
    {
        return $this->hasMany(TransferAsset::class, 'destination_loc_id', 'id');
    }

    public function Assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id', 'id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }
}
