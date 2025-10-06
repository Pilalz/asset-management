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
use Illuminate\Support\Facades\Log;

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
        return $this->asset->asset_number ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                Log::info('Activity Log Check:', [
                    'event' => $eventName,
                    'depreciation_id' => $this->id,
                    'asset_id' => $this->asset_id,
                ]);

                $asset = Asset::find($this->asset_id);
                $Asset = $asset ? $asset->asset_number : 'an unknown asset';

                return "Depreciation Asset '{$Asset}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logOnly(['asset_name', 'type', 'depre_date', 'monthly_depre', 'accumulated_depre', 'book_value']);
    }
}
