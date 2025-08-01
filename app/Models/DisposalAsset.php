<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Department;

class DisposalAsset extends Model
{
    use HasFactory;

    protected $table = 'transfer_assets';

    protected $fillable = [
        'department_id',
        'reason',
        'nbv',
        'esp',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
