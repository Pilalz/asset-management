<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use App\Scopes\CompanyScope;

class DashboardController extends Controller
{
    public function index()
    {
        //Asset By Location
        $assets = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', session('active_company_id'))
            ->with('location')
            ->get();

        // Gunakan metode collection untuk mengelompokkan dan menghitung
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
            'series' => $assetCountByLocation->pluck('asset_count')->all(),
        ];

        //Asset By Class
        $assetCountByClass = Asset::withoutGlobalScope(CompanyScope::class)
            ->join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
            ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
            ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
            ->select('asset_classes.name as class_name', DB::raw('count(assets.id) as asset_count'))
            ->where('assets.company_id', session('active_company_id'))
            ->groupBy('asset_classes.name')
            ->orderBy('asset_count', 'desc')
            ->get();

        $assetClassData = [
            'labels' => $assetCountByClass->pluck('class_name')->all(),
            'series' => $assetCountByClass->pluck('asset_count')->all(),
        ];

        //Asset Arrival
        $assetArrival = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'Onboard')
            ->where('assets.company_id', session('active_company_id'))
            ->count();

        //Fixed Asset
        $assetFixed = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'Active')
            ->where('asset_type', 'FA')
            ->where('assets.company_id', session('active_company_id'))
            ->count();

        //Low Value Asset
        $assetLVA = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('status', 'Active')
            ->where('asset_type', 'LVA')
            ->where('assets.company_id', session('active_company_id'))
            ->count();

        return view('index', compact('assetLocData', 'assetClassData', 'assetArrival', 'assetFixed', 'assetLVA'));
    }
}
