<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\AssetSubClassesImport;
use App\Exports\AssetSubClassesExport;
use Maatwebsite\Excel\Facades\Excel;

class AssetSubClassController extends Controller
{
    public function index()
    {
        return view('asset-sub-class.index');
    }

    public function create()
    {
        $assetclasses = AssetClass::all();
        return view('asset-sub-class.create', compact('assetclasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id'  => 'required|string|max:255',
            'company_id'  => 'required',
        ]);

        AssetSubClass::create($request->all());

        return redirect()->route('asset-sub-class.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(AssetSubClass $asset_sub_class)
    {
        $assetclasses = AssetClass::all();

        return view('asset-sub-class.edit', compact('asset_sub_class', 'assetclasses'));
    }

    public function update(Request $request, AssetSubClass $asset_sub_class)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'class_id'  => 'required|string|max:255',
        ]);

        $dataToUpdate = $validatedData;

        $asset_sub_class->update($dataToUpdate);

        return redirect()->route('asset-sub-class.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(AssetSubClass $asset_sub_class)
    {
        $asset_sub_class->delete();

        return redirect()->route('asset-sub-class.index')->with('success', 'Data berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        // 1. Validasi file yang diupload
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // 2. Lakukan proses import
            Excel::import(new AssetSubClassesImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            // Jika terjadi error, kembali dengan pesan error
            return redirect()->route('asset-sub-class.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('asset-sub-class.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'AssetSubClasses-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new AssetSubClassesExport, $fileName);
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = AssetSubClass::withoutGlobalScope(CompanyScope::class)
                          ->with('assetClass')
                          ->select('asset_sub_classes.*');

        $query->where('asset_sub_classes.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('asset_class_name', function (AssetSubClass $assetSubClass) {
                return $assetSubClass->assetClass?->name ?? 'N/A';
            })
            ->addColumn('action', function ($asset_sub_class) {
                return view('components.action-buttons', [
                    'editUrl' => route('asset-sub-class.edit', $asset_sub_class->id),
                    'deleteUrl' => route('asset-sub-class.destroy', $asset_sub_class->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
