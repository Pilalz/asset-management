<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Department;
use App\Models\Location;
use App\Models\Company;
use App\Models\Asset;
use App\Scopes\CompanyScope;

use Illuminate\Database\Eloquent\Model;

class TransferAsset extends Model
{
    use HasFactory;

    protected $table = 'transfer_assets';

    protected $fillable = [
        'submit_date',
        'form_no',
        'department_id',
        'asset_id',
        'origin_loc_id',
        'destination_loc_id',
        'reason',
        'sequence',
        'status',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function ori_location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_loc_id');
    }
    
    public function dest_location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_loc_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::deleting(function (TransferAsset $transfer_asset) {
            // Hapus semua relasi anaknya terlebih dahulu
            $transfer_asset->detailRegisters()->delete();
            $transfer_asset->approvals()->delete();
        });
    }
}
