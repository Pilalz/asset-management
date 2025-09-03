<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CompanyUser extends Pivot
{
    use HasFactory;

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
            Log::info('--- [DEBUG] Event CompanyUser deleting dijalankan ---');
            
            $user = $companyUser->user;

            if ($user) {
                Log::info("[DEBUG] Mengecek User ID: {$user->id}, Company ID yang dihapus: {$companyUser->company_id}");
                Log::info("[DEBUG] Last Active Company ID User saat ini: {$user->last_active_company_id}");

                if ($user->last_active_company_id === $companyUser->company_id) {
                    Log::info('[DEBUG] KONDISI TERPENUHI: Company yang dihapus adalah company aktif.');

                    $otherCompany = CompanyUser::where('user_id', $user->id)
                        ->where('company_id', '!=', $companyUser->company_id)
                        ->first();

                    if ($otherCompany) {
                        $newActiveCompanyId = $otherCompany->company_id;
                        Log::info("[DEBUG] Menemukan company lain. ID baru: {$newActiveCompanyId}");
                    } else {
                        $newActiveCompanyId = null;
                        Log::info("[DEBUG] Tidak menemukan company lain. ID baru akan di-set ke NULL.");
                    }

                    $user->update(['last_active_company_id' => $newActiveCompanyId]);
                    Log::info("[DEBUG] Berhasil mengupdate last_active_company_id menjadi: " . ($newActiveCompanyId ?? 'NULL'));
                } else {
                    Log::info('[DEBUG] KONDISI TIDAK TERPENUHI: Company yang dihapus BUKAN company aktif. Tidak ada aksi.');
                }
            }
            Log::info('--- [DEBUG] Event CompanyUser deleting selesai ---');
        });

        static::created(function (CompanyUser $companyUser) {
            $user = User::find($companyUser->user_id);

            if ($user && $user->last_active_company_id === null) {
                $user->update(['last_active_company_id' => $companyUser->company_id]);
            }
        });
    }
}
