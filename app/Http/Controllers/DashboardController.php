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
        // ─── Asset Stats: 1 query dengan semua counter sekaligus ─────────────
        $assetStats = Asset::select([
                DB::raw("SUM(CASE WHEN status != 'Sold' AND status != 'Onboard' AND status != 'Disposal' THEN 1 ELSE 0 END) as total_active"),
                DB::raw("SUM(CASE WHEN status != 'Sold' AND status != 'Onboard' AND status != 'Disposal' AND asset_type = 'FA' THEN 1 ELSE 0 END) as total_fa"),
                DB::raw("SUM(CASE WHEN status != 'Sold' AND status != 'Onboard' AND status != 'Disposal' AND asset_type = 'LVA' THEN 1 ELSE 0 END) as total_lva"),
                DB::raw("SUM(CASE WHEN status = 'Onboard' THEN 1 ELSE 0 END) as total_arrival"),
                DB::raw("SUM(CASE WHEN status != 'Sold' AND status != 'Onboard' AND status != 'Disposal' THEN commercial_nbv ELSE 0 END) as total_nbv"),
            ])
            ->where('status', '!=', 'Sold')
            ->where('status', '!=', 'Disposal')
            ->first();

        $totalAsset      = (int) ($assetStats->total_active ?? 0);
        $totalAssetPrice = (float) ($assetStats->total_nbv ?? 0);
        $assetArrival    = (int) ($assetStats->total_arrival ?? 0);
        $assetFixed      = (int) ($assetStats->total_fa ?? 0);
        $assetLVA        = (int) ($assetStats->total_lva ?? 0);

        // ─── Asset by Location (SQL GROUP BY — tidak load seluruh data ke PHP) ──
        $locationRows = Asset::join('locations', 'assets.location_id', '=', 'locations.id')
            ->select('locations.name as location_name', DB::raw('COUNT(assets.id) as asset_count'))
            ->where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->groupBy('locations.id', 'locations.name')
            ->orderByDesc('asset_count')
            ->get();

        $noLocationCount = Asset::whereNull('location_id')
            ->where('status', '!=', 'Sold')
            ->where('status', '!=', 'Onboard')
            ->where('status', '!=', 'Disposal')
            ->count();

        if ($noLocationCount > 0) {
            $locationRows->push((object) ['location_name' => 'No Location', 'asset_count' => $noLocationCount]);
        }

        $assetLocData = [
            'labels' => $locationRows->pluck('location_name')->values()->all(),
            'series' => $locationRows->pluck('asset_count')->map(fn($v) => (int) $v)->values()->all(),
        ];

        // ─── Asset by Category (SQL GROUP BY) ────────────────────────────────
        $classRows = Asset::join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
            ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
            ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
            ->select('asset_classes.name as class_name', DB::raw('COUNT(assets.id) as asset_count'))
            ->where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->groupBy('asset_classes.id', 'asset_classes.name')
            ->orderByDesc('asset_count')
            ->get();

        $assetClassData = [
            'labels' => $classRows->pluck('class_name')->values()->all(),
            'series' => $classRows->pluck('asset_count')->map(fn($v) => (int) $v)->values()->all(),
        ];

        // ─── Asset by Department (SQL GROUP BY — tidak load seluruh data ke PHP) ─
        $deptRows = Asset::join('departments', 'assets.department_id', '=', 'departments.id')
            ->select('departments.name as department_name', DB::raw('COUNT(assets.id) as asset_count'))
            ->where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('asset_count')
            ->get();

        $noDeptCount = Asset::whereNull('department_id')
            ->where('status', '!=', 'Sold')
            ->where('status', '!=', 'Onboard')
            ->where('status', '!=', 'Disposal')
            ->count();

        if ($noDeptCount > 0) {
            $deptRows->push((object) ['department_name' => 'No Department', 'asset_count' => $noDeptCount]);
        }

        $assetDeptData = [
            'labels' => $deptRows->pluck('department_name')->values()->all(),
            'series' => $deptRows->pluck('asset_count')->map(fn($v) => (int) $v)->values()->all(),
        ];

        // ─── Asset Remaks (limit 50 — hanya kolom yang dibutuhkan) ───────────
        $assetRemaks = Asset::where('status', '!=', 'Sold')
            ->where('status', '!=', 'Onboard')
            ->where('status', '!=', 'Disposal')
            ->whereNotNull('remaks')
            ->where('remaks', '!=', '')
            ->select('id', 'asset_number', 'asset_type', 'remaks')
            ->latest('updated_at')
            ->limit(50)
            ->get();

        $assetRemaksCount = Asset::where('status', '!=', 'Sold')
            ->where('status', '!=', 'Onboard')
            ->where('status', '!=', 'Disposal')
            ->whereNotNull('remaks')
            ->where('remaks', '!=', '')
            ->count();

        // ─── Depreciation Trend Chart ─────────────────────────────────────────
        $depreData = Depreciation::join('assets', 'depreciations.asset_id', '=', 'assets.id')
            ->select(
                'depreciations.depre_date',
                DB::raw("SUM(CASE WHEN depreciations.type = 'commercial' THEN depreciations.monthly_depre ELSE 0 END) as commercial_depre_sum"),
                DB::raw("SUM(CASE WHEN depreciations.type = 'fiscal' THEN depreciations.monthly_depre ELSE 0 END) as fiscal_depre_sum"),
                DB::raw("COUNT(CASE WHEN depreciations.type = 'commercial' THEN 1 END) as commercial_asset_count"),
                DB::raw("COUNT(CASE WHEN depreciations.type = 'fiscal' THEN 1 END) as fiscal_asset_count")
            )
            ->where('assets.company_id', session('active_company_id'))
            ->whereNull('assets.deleted_at')
            ->groupBy('depreciations.depre_date')
            ->orderBy('depreciations.depre_date', 'asc')
            ->get();

        $chartLabels = $depreData->pluck('depre_date')->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        });

        $commercialSumData   = $depreData->pluck('commercial_depre_sum');
        $fiscalSumData       = $depreData->pluck('fiscal_depre_sum');
        $commercialCountData = $depreData->pluck('commercial_asset_count');
        $fiscalCountData     = $depreData->pluck('fiscal_asset_count');

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
