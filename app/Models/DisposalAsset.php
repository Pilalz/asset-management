<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Department;
use App\Models\Company;
use App\Models\DetailDisposal;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DisposalAsset extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'disposal_assets';

    protected $fillable = [
        'submit_date',
        'form_no',
        'department_id',
        'reason',
        'nbv',
        'esp',
        'sequence',
        'status',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function detailDisposals(): HasMany
    {
        return $this->hasMany(DetailDisposal::class, 'disposal_asset_id', 'id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        // static::deleting(function (DisposalAsset $disposal_asset) {
        //     // Hapus semua relasi anaknya terlebih dahulu
        //     $disposal_asset->detailDisposals()->delete();
        //     $disposal_asset->approvals()->delete();

        //     foreach ($disposal_asset->attachments as $attachment) {
        //         Storage::disk('public')->delete($attachment->file_path);
        //     }
        //     $disposal_asset->attachments()->delete();
        // });
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department->name ?? null;
    }

    public function getSequenceNameAttribute()
    {
        return $this->sequence == '1' ? 'Yes' : 'No';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $disposalAsset = $this->form_no;

                return "Form Disposal '{$disposalAsset}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logExcept(['status'])
            ->logOnly(['submit_date', 'form_no', 'department_name', 'reason', 'nbv', 'esp', 'sequence_name']);
    }
}
