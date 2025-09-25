<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\DepartmentsImport;
use App\Exports\DepartmentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::paginate(25);
        return view('department.index', compact('departments'));
    }

    public function create()
    {
        Gate::authorize('is-admin');
        
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
        Gate::authorize('is-admin');
        
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
        Gate::authorize('is-admin');
        
        $department->delete();

        return redirect()->route('department.index')->with('success', 'Data berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new DepartmentsImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('department.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
        
        return redirect()->route('department.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'Departments-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new DepartmentsExport, $fileName);
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
