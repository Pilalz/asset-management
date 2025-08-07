<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetClass;
use App\Models\AssetSubClass;

class AssetSubClassController extends Controller
{
    public function index()
    {
        $assetsubclasses = AssetSubClass::with('assetClass')->paginate(25);

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
}
