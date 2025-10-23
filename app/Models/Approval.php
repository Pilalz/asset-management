<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\RegisterAsset;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Approval extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'approvals';

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'approval_action',
        'role',
        'user_id',
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
        return $this->belongsTo(User::class, 'user_id');
    }

    //================================

    public function getApprovalNameAttribute()
    {
        return $this->approvable->form_no ?? null;
    }

    public function getUserNameAttribute()
    {
        return $this->user->name ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $approval = $this->approvable->form_no;
                $name = $this->role;
                if($this->approvable_type === "App\Models\RegisterAsset"){
                    return "Approval '{$name}' has been {$eventName} in the register form '{$approval}'";
                }
                elseif($this->approvable_type === "App\Models\TransferAsset"){
                    return "Approval '{$name}' has been {$eventName} in the transfer form '{$approval}'";
                }
                elseif($this->approvable_type === "App\Models\DisposalAsset"){
                   return "Approval '{$name}' has been {$eventName} in the disposal form '{$approval}'"; 
                }
            })
            ->useLogName(session('active_company_id'))
            ->logOnlyDirty()
            ->logOnly(['approvable_type', 'approval_name', 'approval_action', 'role', 'user_name', 'status', 'approval_date', 'approval_order']);
    }
}
