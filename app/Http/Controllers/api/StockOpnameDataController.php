<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\StockOpnameSession;
use App\Models\StockOpnameDetail;
use App\Models\CompanyUser;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;

class StockOpnameDataController extends Controller
{
    public function getAssetByCode(Request $request, string $assetCode)
    {
        $soExist = StockOpnameSession::where('status', 'Open')->exists();

        if (!$soExist) {
            return response()->json([
                'success' => false,
                'message' => 'Stock opname is not open!',
            ], 404);
        }

        $asset = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('asset_code', $assetCode)
            ->with([
                'assetName' => fn($q) => $q->withoutGlobalScopes(),
                'company',
                'location' => fn($q) => $q->withoutGlobalScopes(),
                'department' => fn($q) => $q->withoutGlobalScopes(),
            ])
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

        if ($isWrongCompany === true) {
            $userid = auth()->id();

            $userRole = CompanyUser::where('user_id', $userid)
                ->where('company_id', $asset->company_id)
                ->first();

            $allowUser = ['Owner', 'Asset Management'];

            if (!$userRole || !in_array($userRole->role, $allowUser)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This asset has registered in other company, and you are dont have permission to scan this asset!',
                ], 404);
            }
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

    public function foundAsset(Request $request, $id)
    {
        $soDetail = StockOpnameDetail::where('asset_id', $id)
            ->with([
                'soSession' => function ($query) {
                    $query->withoutGlobalScopes();
                }
            ])
            ->whereHas('soSession', function ($query) {
                $query->withoutGlobalScopes()->where('status', 'Open');
            })
            ->first();

        if ($request->query('mark') === 'true') {
            $mark = true;
        } else {
            $mark = false;
        }

        $soDetail->update([
            'status' => 'Found',
            'scanned_at' => now(),
            'scanned_by' => auth()->id(),
            'mark' => $mark,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }
}