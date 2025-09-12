<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisterAsset;
use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;

class InsuredController extends Controller
{
    public function index()
    {
        return view('insured.index');
    }

    public function show(RegisterAsset $register_asset)
    {
        // Eager load relasi untuk efisiensi
        $register_asset->load('department', 'detailRegisters');

        return view('insured.show', compact('register_asset'));
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = RegisterAsset::withoutGlobalScope(CompanyScope::class)
                          ->where('status', 'Approved')
                          ->with(['department'])
                          ->withCount('detailRegisters')
                          ->where('company_id', $companyId);;

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('department_name', function($registerAsset) {
                return $registerAsset->department->name ?? '-';
            })
            ->addColumn('action', function ($register_assets) {
                return view('components.action-show-buttons', [
                    'showUrl' => route('insured.show', $register_assets->id),
                ])->render();
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('department_name', function ($query, $order) {
                $query->orderBy(
                    Department::select('name')
                        ->whereColumn('departments.id', 'register_assets.department_id'),
                    $order
                );
            })
            ->toJson();
    }
}
