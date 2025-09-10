<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path', 
        'original_filename', 
        'attachable_id', 
        'attachable_type'
    ];

    /**
     * Relasi polimorfik ke model induk (RegisterAsset, TransferAsset, dll.)
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
