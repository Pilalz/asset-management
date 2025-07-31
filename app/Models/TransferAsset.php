<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Department;
use App\Models\Location;

use Illuminate\Database\Eloquent\Model;

class TransferAsset extends Model
{
    use HasFactory;

    protected $table = 'transfer_assets';

    protected $fillable = [
        'id',
        'department_id',
        'asset_id',
        'destination_loc_id',
        'reason',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'destination_loc_id');
    }
}
