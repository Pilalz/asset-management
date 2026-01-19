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
        Gate::authorize('is-admin');

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string',
            'company_id' => 'required'
        ]);

        CompanyUser::create($request->all());

        return redirect()->route('company-user.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(CompanyUser $company_user)
    {
        Gate::authorize('is-admin');
        
        return view('company-user.edit', compact('company_user'));
    }

    public function update(Request $request, CompanyUser $company_user)
    {
        Gate::authorize('is-admin');

        $validatedData = $request->validate([
            'role' => 'required|string',
        ]);

        $dataToUpdate = $validatedData;

        $company_user->update($dataToUpdate);

        return redirect()->route('company-user.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(CompanyUser $company_user)
    {
        Gate::authorize('is-admin');
        
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

    public function search(Request $request)
    {
        $companyId = session('active_company_id');
        $searchTerm = $request->query('q', '');

        // Hanya cari jika ada input dan panjangnya minimal 3 karakter
        if (!$companyId || strlen($searchTerm) < 3) {
            return response()->json([]);
        }

        $users = User::query()
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%");
            })
            ->whereDoesntHave('companies', function($query) use ($companyId) {
                $query->where('companies.id', $companyId);
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
