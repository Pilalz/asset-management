<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Company;
use App\Models\CompanyUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Scopes\UserCompanyScope;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'password',
        'avatar',
        'last_active_company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ownedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'owner_id', 'id');
    }

    public function companies(): BelongsToMany
    {
        // Relasi many-to-many: satu user bisa tergabung dalam banyak perusahaan
        // Melalui tabel pivot 'company_users'
        return $this->belongsToMany(Company::class, 'company_users');
    }

    // Relasi ke company yang menjadi last_active_company
    public function lastActiveCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'last_active_company_id');
    }

    protected function role(): Attribute
    {
        return Attribute::make(
            get: function () {
                $companyUser = CompanyUser::where('user_id', $this->id)
                    ->where('company_id', $this->last_active_company_id)
                    ->first();
                
                return $companyUser?->role; // Mengembalikan nama role (string) atau null
            }
        );
    }
}
