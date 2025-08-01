<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetSubClass;
use App\Models\AssetClass;
use App\Models\AssetName;

class AssetNameController extends Controller
{
    public function index()
    {
        $assetnames = AssetName::with('assetSubClass')->get();

        return view('asset-name.index', compact('assetnames'));
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
}
