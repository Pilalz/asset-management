<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Location;
use App\Models\Department;
use App\Models\AssetClass;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;

class AssetArrivalController extends Controller
{
    public function index()
    {
        return view('asset.arrival.index');
    }

    public function edit(Asset $assetArrival)
    {      
        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        return view('asset.arrival.edit', [
            'asset' => $assetArrival,
            'locations' => $locations,
            'departments' => $departments,
            'assetclasses' => $assetclasses
        ]);
    }

    public function update(Request $request, Asset $assetArrival)
    {
        $validatedData = $request->validate([
            'asset_number' => 'unique:assets,asset_number',
            'asset_name_id' => 'exists:asset_names,id',
            'status' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'detail'  => 'max:255',
            'pareto'  => 'max:255',
            'unit_no'  => 'max:255',
            'sn_chassis'  => 'max:255',
            'sn_engine'  => 'max:255',
            'po_no'  => 'required|string|max:255',
            'location_id'  => 'required|exists:locations,id',
            'department_id'  => 'required|exists:departments,id',
            'quantity'  => 'required',
            'capitalized_date'  => 'required|date',
            'start_depre_date'  => 'nullable|date',
            'acquisition_value'  => 'required',
        ]);

        if ($assetArrival->asset_type === 'LVA') {
            $validatedData['start_depre_date'] = null;
        }

        $validatedData['status'] = 'Active';
        $validatedData['current_cost'] = $validatedData['acquisition_value'];
        $validatedData['commercial_nbv'] = $validatedData['acquisition_value'];
        $validatedData['fiscal_nbv'] = $validatedData['acquisition_value'];

        $assetArrival->update($validatedData);

        return redirect()->route('asset.low-value.index')->with('success', 'Data berhasil diperbarui!');
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
                        ->leftJoin('detail_registers', 'assets.po_no', '=', 'detail_registers.po_no')
                        ->leftJoin('register_assets', 'detail_registers.register_asset_id', '=', 'register_assets.id')
                        ->where('register_assets.status', '=', 'Approved')
                        ->where('assets.status', '=', 'Onboard')
                        ->where('assets.company_id', $companyId)
                        ->select([
                            'assets.*',
                            'asset_names.name as asset_name_name',
                            'asset_classes.obj_acc as asset_class_obj',
                            'locations.name as location_name',
                            'departments.name as department_name',
                            'register_assets.form_no as registration_form_no'
                        ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($asset) {
                return view('components.action-arrival-asset-buttons', [
                    'editUrl' => route('assetArrival.edit', $asset->id),
                    'deleteUrl' => route('asset.destroy', $asset->id)
                ])->render();
            })
            ->filterColumn('registration_form_no', function($query, $keyword) {
                $query->where('register_assets.form_no', 'like', "%{$keyword}%");
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
