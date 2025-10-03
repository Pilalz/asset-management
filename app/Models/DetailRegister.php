<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RegisterAsset;
use App\Models\AssetName;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DetailRegister extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'detail_registers';

    protected $fillable = [
        'register_asset_id',
        'po_no',
        'invoice_no',
        'commission_date',
        'specification',
        'asset_name_id',
    ];

    public function registerAsset(): BelongsTo
    {
        return $this->belongsTo(RegisterAsset::class);
    }

    public function assetName(): BelongsTo
    {
        return $this->belongsTo(AssetName::class);
    }

    public function getRegisterNameAttribute()
    {
        return $this->registerAsset->form_no ?? null;
    }

    public function getAssetNameNameAttribute()
    {
        return $this->assetName->name ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $detailRegister = $this->registerAsset->form_no;

                return "Asset has been {$eventName} in the register form '{$detailRegister}'";
            })
            ->useLogName(session('active_company_id'))
            ->logOnly(['register_name', 'po_no', 'invoice_no', 'commission_date', 'specification', 'asset_name_name']);
    }
}
