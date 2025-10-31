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
use Illuminate\Validation\Rule;

class AssetNameController extends Controller
{
    public function index()
    {
        $companyId = session('active_company_id');

        $assetSubClassesForFilter = AssetSubClass::withoutGlobalScope(CompanyScope::class)
                                     ->where('company_id', $companyId)
                                     ->orderBy('name', 'asc')
                                     ->get(['id', 'name']);

        return view('asset-name.index', [
            'assetSubClassesForFilter' => $assetSubClassesForFilter,
        ]);
    }

    public function create()
    {
        Gate::authorize('is-admin');

        $assetsubclasses = AssetSubClass::with('assetClass')->get();

        return view('asset-name.create', compact('assetsubclasses'));
    }

    public function store(Request $request)
    {
        $companyId = $request->input('company_id');

        $request->validate([
            'sub_class_id' => [
                'required',
                Rule::exists('asset_sub_classes', 'id')->where('company_id', $companyId)
            ],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('asset_names')->where('company_id', $companyId)
            ],
            'grouping' => [
                'required', 'string', 'max:255',
                Rule::unique('asset_names')->where('company_id', $companyId)
            ],
            'commercial'  => 'required',
            'fiscal'  => 'required',
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
        $companyId = $asset_name->company_id;
        
        $validatedData = $request->validate([
            'sub_class_id' => [
                'required',
                Rule::exists('asset_sub_classes', 'id')->where('company_id', $companyId)
            ],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('asset_names')->ignore($asset_name->id)->where('company_id', $companyId)
            ],
            'grouping' => [
                'required', 'string', 'max:255',
                Rule::unique('asset_names')->ignore($asset_name->id)->where('company_id', $companyId)
            ],
            'commercial'  => 'required',
            'fiscal'  => 'required',
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
