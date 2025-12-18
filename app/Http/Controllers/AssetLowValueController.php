<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Location;
use App\Models\Department;
use App\Models\AssetClass;
use App\Models\Company;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\LVAImport;
use App\Exports\LVAExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AssetLowValueController extends Controller
{
    public function index()
    {
        return view('asset.low-value.index');
    }

    public function show(Asset $assetLVA)
    {
        $assetLVA->load('location', 'department');
        return view('asset.low-value.show', ['asset' => $assetLVA]);
    }

    public function create()
    {
        Gate::authorize('is-admin'); 

        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();
        
        $activeCompany = Company::find(session('active_company_id'));

        return view('asset.low-value.create', compact('locations', 'departments', 'assetclasses', 'activeCompany'));
    }

    public function store(Request $request)
    {
        $companyId = session('active_company_id');

        $validatedData = $request->validate([
            'asset_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets')->where('company_id', $companyId)
            ],
            'asset_name_id'    => 'required|exists:asset_names,id',
            'description'      => 'required|string|max:255',
            'detail'           => 'nullable|string|max:255',
            'pareto'           => 'nullable|string|max:255',
            'unit_no'          => 'nullable|string|max:255',
            'user'             => 'nullable|string|max:255',
            'sn_chassis'       => 'nullable|string|max:255',
            'sn_engine'        => 'nullable|string|max:255',
            'sn'               => 'nullable|string|max:255',
            'production_year'  => 'nullable|string|max:255',
            'po_no'            => 'required|string|max:255',
            'location_id'      => 'required|exists:locations,id',
            'department_id'    => 'required|exists:departments,id',
            'quantity'         => 'required|integer|min:1',
            
            'status'           => 'required|string', 
            'capitalized_date' => 'required|date',
            
            'acquisition_value' => 'required',
            'current_cost'      => 'required',
            'commercial_nbv'    => 'required',
            'fiscal_nbv'        => 'required',
            'remaks'            => 'nullable|string'
        ]);

        $validatedData['company_id'] = $companyId;
        
        $validatedData['asset_type'] = 'LVA'; 

        $validatedData['commercial_useful_life_month'] = 0;
        $validatedData['fiscal_useful_life_month']     = 0;
        $validatedData['commercial_accum_depre']       = 0;
        $validatedData['fiscal_accum_depre']           = 0;
        
        $validatedData['start_depre_date'] = $validatedData['capitalized_date'];

        Asset::create($validatedData);

        return redirect()->route('assetLVA.index')->with('success', 'Low Value Asset berhasil dibuat!');
    }

    public function edit(Asset $assetLVA)
    {      
        Gate::authorize('is-admin');
        
        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        return view(
            'asset.low-value.edit', 
            [
                'asset' => $assetLVA,
                'locations' => $locations,
                'departments' => $departments,
                'assetclasses' => $assetclasses
                ]
        );
    }

    public function update(Request $request, Asset $assetLVA)
    {
        $validatedData = $request->validate([
            'asset_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets')->ignore($assetLVA->id)->where('company_id', $assetLVA->company_id)
            ],
            'asset_name_id'     => 'required|exists:asset_names,id',
            'description'       => 'required|string|max:255',
            'detail'            => 'max:255',
            'pareto'            => 'max:255',
            'unit_no'           => 'max:255',
            'user'              => 'nullable',
            'sn_chassis'        => 'max:255',
            'sn_engine'         => 'max:255',
            'sn'                => 'max:255',
            'production_year'   => 'max:255',
            'po_no'             => 'required|string|max:255',
            'location_id'       => 'required|exists:locations,id',
            'department_id'     => 'required|exists:departments,id',
            'quantity'          => 'required',
            'capitalized_date'  => 'required|date',
            'acquisition_value' => 'required',
            'current_cost'      => 'required',
            'commercial_nbv'    => 'required',
            'fiscal_nbv'        => 'required',
            'remaks'            => 'nullable'
        ]);

        $dataToUpdate = $validatedData;

        $assetLVA->update($dataToUpdate);

        return redirect()->route('assetLVA.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            Excel::import(new LVAImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('assetLVA.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
        
        return redirect()->route('assetLVA.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'LowValueAsset-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new LVAExport, $fileName);
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Asset::withoutGlobalScope(CompanyScope::class)
                        ->join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
                        ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
                        ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
                        ->join('locations', 'assets.location_id', '=', 'locations.id')
                        ->join('departments', 'assets.department_id', '=', 'departments.id')
                        ->join('companies', 'assets.company_id', '=', 'companies.id')
                        ->where('assets.asset_type', '=', 'LVA')
                        ->where('assets.status', '!=', 'Sold')
                        ->where('assets.status', '!=', 'Onboard')
                        ->where('assets.status', '!=', 'Disposal')
                        ->where('assets.status', '=', 'Active')
                        ->where('assets.company_id', $companyId)
                        ->select([
                            'assets.*',
                            'asset_names.name as asset_name_name',
                            'asset_classes.obj_acc as asset_class_obj',
                            'locations.name as location_name',
                            'departments.name as department_name',
                            'companies.currency as currency_code',
                        ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($asset) {
                return view('components.action-buttons-3-buttons', [
                    'model'     => $asset,
                    'showUrl' => route('assetLVA.show', $asset->id),
                    'editUrl' => route('assetLVA.edit', $asset->id),
                    'deleteUrl' => route('asset.destroy', $asset->id)
                ])->render();
            })
            ->addColumn('currency', function($asset) {
                return $asset->currency_code ?? 'USD';
            })
            ->filterColumn('asset_name_name', function($query, $keyword) {
                $query->where('asset_names.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('asset_class_obj', function($query, $keyword) {
                $query->where('asset_classes.obj_acc', 'like', "%{$keyword}%");
            })
            ->filterColumn('location_name', function($query, $keyword) {
                $query->where('locations.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->where('departments.name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
