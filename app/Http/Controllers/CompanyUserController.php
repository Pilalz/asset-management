<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Gate;

class CompanyUserController extends Controller
{
    public function index()
    {   
        return view('company-user.index');
    }

    public function create()
    {
        Gate::authorize('is-admin');

        return view('company-user.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|string',
        ]);

        $userToAdd = User::where('email', $validated['email'])->first();
        $activeCompanyId = Session::get('active_company_id');
        
        $isAlreadyMember = CompanyUser::where('user_id', $userToAdd->id)->where('company_id', $activeCompanyId)->exists();

        if ($isAlreadyMember) {
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
        Gate::authorize('is-admin');
        
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

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = CompanyUser::withoutGlobalScope(CompanyScope::class)
                          ->where('role', '!=', 'owner')
                          ->with('user')
                          ->select('company_users.*');

        $query->where('company_users.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('user_name', function (CompanyUser $companyUser) {
                return $companyUser->user?->name ?? 'N/A';
            })
            ->addColumn('user_email', function (CompanyUser $companyUser) {
                return $companyUser->user?->email ?? 'N/A';
            })
            ->addColumn('action', function ($company_user) {
                return view('components.action-buttons', [
                    'editUrl' => route('company-user.edit', $company_user->id),
                    'deleteUrl' => route('company-user.destroy', $company_user->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
