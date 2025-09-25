<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonInCharge;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Gate;

class PersonInChargeController extends Controller
{
    public function index()
    {
        return view('person-in-charge.index');
    }

    public function create()
    {
        Gate::authorize('is-admin');

        return view('person-in-charge.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255',
            'company_id'  => 'required',
        ]);

        PersonInCharge::create($request->all());

        return redirect()->route('person-in-charge.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(PersonInCharge $personInCharge)
    {
        Gate::authorize('is-admin');

        return view('person-in-charge.edit', compact('personInCharge'));
    }

    public function update(Request $request, PersonInCharge $personInCharge)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255'
        ]);

        $dataToUpdate = $validatedData;

        $personInCharge->update($dataToUpdate);

        return redirect()->route('person-in-charge.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(PersonInCharge $personInCharge)
    {
        $personInCharge->delete();

        return redirect()->route('person-in-charge.index')->with('success', 'Data berhasil dihapus!');
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = PersonInCharge::withoutGlobalScope(CompanyScope::class)
                          ->select('person_in_charges.*');

        $query->where('person_in_charges.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($person_in_charge) {
                return view('components.action-buttons', [
                    'editUrl' => route('person-in-charge.edit', $person_in_charge->id),
                    'deleteUrl' => route('person-in-charge.destroy', $person_in_charge->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
