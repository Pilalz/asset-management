<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Depreciation;
use App\Models\Location;
use App\Models\Department;
use App\Models\AssetClass;
use App\Models\Company;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\AssetsImport;
use App\Exports\AssetsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index()
    {
        return view('asset.fixed.index');
    }

    public function show(Request $request, Asset $asset)
    {
        $year = $request->input('year', now()->year);
        $startDate = Carbon::create($year, 1, 1)->startOfMonth();
        $endDate = Carbon::create($year, 12, 1)->endOfMonth();

        $schedules = Depreciation::where('asset_id', $asset->id)
            ->whereBetween('depre_date', [$startDate, $endDate])
            ->orderBy('depre_date')
            ->get();

        $pivotedData = [];
        $pivotedData[$asset->id] = [
            'master_data' => $asset,
            'schedule' => []
        ];

        foreach ($schedules as $schedule) {
            $monthKey = Carbon::parse($schedule->depre_date)->format('Y-m');
            $pivotedData[$asset->id]['schedule'][$monthKey] = (object)[
                'monthly_depre' => $schedule->monthly_depre,
                'accumulated_depre' => $schedule->accumulated_depre,
                'book_value' => $schedule->book_value,
            ];
        }

        $months = [];
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($period as $date) {
            $months[$date->format('Y-m')] = $date->format('M-y');
        }
        
        return view('asset.fixed.show', [
            'pivotedData' => $pivotedData,
            'months' => $months,
            'selectedYear' => $year,
            'asset' =>$asset
        ]);
    }

    public function edit(Asset $asset)
    {
        Gate::authorize('is-admin');
        
        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        $asset->load('depreciations');        

        return view('asset.fixed.edit', compact('asset', 'locations', 'departments', 'assetclasses'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validatedData = $request->validate([
            'asset_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets')->ignore($asset->id)->where('company_id', $asset->company_id)
            ],
            'asset_name_id'     => 'required|exists:asset_names,id',
            'description'       => 'required|string|max:255',
            'detail'            => 'max:255',
            'pareto'            => 'max:255',
            'unit_no'           => 'max:255',
            'user'              => 'nullable',
            'sn_chassis'        => 'max:255',
            'sn_engine'         => 'max:255',
            'production_year'   => 'max:255',
            'po_no'             => 'required|string|max:255',
            'location_id'       => 'required|exists:locations,id',
            'department_id'     => 'required|exists:departments,id',
            'quantity'          => 'required',
            'capitalized_date'  => 'required|date',
            'start_depre_date'  => 'required|date',
            'acquisition_value' => 'required',
            'current_cost'      => 'required',
            'commercial_nbv'    => 'required',
            'fiscal_nbv'        => 'required',
            'remaks'            => 'nullable',
        ]);

        $dataToUpdate = $validatedData;

        $asset->update($dataToUpdate);

        return redirect()->route('asset.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new AssetsImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('asset.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
        
        return redirect()->route('asset.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'Fixed-Asset-' . $companyName->name .'-'. now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new AssetsExport, $fileName);
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
                        ->where('assets.asset_type', '=', 'FA')
                        ->where('assets.status', '!=', 'Sold')
                        ->where('assets.status', '!=', 'Onboard')
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
                return view('components.action-asset-buttons', [
                    'showUrl' => route('asset.show', $asset->id),
                    'depreUrl' => route('depreciation.depre', $asset->id),
                    'editUrl' => route('asset.edit', $asset->id),
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
