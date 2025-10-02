<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CompanyUser extends Pivot
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'company_users';

    protected $fillable = [
        'user_id',
        'company_id',
        'role',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::deleting(function (CompanyUser $companyUser) {
            
            $user = $companyUser->user;

            if ($user) {

                if ($user->last_active_company_id === $companyUser->company_id) {

                    $otherCompany = CompanyUser::where('user_id', $user->id)
                        ->where('company_id', '!=', $companyUser->company_id)
                        ->first();

                    if ($otherCompany) {
                        $newActiveCompanyId = $otherCompany->company_id;
                    } else {
                        $newActiveCompanyId = null;
                    }

                    $user->update(['last_active_company_id' => $newActiveCompanyId]);
                } else {
                    Log::info('[DEBUG] KONDISI TIDAK TERPENUHI: Company yang dihapus BUKAN company aktif. Tidak ada aksi.');
                }
            }
        });

        static::created(function (CompanyUser $companyUser) {
            $user = User::find($companyUser->user_id);

            if ($user && $user->last_active_company_id === null) {
                $user->update(['last_active_company_id' => $companyUser->company_id]);
            }
        });
    }

    public function getUserNameAttribute()
    {
        return $this->user->name ?? null;
    }

    public function getCompanyNameAttribute()
    {
        return $this->company->name ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(function(string $eventName) {
                $userName = $this->user->name;
                $company = $this->company->name;

                return "User '{$userName}' has been {$eventName} to {$company}";
            })
            ->useLogName(session('active_company_id'))
            ->logExcept(['status'])
            ->logOnly(['user_name', 'company_name', 'role']);
    }
}
