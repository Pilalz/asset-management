<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Depreciation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        //Asset By Location
        $assets = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->with('location')
            ->get();

        $assetCountByLocation = $assets
            ->groupBy(function ($asset) {
                return $asset->location->name ?? 'No Location';
            })
            ->map(function ($group, $locationName) {
                return [
                    'location_name' => $locationName,
                    'asset_count' => $group->count(),
                ];
            })
            ->sortByDesc('asset_count')
            ->values();

        $assetLocData = [
            'labels' => $assetCountByLocation->pluck('location_name')->all(),
            'series' => $assetCountByLocation->pluck('asset_count')->map(fn($v) => (int) $v)->values()->all(),
        ];

        //Asset By Category
        $assetCountByClass = Asset::join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
            ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
            ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
            ->select('asset_classes.name as class_name', DB::raw('count(assets.id) as asset_count'))
            ->where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->groupBy('asset_classes.name')
            ->orderBy('asset_count', 'desc')
            ->get();

        $assetClassData = [
            'labels' => $assetCountByClass->pluck('class_name')->values()->all(),
            'series' => $assetCountByClass->pluck('asset_count')->map(fn($v) => (int) $v)->values()->all(),
        ];

        // Asset By Department
        $assetsdep = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->with('department')
            ->get();

        $assetCountByDepartment = $assetsdep
            ->groupBy(function ($asset) {
                return $asset->department->name ?? 'No Department';
            })
            ->map(function ($group, $departmentName) {
                return [
                    'department_name' => $departmentName,
                    'asset_count' => $group->count(),
                ];
            })
            ->sortByDesc('asset_count')
            ->values();

        $assetDeptData = [
            'labels' => $assetCountByDepartment->pluck('department_name')->all(),
            'series' => $assetCountByDepartment->pluck('asset_count')->map(fn($v) => (int) $v)->values()->all(),
        ];

        // Active assets base query (reusable)
        $activeAssetsQuery = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal');

        // Total Assets
        $totalAsset = (clone $activeAssetsQuery)->count();

        // Total Asset Value
        $totalAssetPrice = (clone $activeAssetsQuery)->sum('commercial_nbv');

        //Asset Arrival
        $assetArrival = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Disposal')
            ->where('assets.status', 'Onboard')
            ->count();

        //Fixed Asset
        $assetFixed = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->where('asset_type', 'FA')
            ->count();

        //Low Value Asset
        $assetLVA = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->where('asset_type', 'LVA')
            ->count();

        //Asset Remaks
        $assetRemaks = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->where('assets.remaks', '!=', null)
            ->get();

        $assetRemaksCount = Asset::where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->where('assets.remaks', '!=', null)
            ->count();

        //Sum Depre by Asset Class
        $depreByClass = Asset::join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
            ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
            ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
            ->select('asset_classes.obj_id', DB::raw('sum(assets.commercial_accum_depre) as commercial_depre_sum'), DB::raw('sum(assets.fiscal_accum_depre) as fiscal_depre_sum'))
            ->where('assets.asset_type', 'FA')
            ->where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->groupBy('asset_classes.obj_id')
            ->get();

        $depreData = Depreciation::join('assets', 'depreciations.asset_id', '=', 'assets.id')
            ->select(
                'depreciations.depre_date',
                DB::raw("SUM(CASE WHEN depreciations.type = 'commercial' THEN depreciations.monthly_depre ELSE 0 END) as commercial_depre_sum"),
                DB::raw("SUM(CASE WHEN depreciations.type = 'fiscal' THEN depreciations.monthly_depre ELSE 0 END) as fiscal_depre_sum"),
                DB::raw("COUNT(CASE WHEN depreciations.type = 'commercial' THEN 1 END) as commercial_asset_count"),
                DB::raw("COUNT(CASE WHEN depreciations.type = 'fiscal' THEN 1 END) as fiscal_asset_count")
            )
            ->where('assets.company_id', session('active_company_id'))
            ->whereNull('assets.deleted_at') //tambahan
            ->groupBy('depreciations.depre_date')
            ->orderBy('depreciations.depre_date', 'asc')
            ->get();

        $chartLabels = $depreData->pluck('depre_date')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        });

        // Buat data Series
        $commercialSumData = $depreData->pluck('commercial_depre_sum');
        $fiscalSumData = $depreData->pluck('fiscal_depre_sum');
        $commercialCountData = $depreData->pluck('commercial_asset_count');
        $fiscalCountData = $depreData->pluck('fiscal_asset_count');

        // Current month depreciated asset count (from last entry in $depreData)
        $currentMonthDepreCount = (int) ($depreData->last()?->commercial_asset_count ?? 0);

        return view('index', compact(
            'assetLocData',
            'assetClassData',
            'assetArrival',
            'assetFixed',
            'assetLVA',
            'assetRemaks',
            'assetRemaksCount',
            'chartLabels',
            'commercialSumData',
            'fiscalSumData',
            'commercialCountData',
            'fiscalCountData',
            'totalAsset',
            'totalAssetPrice',
            'assetDeptData',
            'currentMonthDepreCount'
        ));
    }
}
