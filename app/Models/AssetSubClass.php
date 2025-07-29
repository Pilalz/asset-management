<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetClass;

class AssetSubClass extends Model
{
    use HasFactory;

    protected $table = 'asset_sub_classes';

    protected $fillable = [
        'class_id',
        'name',
        'commercial',
        'fiscal',
        'cost'
    ];

    public function assetClass()
    {
        return $this->belongsTo(AssetClass::class, 'class_id');
    }
}
