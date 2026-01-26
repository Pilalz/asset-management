<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company;
use App\Models\Asset;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Depreciation extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'depreciations';

    protected $fillable = [
        'asset_id',
        'type',
        'depre_date',
        'monthly_depre',
        'accumulated_depre',
        'book_value',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function getAssetNameAttribute()
    {
        $asset = Asset::withoutGlobalScope(CompanyScope::class)->find($this->asset_id);
        return $asset ?->asset_number ?? 'unknown asset';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function (string $eventName) {
                
                $asset = Asset::withoutGlobalScope(CompanyScope::class)->find($this->asset_id);
                $assetNumber = $asset->asset_number;

                return "Depreciation Asset '{$assetNumber}' has been {$eventName}";
            })
            ->useLogName($this->company_id ?? session('active_company_id'))
            ->logOnly(['asset_name', 'type', 'depre_date', 'monthly_depre', 'accumulated_depre', 'book_value']);
    }
}
