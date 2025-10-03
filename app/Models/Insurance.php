<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Company;
use App\Models\Claim;
use App\Models\Asset;
use App\Scopes\CompanyScope;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Insurance extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'insurances';

    protected $fillable = [
        'polish_no',
        'start_date',
        'end_date',
        'instance_name',
        'annual_premium',
        'schedule',
        'next_payment',
        'status',
        'company_id',
    ];

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function detailInsurances(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'detail_insurances');
    }

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
                $insurance = $this->polish_no;

                return "Insurance '{$insurance}' has been {$eventName}";
            })
            ->useLogName(session('active_company_id'))
            ->logOnly(['polish_no', 'start_date', 'end_date', 'instance_name', 'annual_premium', 'status']);
    }
}
