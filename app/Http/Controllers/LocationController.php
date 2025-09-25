<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\LocationsImport;
use App\Exports\LocationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

class LocationController extends Controller
{
    public function index()
    {
        return view('location.index');
    }

    public function create()
    {
        Gate::authorize('is-admin');
        
        return view('location.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('is-admin');
        
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
        Gate::authorize('is-admin');
        
        return view('location.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        Gate::authorize('is-admin');
        
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
        Gate::authorize('is-admin');
        
        $location->delete();

        return redirect()->route('location.index')->with('success', 'Data berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new LocationsImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('location.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
        
        return redirect()->route('location.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'Locations-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new LocationsExport, $fileName);
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
