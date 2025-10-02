<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Attachment extends Model
{
    use HasFactory;
    use LogsActivity;

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

    public function getAttachmentNameAttribute()
    {
        return $this->attachable->form_no ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $attachment = $this->attachable->form_no;
                $name = $this->original_filename;
                if($this->attachable_type === "App\Models\RegisterAsset"){
                    return "Attachment '{$name}' has been {$eventName} in the register form '{$attachment}'";
                }
                elseif($this->attachable_type === "App\Models\TransferAsset"){
                    return "Attachment '{$name}' has been {$eventName} in the transfer form '{$attachment}'";
                }
                elseif($this->attachable_type === "App\Models\DisposalAsset"){
                   return "Attachment '{$name}' has been {$eventName} in the disposal form '{$attachment}'"; 
                }
            })
            ->useLogName(session('active_company_id'))
            ->logOnly(['file_path', 'original_filename', 'attachment_name', 'attachable_type']);
    }
}
