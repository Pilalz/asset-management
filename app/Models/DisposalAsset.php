<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Department;
use App\Models\Company;
use App\Models\DetailDisposal;
use App\Scopes\CompanyScope;

class DisposalAsset extends Model
{
    use HasFactory;

    protected $table = 'disposal_assets';

    protected $fillable = [
        'submit_date',
        'form_no',
        'department_id',
        'reason',
        'nbv',
        'esp',
        'sequence',
        'status',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function detailDisposals(): HasMany
    {
        return $this->hasMany(DetailDisposal::class, 'disposal_asset_id', 'id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::deleting(function (DisposalAsset $disposal_asset) {
            // Hapus semua relasi anaknya terlebih dahulu
            $disposal_asset->detailDisposals()->delete();
            $disposal_asset->approvals()->delete();
        });
    }
}
