<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;

class MainController extends Controller
{
    public function mainLayout()
    {
        $user = Auth::user();

        if (!$user) {
            // Jika user belum login, mungkin redirect atau berikan error
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Company yang sedang aktif
        $lastActiveCompany = null;
        if ($user->last_active_company_id) {
            $lastActiveCompany = Company::find($user->last_active_company_id);
        }

        // Daftar company lain yang dimiliki user, selain yang aktif
        $otherCompanies = Company::where('owner_id', $user->id)
                                ->where('id', '!=', $user->last_active_company_id)
                                ->get();

        return view('layouts.main', [
            'otherCompanies' => $otherCompanies,
        ]);
    }
}
