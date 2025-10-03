<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Department;
use App\Models\Location;
use App\Models\Company;
use App\Models\DetailTransfer;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use Illuminate\Database\Eloquent\Model;

class TransferAsset extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'transfer_assets';

    protected $fillable = [
        'submit_date',
        'form_no',
        'department_id',
        'destination_loc_id',
        'reason',
        'sequence',
        'status',
        'company_id',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_loc_id');
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

    public function detailTransfers(): HasMany
    {
        return $this->hasMany(DetailTransfer::class, 'transfer_asset_id', 'id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::deleting(function (TransferAsset $transfer_asset) {
            // Hapus semua relasi anaknya terlebih dahulu
            $transfer_asset->detailTransfers()->delete();
            $transfer_asset->approvals()->delete();

            foreach ($transfer_asset->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $transfer_asset->attachments()->delete();
        });
    }

    public function getDepartmentNameAttribute()
    {
        return $this->department->name ?? null;
    }

    public function getDestinationLocNameAttribute()
    {
        return $this->destinationLocation->name ?? null;
    }

    public function getSequenceNameAttribute()
    {
        return $this->sequence === '1' ? 'Yes' : 'No';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $transferAsset = $this->form_no;

                return "Form Transfer '{$transferAsset}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logExcept(['status'])
            ->logOnly(['submit_date', 'form_no', 'department_name', 'destination_loc_name', 'reason', 'sequence_name']);
    }
}
