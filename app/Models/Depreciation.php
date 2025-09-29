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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $Asset = $this->asset->asset_number;

                return "Depreciation Asset '{$Asset}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logExcept(['commercial_accum_depre', 'fiscal_accum_depre', 'commercial_nbv', 'fiscal_nbv'])
            ->logFillable();
    }
}
