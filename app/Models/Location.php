<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RegisterAsset;
use App\Models\TransferAsset;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'name',
        'description',
    ];

    public function registerAsset()
    {
        return $this->hasMany(RegisterAsset::class, 'location_id', 'id');
    }

    public function transferAsset()
    {
        return $this->hasMany(TransferAsset::class, 'destination_loc_id', 'id');
    }
}
