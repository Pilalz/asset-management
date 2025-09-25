<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
            
        $companies = Company::join('company_users', 'companies.id', '=', 'company_users.company_id')
            ->where('user_id', $userId)
            ->get();

        return view('company.index', compact('companies'));
    }

    public function create()
    {
        Gate::authorize('is-dev');

        return view('company.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('is-dev');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'required|string|unique:companies,alias',
            'code' => 'required|string|unique:companies,code',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $validated['owner_id'] = $user->id;
        
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $newCompany = Company::create($validated);

        $newCompany->users()->attach($user->id, ['role' => 'Owner']);

        $user->last_active_company_id = $newCompany->id;
        $user->save();
        Session::put('active_company_id', $newCompany->id);

        return redirect()->route('company.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(Company $company)
    {
        $this->authorize('update', $company);

        $companyId = session('active_company_id');

        $countAsset = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('assets.company_id', $companyId)
            ->where('status', 'Active')
            ->count();

        return view('company.edit', compact('company', 'countAsset'));
    }

    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'required|string|unique:companies,alias,' . $company->id,
            'code' => 'required|string|unique:companies,code,' . $company->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $validatedData['logo'] = $logoPath;
        }

        $company->update($validatedData);

        return redirect()->route('company.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Company $company)
    {
        User::where('last_active_company_id', $company->id)
        ->update(['last_active_company_id' => null]);

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
