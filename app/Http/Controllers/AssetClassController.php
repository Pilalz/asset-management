<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetClass;
use App\Imports\AssetClassesImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;

class AssetClassController extends Controller
{
    public function index()
    {
        $assetclasses = AssetClass::paginate(25);
        return view('asset-class.index', compact('assetclasses'));
    }

    public function create()
    {
        return view('asset-class.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'obj_id' => 'required|string|max:255',
            'obj_acc' => 'required|string|max:255',
            'company_id' => 'required|string|max:255',
        ]);

        AssetClass::create($request->all());

        return redirect()->route('asset-class.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(AssetClass $asset_class)
    {
        return view('asset-class.edit', compact('asset_class'));
    }

    public function update(Request $request, AssetClass $asset_class)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'obj_id' => 'required|string|max:255',
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
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new AssetClassesImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('asset-class.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('asset-class.index')->with('success', 'Data aset berhasil diimpor!');
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
