<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AssetSubClass;
use App\Models\Company;
use App\Scopes\CompanyScope;

class AssetClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'obj_id',
        'obj_acc',
        'company_id',
    ];

    public function subClasses(): HasMany
    {
        return $this->hasMany(AssetSubClass::class, 'class_id', 'id');
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
