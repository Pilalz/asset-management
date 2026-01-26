<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AssetSubClass;
use App\Models\Company;
use App\Models\RegisterAsset;
use App\Models\Asset;
use App\Scopes\CompanyScope;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssetName extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'asset_names';

    protected $fillable = [
        'sub_class_id',
        'name',
        'grouping',
        'commercial',
        'fiscal',
        'company_id',
    ];

    public function assetSubClass(): BelongsTo
    {
        return $this->belongsTo(AssetSubClass::class, 'sub_class_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'asset_name_id', 'id');
    }

    public function detailRegisters(): HasMany
    {
        return $this->hasMany(RegisterAsset::class, 'asset_name_id', 'id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function getSubClassNameAttribute()
    {
        return $this->assetSubClass->name ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $assetName = $this->name;

                return "Asset Name '{$assetName}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logOnly(['sub_class_name', 'name', 'grouping', 'commercial', 'fiscal']);
    }
}
