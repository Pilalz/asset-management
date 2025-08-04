<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'owner_id' => 'required|string|max:255'
        ]);

        Company::create($request->all());

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
            'owner_id' => 'required|string|max:255'
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
}
