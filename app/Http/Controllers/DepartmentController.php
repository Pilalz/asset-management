<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::paginate(25);
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
            'description' => 'max:255',
            'company_id'  => 'required',
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

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Department::withoutGlobalScope(CompanyScope::class)
                          ->select('departments.*');

        $query->where('departments.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($department) {
                return view('components.action-buttons', [
                    'editUrl' => route('department.edit', $department->id),
                    'deleteUrl' => route('department.destroy', $department->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
