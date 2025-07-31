<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetClass;
use App\Models\AssetName;

class AssetSubClass extends Model
{
    use HasFactory;

    protected $table = 'asset_sub_classes';

    protected $fillable = [
        'class_id',
        'name',
    ];

    public function assetClass()
    {
        return $this->belongsTo(AssetClass::class, 'class_id');
    }

    public function assetName()
    {
        return $this->hasMany(AssetName::class, 'sub_class_id', 'id');
    }
}
