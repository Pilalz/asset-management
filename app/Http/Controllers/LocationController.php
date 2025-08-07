<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::paginate(25);
        return view('location.index', compact('locations'));
    }

    public function create()
    {
        return view('location.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255',
            'company_id'  => 'required',
        ]);

        Location::create($request->all());

        return redirect()->route('location.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(Location $location)
    {
        return view('location.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255'
        ]);

        $dataToUpdate = $validatedData;

        $location->update($dataToUpdate);

        return redirect()->route('location.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('location.index')->with('success', 'Data berhasil dihapus!');
    }
}
