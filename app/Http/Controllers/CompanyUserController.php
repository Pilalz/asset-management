<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class CompanyUserController extends Controller
{
    public function index()
    {
        $activeCompanyId = Session::get('active_company_id');

        $companyusers = CompanyUser::where('company_id', $activeCompanyId)->where('role', '!=', 'owner')->with('user')->get();
        
        return view('company-user.index', compact('companyusers'));
    }

    public function create()
    {
        return view('company-user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|string',
        ]);

        // 2. Ambil data User berdasarkan email yang tervalidasi
        $userToAdd = User::where('email', $validated['email'])->first();
        $activeCompanyId = Session::get('active_company_id');
        
        $isAlreadyMember = CompanyUser::where('user_id', $userToAdd->id)->where('company_id', $activeCompanyId)->exists();

        if ($isAlreadyMember) {
            // Jika sudah jadi anggota, kembalikan dengan pesan error
            return redirect()->back()->withInput()->with('error', 'User ini sudah menjadi anggota perusahaan.');
        }

        CompanyUser::create([
            'user_id' => $userToAdd->id,
            'company_id' => $activeCompanyId,
            'role' => $validated['role'],
        ]);

        return redirect()->route('company-user.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(CompanyUser $company_user)
    {
        return view('company-user.edit', compact('company_user'));
    }

    public function update(Request $request, CompanyUser $company_user)
    {
        $validatedData = $request->validate([
            'role' => 'required|string',
        ]);

        $dataToUpdate = $validatedData;

        $company_user->update($dataToUpdate);

        return redirect()->route('company-user.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(CompanyUser $company_user)
    {
        $company_user->delete();

        return redirect()->route('company-user.index')->with('success', 'Data berhasil dihapus!');
    }
}
