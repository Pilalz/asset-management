<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Department;
use App\Models\Location;

class RegisterAsset extends Model
{
    use HasFactory;

    protected $table = 'register_assets';

    protected $fillable = [
        'id',
        'department_id',
        'location_id',
        'insured',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
