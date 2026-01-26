<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RegisterAsset;
use App\Models\TransferAsset;
use App\Models\DisposalAsset;
use App\Models\Company;
use App\Scopes\CompanyScope;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description',
        'company_id',
    ];

    public function registerAsset(): HasMany
    {
        return $this->hasMany(RegisterAsset::class, 'department_id', 'id');
    }

    public function transferAsset(): HasMany
    {
        return $this->hasMany(TransferAsset::class, 'department_id', 'id');
    }

    public function disposalAsset(): HasMany
    {
        return $this->hasMany(DisposalAsset::class, 'department_id', 'id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'department_id', 'id');
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
            ->logOnly(['name', 'description'])
            ->setDescriptionForEvent(function(string $eventName) {
                $department = $this->name;

                return "Department '{$department}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'));
    }
}
