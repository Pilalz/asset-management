<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Imports\AssetSubClassesImport;
use Maatwebsite\Excel\Facades\Excel;

class AssetSubClassController extends Controller
{
    public function index()
    {
        $assetsubclasses = AssetSubClass::with('assetClass')->get();

        return view('asset-sub-class.index', compact('assetsubclasses'));
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
            'name'  => 'required',
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
            'name'  => 'required',
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

    public function showImportForm()
    {
        return view('asset-sub-class.import');
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
            return redirect()->route('asset-sub-class.import.form')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('asset-sub-class.index')->with('success', 'Data aset berhasil diimpor!');
    }
}
