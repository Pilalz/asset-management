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
        $assetnames = AssetName::all();

        return view('asset-name.index', compact('assetnames'));
    }

    public function create()
    {
        $assetsubclasses = AssetSubClass::all();
        return view('asset-name.create', compact('assetsubclasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id'  => 'required|string|max:255',
            'name'  => 'required',
        ]);

        AssetSubClass::create($request->all());

        return redirect()->route('asset-name.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(AssetSubClass $asset_sub_class)
    {
        $assetclasses = AssetClass::all();

        return view('asset-name.edit', compact('asset_sub_class', 'assetclasses'));
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

        return redirect()->route('asset-name.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(AssetSubClass $asset_sub_class)
    {
        $asset_sub_class->delete();

        return redirect()->route('asset-name.index')->with('success', 'Data berhasil dihapus!');
    }
}
