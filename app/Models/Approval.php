<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RegisterAsset;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'approvals';

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'approval_action',
        'role',
        'user_id',
        'signature_image',
        'status',
        'approval_date',
        'approval_order',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
