<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Department;
use App\Models\Location;
use App\Models\Company;
use App\Models\DetailRegister;
use App\Models\Approval;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RegisterAsset extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'register_assets';

    protected $fillable = [
        'form_no',
        'department_id',
        'location_id',
        'asset_type',
        'insured',
        'polish_no',
        'sequence',
        'status',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function detailRegisters(): HasMany
    {
        return $this->hasMany(DetailRegister::class, 'register_asset_id', 'id');
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::deleting(function (RegisterAsset $register_asset) {
            // Hapus semua relasi anaknya terlebih dahulu
            $register_asset->detailRegisters()->delete();
            $register_asset->approvals()->delete();

            foreach ($register_asset->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $register_asset->attachments()->delete();
        });
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department->name ?? null;
    }

    public function getLocationNameAttribute()
    {
        return $this->location->name ?? null;
    }

    public function getInsuredNameAttribute()
    {
        return $this->insured == 1 ? 'Yes' : 'No';
    }

    public function getSequenceNameAttribute()
    {
        return $this->sequence == 1 ? 'Yes' : 'No';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $registerAsset = $this->form_no;

                return "Form Register '{$registerAsset}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logExcept(['status'])
            ->logOnly(['form_no', 'department_name', 'location_name', 'asset_type', 'insured_name', 'polish_no', 'sequence_name']);
    }
}
