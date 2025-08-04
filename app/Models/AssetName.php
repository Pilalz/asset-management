<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\AssetSubClass;
use App\Models\Company;
use App\Scopes\CompanyScope;

class AssetName extends Model
{
    use HasFactory;

    protected $table = 'asset_names';

    protected $fillable = [
        'sub_class_id',
        'name',
        'code',
        'commercial',
        'fiscal',
        'cost',
        'lva',
        'company_id',
    ];

    public function assetSubClass(): BelongsTo
    {
        return $this->belongsTo(AssetSubClass::class, 'sub_class_id');
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
