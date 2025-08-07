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

        $companies = Company::where('owner_id', $userId)->paginate(25);

        return view('company.index', compact('companies'));
    }

    public function create()
    {
        return view('company.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:companies,code',
        ]);

        $user = Auth::user();

        $newCompany = Company::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'owner_id' => $user->id,
        ]);

        $newCompany->users()->attach($user->id, ['role' => 'owner']);

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
        $companyId = $request->input('company_id');
        $user = Auth::user();

        if ($user->companies()->where('companies.id', $companyId)->exists()) {
            
            Session::put('active_company_id', $companyId);

            $user->last_active_company_id = $companyId;
            $user->save();
            
            return redirect()->back()->with('success', 'Berhasil berganti perusahaan.');
        }

        return redirect()->back()->with('error', 'Akses tidak diizinkan.');
    }
}
