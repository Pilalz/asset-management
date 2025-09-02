<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Company;
use App\Models\Depreciation;
use App\Models\DetailDisposal;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_number',
        'asset_name_id',
        'asset_type',
        'status',
        'description',
        'detail',
        'pareto',
        'unit_no',
        'sn_chassis',
        'sn_engine',
        'po_no',
        'location_id',
        'department_id',
        'quantity',
        'capitalized_date',
        'start_depre_date',
        'acquisition_value',
        'current_cost',
        'useful_life_month',
        'accum_depre',
        'net_book_value',
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

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }
}
