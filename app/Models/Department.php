<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RegisterAsset;
use App\Models\TransferAsset;
use App\Models\Company;
use App\Scopes\CompanyScope;

class Department extends Model
{
    use HasFactory;

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

    public function Assets(): HasMany
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
}
