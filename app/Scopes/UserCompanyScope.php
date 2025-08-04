<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class UserCompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // 1. Periksa apakah ada company yang aktif di session
        if ($activeCompanyId = Session::get('active_company_id')) {

            // 2. Terapkan filter menggunakan whereHas pada relasi 'companies'
            $builder->whereHas('companies', function ($query) use ($activeCompanyId) {
                $query->where('companies.id', $activeCompanyId);
            });

        } else {
            // 3. (Keamanan) Jika tidak ada company aktif, jangan tampilkan user manapun
            $builder->whereRaw('1 = 0');
        }
    }
}