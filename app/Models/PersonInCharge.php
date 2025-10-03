<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\CompanyScope;
use App\Models\Company;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PersonInCharge extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'person_in_charges';

    protected $fillable = [
        'name',
        'position',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $PIC = $this->name;

                return "PIC '{$PIC}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logOnly(['name', 'position']);
    }
}
