<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Asset;
use App\Models\User;
use App\Models\Location;
use App\Models\StockOpnameSession;

class StockOpnameDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'so_session_id',
        'asset_id',
        'status',
        'system_location_id',
        'actual_location_id',
        'system_user',
        'actual_user',
        'system_condition',
        'actual_condition',
        'note',
        'attachment_path',
        'scanned_at',
        'scanned_by',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(StockOpnameSession::class, 'so_session_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function systemLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'system_location_id');
    }

    public function actualLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'actual_location_id');
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
