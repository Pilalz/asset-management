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
use Illuminate\Validation\Rule;

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
        Gate::authorize('is-admin');

        $companyId = $request->input('company_id');

        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                // Buat 'name' unik HANYA di dalam perusahaan ini
                Rule::unique('departments')->where('company_id', $companyId)
            ],
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
        Gate::authorize('is-admin');

        $companyId = $department->company_id;

        $validatedData = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('departments')->ignore($department->id)->where('company_id', $companyId)
            ],
            'description' => 'max:255'
        ]);

        $dataToUpdate = $validatedData;

        $department->update($dataToUpdate);

        return redirect()->route('department.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Department $department)
    {
        Gate::authorize('is-admin');
        
        if ($department->registerAsset()->where('register_assets.status', '!=', 'Approved')->exists()) {
            return back()->with('error', 'Gagal dihapus! Department ini masih digunakan dalam transaksi Register Asset.');
        }

        if ($department->transferAsset()->where('transfer_assets.status', '!=', 'Approved')->exists()) {
            return back()->with('error', 'Gagal dihapus! Department ini sedang dalam proses Transfer Asset.');
        }

        if ($department->disposalAsset()->where('disposal_assets.status', '!=', 'Approved')->exists()) {
            return back()->with('error', 'Gagal dihapus! Department ini sedang dalam proses Disposal Asset.');
        }

        $hasActiveAssets = $department->assets()
            ->whereNotIn('assets.status', ['Sold', 'Disposal'])
            ->exists();

        if ($hasActiveAssets) {
            return back()->with('error', 'Gagal dihapus! Masih ada Aset Active di department ini.');
        }

        $department->delete();

        return redirect()->route('department.index')->with('success', 'Data berhasil dihapus!');
    }

    public function importExcel(Request $request)
    {
        Gate::authorize('is-admin');
        
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:5120',
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
