<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;

class LocationController extends Controller
{
    public function index()
    {
        return view('location.index');
    }

    public function create()
    {
        return view('location.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255',
            'company_id'  => 'required',
        ]);

        Location::create($request->all());

        return redirect()->route('location.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(Location $location)
    {
        return view('location.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255'
        ]);

        $dataToUpdate = $validatedData;

        $location->update($dataToUpdate);

        return redirect()->route('location.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('location.index')->with('success', 'Data berhasil dihapus!');
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Location::withoutGlobalScope(CompanyScope::class)
                          ->select('locations.*');

        $query->where('locations.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($location) {
                return view('components.action-buttons', [
                    'editUrl' => route('location.edit', $location->id),
                    'deleteUrl' => route('location.destroy', $location->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
