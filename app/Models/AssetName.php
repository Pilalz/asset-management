<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AssetSubClass;

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
        'lva'
    ];

    public function assetSubClass()
    {
        return $this->belongsTo(AssetSubClass::class, 'sub_class_id');
    }
}
