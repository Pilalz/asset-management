<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\TransferAsset;
use App\Models\Location;
use App\Models\Asset;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DetailTransfer extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'detail_transfers';

    protected $fillable = [
        'transfer_asset_id',
        'asset_id',
        'origin_loc_id',
        'destination_loc_id',
    ];

    public function transferAsset(): BelongsTo
    {
        return $this->belongsTo(TransferAsset::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_loc_id');
    }

    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_loc_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $detailTransfer = $this->transferAsset->form_no;

                return "Asset has been {$eventName} in the transfer form '{$detailTransfer}'";
            })
            ->useLogName(session('active_company_id'))
            ->logFillable();
    }
}
