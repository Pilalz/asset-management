<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\AssetSubClass;

class AssetClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function subClasses()
    {
        return $this->hasMany(AssetSubClass::class, 'class_id', 'id');
    }
}
