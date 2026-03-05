<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Pastikan ada company yang aktif di config atau sesi
        $companyId = config('app.active_company_id') ?? Session::get('active_company_id');

        if ($companyId) {
            $builder->where($model->getTable() . '.company_id', $companyId);
        } else {
            // Jika tidak ada company aktif, dan user sudah login,
            // mungkin kita bisa defaultkan ke company pertama user, atau redirect
            // Untuk saat ini, jika tidak ada company aktif, tidak akan ada data yang muncul
            // Ini bisa diubah sesuai kebutuhan (misal, hanya tampilkan data jika company_id match dengan user's first company)
            $builder->whereRaw('1 = 0');
        }
    }
}