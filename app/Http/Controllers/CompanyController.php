<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Asset;
use App\Models\User;
use App\Models\TransferAsset;
use App\Models\RegisterAsset;
use App\Models\DisposalAsset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
            'currency' => 'required',
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

        $companyUsers = $company->users()
            ->where('role', '!=', 'Owner')
            ->orderBy('name', 'asc')
            ->get();

        return view('company.edit', compact('company', 'countAsset', 'companyUsers'));
    }

    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'required|string|unique:companies,alias,' . $company->id,
            'code' => 'required|string|unique:companies,code,' . $company->id,
            'currency' => 'required',
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

        $cacheKey = 'company_' . $company->id;
        Cache::forget($cacheKey);

        return redirect()->route('company.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Request $request, Company $company)
    {
        if (!Gate::any(['is-dev', 'is-owner'])) {
            abort(403);
        }

        $request->validate([
            'delete_company' => 'required',
        ]);

        if ($request->delete_company !== $company->name) {
            return back()->with('error', 'Nama perusahaan tidak cocok.');
        }

        $companyId = $company->id;

        if ($company->assets()->whereNotIn('status', ['Sold', 'Disposal'])->exists()) {
            return back()->with('error', "Gagal! Perusahaan '{$company->name}' masih memiliki aset active. Harap bereskan aset terlebih dahulu.");
        }

        $hasPending = TransferAsset::where('company_id', $companyId)->where('status', 'Waiting')->exists() ||
                    RegisterAsset::where('company_id', $companyId)->where('status', 'Waiting')->exists() ||
                    DisposalAsset::where('company_id', $companyId)->where('status', 'Waiting')->exists();

        if ($hasPending) {
            return back()->with('error', "Gagal! Masih ada transaksi (Transfer/Register/Disposal) yang berstatus 'Waiting'.");
        }

        $jobStatus = Cache::get('depreciation_status_' . $companyId);
        if ($jobStatus && $jobStatus['status'] === 'running') {
            return back()->with('error', "Gagal! Proses hitung depresiasi sedang berjalan untuk perusahaan ini.");
        }

        DB::transaction(function () use ($company) {
            User::where('last_active_company_id', $company->id)
                ->update(['last_active_company_id' => null]);

            $company->users()->detach();

            $company->delete();
        });

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

    public function transfer(Request $request, Company $company)
    {
        $this->authorize('update', $company);

        $request->validate([
            'new_owner_id' => 'required|exists:users,id',
            'password'     => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Password konfirmasi salah.');
        }
        
        $isMember = $company->users()->where('users.id', $request->new_owner_id)->exists();
        if (!$isMember) {
            return back()->with('error', 'User tersebut bukan anggota perusahaan ini.');
        }

        DB::transaction(function () use ($company, $user, $request) {
            $newOwnerId = $request->new_owner_id;

            $company->update(['owner_id' => $newOwnerId]);

            $company->users()->updateExistingPivot($user->id, ['role' => 'Asset Management']);
            $company->users()->updateExistingPivot($newOwnerId, ['role' => 'Owner']);
        });

        return redirect()->route('company.index')->with('success', 'Kepemilikan perusahaan berhasil dipindahkan.');
    }
}
