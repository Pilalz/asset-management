<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Department;
use App\Models\Location;
use App\Models\Company;
use App\Models\DetailRegister;
use App\Models\Approval;
use App\Scopes\CompanyScope;

class RegisterAsset extends Model
{
    use HasFactory;

    protected $table = 'register_assets';

    protected $fillable = [
        'form_no',
        'department_id',
        'location_id',
        'insured',
        'sequence',
        'status',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function detailRegisters(): HasMany
    {
        return $this->hasMany(DetailRegister::class, 'register_asset_id', 'id');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::deleting(function (RegisterAsset $register_asset) {
            // Hapus semua relasi anaknya terlebih dahulu
            $register_asset->detailRegisters()->delete();
            $register_asset->approvals()->delete();
        });
    }
}
