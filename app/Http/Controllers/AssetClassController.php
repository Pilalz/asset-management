<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetClass;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\AssetClassesImport;
use App\Exports\AssetClassesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AssetClassController extends Controller
{
    public function index()
    {
        return view('asset-class.index');
    }

    public function create()
    {
        Gate::authorize('is-admin');

        return view('asset-class.create');
    }

    public function store(Request $request)
    {
        $companyId = $request->input('company_id', session('active_company_id'));

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('asset_classes')->where('company_id', $companyId)
            ],
            'obj_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('asset_classes')->where('company_id', $companyId)
            ],
            'obj_acc' => 'required|string|max:255',
            'company_id' => 'required|string|max:255',
        ]);

        AssetClass::create($request->all());

        return redirect()->route('asset-class.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(AssetClass $asset_class)
    {
        Gate::authorize('is-admin');

        return view('asset-class.edit', compact('asset_class'));
    }

    public function update(Request $request, AssetClass $asset_class)
    {
        $companyId = $asset_class->company_id;

        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('asset_classes')->ignore($asset_class->id)->where('company_id', $companyId)
            ],
            'obj_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('asset_classes')->ignore($asset_class->id)->where('company_id', $companyId)
            ],
            'obj_acc' => 'required|string|max:255',          
        ]);

        $dataToUpdate = $validatedData;

        $asset_class->update($dataToUpdate);

        return redirect()->route('asset-class.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(AssetClass $asset_class)
    {
        $asset_class->delete();

        return redirect()->route('asset-class.index')->with('success', 'Data berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        // 1. Validasi file yang diupload
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            Excel::import(new AssetClassesImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('asset-class.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('asset-class.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'AssetClasses-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new AssetClassesExport, $fileName);
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = AssetClass::withoutGlobalScope(CompanyScope::class)
                          ->select('asset_classes.*');

        $query->where('asset_classes.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($asset_class) {
                return view('components.action-buttons', [
                    'editUrl' => route('asset-class.edit', $asset_class->id),
                    'deleteUrl' => route('asset-class.destroy', $asset_class->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
