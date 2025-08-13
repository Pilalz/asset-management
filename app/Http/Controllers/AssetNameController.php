<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetSubClass;
use App\Models\AssetClass;
use App\Models\AssetName;
use App\Imports\AssetNamesImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AssetNameController extends Controller
{
    public function index()
    {
        // $assetnames = AssetName::with('assetSubClass')->get();

        // return view('asset-name.index', compact('assetnames'));
        return view('asset-name.index');
    }

    public function create()
    {
        $assetsubclasses = AssetSubClass::with('assetClass')->get();

        return view('asset-name.create', compact('assetsubclasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_class_id' => 'required|string|max:255',
            'name'  => 'required|string|max:255',
            'code'  => 'required|string|max:255',
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
        $assetsubclasses = AssetSubClass::with('assetClass')->get();

        return view('asset-name.edit', compact('asset_name', 'assetsubclasses'));
    }

    public function update(Request $request, AssetName $asset_name)
    {
        $validatedData = $request->validate([
            'sub_class_id' => 'required|string|max:255',
            'name'  => 'required|string|max:255',
            'code'  => 'required|string|max:255',
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

    public function showImportForm()
    {
        return view('asset-name.import');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new AssetNamesImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('asset-name.import.form')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('asset-name.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = AssetName::with('assetSubClass')
                            ->where('asset_names.company_id', $companyId)
                            ->select('asset_names.*');

                            dd($query->get());
        return DataTables::of($query)
            ->addColumn('action', function ($asset_name) {
                // Buat tombol action secara dinamis
                $editUrl = route('asset-name.edit', $asset_name->id);
                $deleteUrl = route('asset-name.destroy', $asset_name->id);
                
                return '<a href="' . $editUrl . '" class="btn btn-primary btn-sm">Edit</a> ' .
                       '<form action="' . $deleteUrl . '" method="POST" style="display:inline;">' .
                       csrf_field() . method_field("DELETE") .
                       '<button type="submit" class="btn btn-danger btn-sm">Delete</button></form>';
            })
            ->addColumn('asset_sub_class_name', function ($asset_name) {
                return $asset_name->assetSubClass->name ?? 'N/A';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
