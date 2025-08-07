<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisterAsset;
use App\Models\Location;
use App\Models\Department;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RegisterAssetController extends Controller
{
    public function index()
    {
        $registerassets = RegisterAsset::paginate(25);

        return view('register-asset.index', compact('registerassets'));
    }

    public function create()
    {
        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();
        $assetsubclasses = AssetSubClass::with('assetClass')->get();

        $lastRegisterAsset = RegisterAsset::latest('id')->first();
        $seq = 1;

        if ($lastRegisterAsset){
            $lastSeq = (int) substr($lastRegisterAsset->form_no, -5);
            $seq = $lastSeq + 1;
        }
        
        $formattedSeq = str_pad($seq, 5, '0', STR_PAD_LEFT);
        $form_no = Auth::user()->lastActiveCompany->code ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('register-asset.create', compact('locations', 'departments', 'assetclasses', 'assetsubclasses', 'form_no'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_no' => 'required|string|max:255',
            'department_id' => 'required|max:255',
            'location_id'  => 'required',
            'insured'  => 'required',
            'company_id'  => 'required',
        ]);

        $validated['insured'] = ($validated['insured'] == 'Y') ? 1 : 0;

        RegisterAsset::create($validated);

        return redirect()->route('register-asset.index')->with('success', 'Data berhasil ditambah');
    }
}
