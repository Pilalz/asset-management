<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\StockOpnameDetail;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;

class StockOpnameDataController extends Controller
{
    public function getAssetByCode(Request $request, string $assetCode)
    {
        $asset = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('asset_code', $assetCode)
            ->with(['assetName', 'location', 'department', 'company'])
            ->first();

        if (!$asset) {
            return response()->json([
                'success' => false,
                'message' => 'Asset not found in the system!',
            ], 404);
        }

        // Deteksi apakah asset milik company yang berbeda
        $currentCompanyId = (int) $request->query('company_id');
        if ($currentCompanyId !== (int) $asset->company_id) {
            $isWrongCompany = true;
        } else {
            $isWrongCompany = false;
        }

        return response()->json([
            'success' => true,
            'wrong_company' => $isWrongCompany,
            'asset_company' => $asset->company->name ?? 'Unknown Company',
            'asset_company_id' => $asset->company_id,
            'company_id' => $currentCompanyId,
            'asset' => [
                'id' => $asset->id ?? '-',
                'asset_number' => $asset->asset_number ?? '-',
                'asset_name' => $asset->assetName->name ?? '-',
                'description' => $asset->description ?? '-',
                'location' => $asset->location->name ?? '-',
                'department' => $asset->department->name ?? '-',
                'status' => $asset->status ?? '-',
                'user' => $asset->user ?? '-',
            ],
        ]);
    }

    public function foundAsset($mark, $id)
    {
        $soDetail = StockOpnameDetail::where('asset_id', $id)
            ->with(['soSession' => function ($query) {
                    $query->withoutGlobalScopes();
            }])
            ->whereHas('soSession', function ($query) {
                $query->withoutGlobalScopes()->where('status', 'Open');
            })
            ->first();

        $soDetail->update([
            'status' => 'Found',
            'scanned_at' => now(),
            'scanned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'soDetail' => $soDetail,
        ]);
    }
}