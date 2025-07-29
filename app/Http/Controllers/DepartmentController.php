<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('department.index', compact('departments'));
    }

    public function create()
    {
        return view('department.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255'
        ]);

        Department::create($request->all());

        return redirect()->route('department.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(Department $department)
    {
        return view('department.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'max:255'
        ]);

        $dataToUpdate = $validatedData;

        $department->update($dataToUpdate);

        return redirect()->route('department.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('department.index')->with('success', 'Data berhasil dihapus!');
    }
}
