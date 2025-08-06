<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\CompanyUser;
use App\Models\AssetClass;
use App\Models\AssetName;
use App\Models\AssetSubClass;
use App\Models\Department;
use App\Models\DisposalAsset;
use App\Models\Location;
use App\Models\RegisterAsset;
use App\Models\TransferAsset;
use App\Models\Depreciation;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'owner_id',
    ];

    //OWNER
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Relasi many-to-many ke User melalui tabel company_users
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_users')->withPivot('role');
    }

    //Asset Class
    public function assetClasses(): HasMany
    {
        return $this->hasMany(AssetClass::class);
    }

    //Asset Sub Class
    public function assetSubClasses(): HasMany
    {
        return $this->hasMany(AssetSubClass::class);
    }

    //Asset Name
    public function assetNames(): HasMany
    {
        return $this->hasMany(AssetName::class);
    }

    //Department
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    //Disposal Asset
    public function disposalAssets(): HasMany
    {
        return $this->hasMany(DisposalAsset::class);
    }

    //Location
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    //Register Asset
    public function registerAssets(): HasMany
    {
        return $this->hasMany(RegisterAsset::class);
    }

    //Transfer Asset
    public function transferAssets(): HasMany
    {
        return $this->hasMany(TransferAsset::class);
    }

    //Asset
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'company_id', 'id');
    }

    //Depreciation
    public function depreciations(): HasMany
    {
        return $this->hasMany(Depreciation::class, 'company_id', 'id');
    }
}