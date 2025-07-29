<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RegisterAsset;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'name',
        'description',
    ];

    public function registerAsset()
    {
        return $this->hasMany(RegisterAsset::class, 'department_id', 'id');
    }
}
