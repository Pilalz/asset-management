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
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Contracts\Activity;

class Location extends Model
{
    use HasFactory;
    use LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description'])
            ->setDescriptionForEvent(function(string $eventName) {
                $location = $this->name;

                return "Location '{$location}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'));
    }
}
