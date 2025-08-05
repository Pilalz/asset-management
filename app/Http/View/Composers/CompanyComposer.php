<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Company;

class CompanyComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if (Auth::check()) {
            $activeCompanyId = Session::get('active_company_id');
            $activeCompany = Company::find($activeCompanyId);

            // --- BAGIAN INI YANG PALING PENTING ---
            // Pastikan Anda mengambil SEMUA companies milik user
            $userCompanies = Auth::user()->companies()->get(); 

            $view->with('activeCompany', $activeCompany)
                ->with('userCompanies', $userCompanies);
        }
    }
}