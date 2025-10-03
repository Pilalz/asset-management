<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DisposalAsset;
use App\Models\Asset;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DetailDisposal extends Model
{
    use HasFactory;
    use LogsActivity;
    
    protected $table = 'detail_disposals';

    protected $fillable = [
        'disposal_asset_id',
        'asset_id',
        'kurs',
        'njab',
    ];

    public function disposalAsset(): BelongsTo
    {
        return $this->belongsTo(DisposalAsset::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function getDisposalNameAttribute()
    {
        return $this->disposalAsset->form_no ?? null;
    }

    public function getAssetNameAttribute()
    {
        return $this->asset->asset_number ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $detailDisposal = $this->disposalAsset->form_no;

                return "Asset has been {$eventName} in the disposal form '{$detailDisposal}'";
            })
            ->useLogName(session('active_company_id'))
            ->logOnly(['disposal_name', 'asset_name', 'kurs', 'njab']);
    }
}
