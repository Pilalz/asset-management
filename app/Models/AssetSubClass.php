<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AssetClass;
use App\Models\AssetName;
use App\Models\Company;
use App\Scopes\CompanyScope;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AssetSubClass extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'asset_sub_classes';

    protected $fillable = [
        'class_id',
        'name',
        'company_id',
    ];

    public function assetClass(): BelongsTo
    {
        return $this->belongsTo(AssetClass::class, 'class_id');
    }

    public function assetNames(): HasMany
    {
        return $this->hasMany(AssetName::class, 'sub_class_id', 'id');
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
            ->setDescriptionForEvent(function(string $eventName) {
                $assetSubClass = $this->name;

                return "Asset Sub Class '{$assetSubClass}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logFillable();
    }
}
