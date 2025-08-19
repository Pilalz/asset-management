<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferAsset;
use App\Models\Asset;
use App\Models\Location;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use App\Scopes\CompanyScope;

class TransferAssetController extends Controller
{
    public function index()
    {
        $transferassets = TransferAsset::paginate(25);

        return view('transfer-asset.index', compact('transferassets'));
    }

    public function create()
    {
        $locations = Location::all();
        $departments = Department::all();

        $lastTransferAsset = TransferAsset::latest('id')->first();
        $seq = 1;

        if ($lastTransferAsset){
            $lastSeq = (int) substr($lastTransferAsset->form_no, -5);
            $seq = $lastSeq + 1;
        }
        
        $formattedSeq = str_pad($seq, 5, '0', STR_PAD_LEFT);
        $form_no = Auth::user()->lastActiveCompany->code ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('transfer-asset.create', compact('locations', 'departments', 'form_no'));
    }

    public function findAssetByNumber($assetNumber)
    {
        $asset = Asset::withoutGlobalScope(CompanyScope::class)
                    ->with(['assetName', 'location', 'department'])
                    ->where('asset_number', $assetNumber)
                    ->where('company_id', session('active_company_id'))
                    ->first();

        if ($asset) {
            return response()->json($asset);
        }

        return response()->json(['error' => 'Asset not found'], 404);
    }
}
