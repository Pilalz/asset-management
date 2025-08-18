<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Depreciation;
use App\Imports\AssetsImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AssetController extends Controller
{
    public function index()
    {
        return view('asset.index');
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
        
        return view('asset.show', [
            'pivotedData' => $pivotedData,
            'months' => $months,
            'selectedYear' => $year,
            'asset' =>$asset
        ]);
    }

    public function edit(Asset $asset)
    {
        return view('asset.edit', compact('asset'));
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

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Asset::withoutGlobalScope(CompanyScope::class)
                          ->with(['assetName', 'location', 'department'])
                          ->select('assets.*');

        $query->where('assets.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('asset_name_name', function (Asset $asset) {
                return $asset->assetName?->name ?? 'N/A';
            })
            ->addColumn('asset_class_obj', function (Asset $asset) {
                return $asset->assetName?->assetSubClass?->assetClass?->obj_acc ?? 'N/A';
            })
            ->addColumn('location_name', function (Asset $asset) {
                return $asset->location?->name ?? 'N/A';
            })
            ->addColumn('department_name', function (Asset $asset) {
                return $asset->department?->name ?? 'N/A';
            })
            ->addColumn('action', function ($asset) {
                return view('components.action-asset-buttons', [
                    'showUrl' => route('asset.show', $asset->id),
                    'depreUrl' => route('depreciation.depre', $asset->id),
                    'editUrl' => route('asset.edit', $asset->id),
                    'deleteUrl' => route('asset.destroy', $asset->id)
                ])->render();
            })
            ->filterColumn('asset_name_name', function($query, $keyword) {
                $query->whereHas('assetName', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('asset_class_obj', function($query, $keyword) {
                $query->whereHas('assetName.assetSubClass.assetClass', function($q) use ($keyword) {
                    $q->where('obj_acc', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('location_name', function($query, $keyword) {
                $query->whereHas('location', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
