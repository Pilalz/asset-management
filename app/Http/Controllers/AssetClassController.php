<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetClass;

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
}
