<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Company;
use App\Models\Depreciation;
use App\Models\DetailDisposal;
use App\Models\DetailTransfer;
use App\Models\Insurance;
use App\Models\Claim;
use App\Models\AssetLocationHistory;
use App\Models\AssetUserHistory;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'asset_number',
        'asset_code',
        'asset_name_id',
        'asset_type',
        'status',
        'description',
        'detail',
        'pareto',
        'unit_no',
        'user',
        'sn',
        'sn_chassis',
        'sn_engine',
        'production_year',
        'po_no',
        'location_id',
        'department_id',
        'quantity',
        'capitalized_date',
        'start_depre_date',
        'acquisition_value',
        'current_cost',
        'commercial_useful_life_month',
        'commercial_accum_depre',
        'commercial_nbv',
        'fiscal_useful_life_month',
        'fiscal_accum_depre',
        'fiscal_nbv',
        'remaks',
        'company_id',
    ];

    protected $casts = [
        'capitalized_date' => 'datetime',
        'start_depre_date' => 'datetime',
    ];

    public function assetName(): BelongsTo
    {
        return $this->belongsTo(AssetName::class, 'asset_name_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(Depreciation::class, 'asset_id', 'id');
    }

    public function detailDisposals(): HasMany
    {
        return $this->hasMany(DetailDisposal::class, 'asset_id', 'id');
    }

    public function detailTransfers(): HasMany
    {
        return $this->hasMany(DetailTransfer::class, 'asset_id', 'id');
    }

    public function detailInsurances(): BelongsToMany
    {
        return $this->belongsToMany(Insurance::class, 'detail_insurances');
    }

    public function detailClaims(): BelongsToMany
    {
        return $this->belongsToMany(Claim::class, 'detail_claims')
                    ->withPivot('compensation');
    }

    public function locationHistories() {
        return $this->hasMany(AssetLocationHistory::class)->orderBy('start_date', 'desc');
    }

    public function userHistories() {
        return $this->hasMany(AssetUserHistory::class)->orderBy('start_date', 'desc');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (empty($model->asset_code)) {
                $model->asset_code = (string) Str::uuid();
            }
        });
    }

    public function getAssetNameNameAttribute()
    {
        return $this->assetName->name ?? null;
    }

    public function getLocationNameAttribute()
    {
        return $this->location->name ?? null;
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department->name ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $assetNumber = $this->asset_number;

                return "Asset '{$assetNumber}' has been {$eventName}";
            })
            ->useLogName($this->company_id ?? session('active_company_id'))
            ->logFillable();
    }
}
