<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //Onboarding User
    public function onboard()
    {
        return view('onboarding.onboard');
    }

    public function createCompany()
    {
        return view('onboarding.create');
    }

    public function storeCompany(Request $request)
    {
        // 1. Validasi input, owner_id tidak lagi diambil dari form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:companies,code', // Pastikan kode unik
        ]);

        $user = Auth::user();

        // 2. Buat perusahaan baru, atur owner_id secara otomatis
        $company = Company::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'owner_id' => $user->id,
        ]);

        // 3. (PALING PENTING) Hubungkan user ini ke perusahaan yang baru dibuat
        $user->companies()->attach($company->id, ['role' => 'owner']);

        // 4. Atur perusahaan baru sebagai yang aktif di session
        Session::put('active_company_id', $company->id);

        // Update juga last_active_company_id di database
        $user->last_active_company_id = $company->id;
        $user->save();

        // 5. Arahkan ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Selamat datang! Perusahaan Anda berhasil dibuat.');
    }
}
