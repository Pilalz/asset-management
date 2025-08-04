<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Department;
use App\Models\Location;
use App\Models\Company;
use App\Scopes\CompanyScope;

use Illuminate\Database\Eloquent\Model;

class TransferAsset extends Model
{
    use HasFactory;

    protected $table = 'transfer_assets';

    protected $fillable = [
        'id',
        'department_id',
        'asset_id',
        'destination_loc_id',
        'reason',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_loc_id');
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
