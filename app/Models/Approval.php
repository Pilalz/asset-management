<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RegisterAsset;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'approvals';

    protected $fillable = [
        'register_asset_id',
        'approval_action',
        'role',
        'user_id',
        'status',
        'approval_date',
        'approval_order',
    ];

    public function registerAsset(): BelongsTo
    {
        return $this->belongsTo(RegisterAsset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
