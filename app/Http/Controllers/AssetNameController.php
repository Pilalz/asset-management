<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetSubClass;
use App\Models\AssetClass;
use App\Models\AssetName;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\AssetNamesImport;
use App\Exports\AssetNamesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

class AssetNameController extends Controller
{
    public function index()
    {
        return view('asset-name.index');
    }

    public function create()
    {
        Gate::authorize('is-admin');

        $assetsubclasses = AssetSubClass::with('assetClass')->get();

        return view('asset-name.create', compact('assetsubclasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_class_id' => 'required|string|exists:asset_sub_classes,id',
            'name'  => 'required|string|max:255',
            'grouping'  => 'required|string|max:255',
            'commercial'  => 'required',
            'fiscal'  => 'required',
            'cost'  => 'required',
            'lva'  => 'required',
            'company_id'  => 'required',
        ]);

        AssetName::create($request->all());

        return redirect()->route('asset-name.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(AssetName $asset_name)
    {
        Gate::authorize('is-admin');
        
        $assetsubclasses = AssetSubClass::with('assetClass')->get();

        return view('asset-name.edit', compact('asset_name', 'assetsubclasses'));
    }

    public function update(Request $request, AssetName $asset_name)
    {
        $validatedData = $request->validate([
            'sub_class_id' => 'required|string|exists:asset_sub_classes,id',
            'name'  => 'required|string|max:255',
            'grouping'  => 'required|string|max:255',
            'commercial'  => 'required',
            'fiscal'  => 'required',
            'cost'  => 'required',
            'lva'  => 'required',
        ]);

        $dataToUpdate = $validatedData;

        $asset_name->update($dataToUpdate);

        return redirect()->route('asset-name.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(AssetName $asset_name)
    {
        $asset_name->delete();

        return redirect()->route('asset-name.index')->with('success', 'Data berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new AssetNamesImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('asset-name.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('asset-name.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'AssetName-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new AssetNamesExport, $fileName);
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = AssetName::withoutGlobalScope(CompanyScope::class)
                          ->with('assetSubClass')
                          ->select('asset_names.*');

        $query->where('asset_names.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('asset_sub_class_name', function (AssetName $assetName) {
                return $assetName->assetSubClass?->name ?? 'N/A';
            })
            ->addColumn('action', function ($asset_name) {
                return view('components.action-buttons', [
                    'editUrl' => route('asset-name.edit', $asset_name->id),
                    'deleteUrl' => route('asset-name.destroy', $asset_name->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
