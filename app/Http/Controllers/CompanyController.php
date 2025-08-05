<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CompanyController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $companies = Company::where('owner_id', $userId)->get();

        return view('company.index', compact('companies'));
    }

    public function create()
    {
        return view('company.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi input, owner_id tidak lagi diambil dari form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:companies,code',
        ]);

        $user = Auth::user();

        // 2. Buat perusahaan baru, atur owner_id secara otomatis
        $newCompany = Company::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'owner_id' => $user->id,
        ]);

        // 3. (PALING PENTING) Daftarkan user sebagai anggota perusahaan di tabel pivot
        $newCompany->users()->attach($user->id, ['role' => 'owner']);

        // 4. Atur perusahaan baru sebagai yang aktif
        $user->last_active_company_id = $newCompany->id;
        $user->save();
        Session::put('active_company_id', $newCompany->id);

        return redirect()->route('company.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(Company $company)
    {
        return view('company.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
        ]);

        $dataToUpdate = $validatedData;

        $company->update($dataToUpdate);

        return redirect()->route('company.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('company.index')->with('success', 'Data berhasil dihapus!');
    }

    public function switch(Request $request)
    {
        // 1. Ambil ID perusahaan yang diminta dari form
        $companyId = $request->input('company_id');
        $user = Auth::user();

        // 2. Validasi: Apakah pengguna ini benar-benar anggota dari perusahaan yang diminta?
        if ($user->companies()->where('companies.id', $companyId)->exists()) {
            
            // 3. Jika valid, perbarui sesi dan database
            //    a. Atur perusahaan aktif di sesi untuk saat ini
            Session::put('active_company_id', $companyId);

            //    b. Simpan sebagai preferensi di database untuk login berikutnya
            $user->last_active_company_id = $companyId;
            $user->save();
            
            // 4. Kembalikan pengguna dengan pesan sukses
            return redirect()->back()->with('success', 'Berhasil berganti perusahaan.');
        }

        // 5. Jika tidak valid, kembalikan dengan pesan error
        return redirect()->back()->with('error', 'Akses tidak diizinkan.');
    }
}
