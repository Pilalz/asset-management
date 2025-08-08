<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisterAsset;
use App\Models\Location;
use App\Models\Department;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegisterAssetController extends Controller
{
    public function index()
    {
        $registerassets = RegisterAsset::withCount('detailRegisters')->paginate(25);

        return view('register-asset.index', compact('registerassets'));
    }

    public function create()
    {
        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        $lastRegisterAsset = RegisterAsset::latest('id')->first();
        $seq = 1;

        if ($lastRegisterAsset){
            $lastSeq = (int) substr($lastRegisterAsset->form_no, -5);
            $seq = $lastSeq + 1;
        }
        
        $formattedSeq = str_pad($seq, 5, '0', STR_PAD_LEFT);
        $form_no = Auth::user()->lastActiveCompany->code ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('register-asset.create', compact('locations', 'departments', 'assetclasses', 'form_no'));
    }

    public function store(Request $request)
    {
        //Store Register Asset
        $validated = $request->validate([
            'form_no' => 'required|string|max:255',
            'department_id' => 'required|max:255',
            'location_id'  => 'required',
            'insured'  => 'required',
            'sequence'  => 'required',
            'company_id'  => 'required',

            //Validasi Detail Asset
            'assets'                    => 'required|array|min:1',
            'assets.*.po_no'            => 'nullable|string|max:255',
            'assets.*.invoice_no'       => 'nullable|string|max:255',
            'assets.*.commission_date'  => 'required|date',
            'assets.*.specification'    => 'required|string',
            'assets.*.asset_name_id'    => 'required|exists:asset_names,id',

            //Validasi Approval
            'approvals'                     => 'required|array|min:1',
            'approvals.*.approval_action'   => 'required|string|max:255',
            'approvals.*.role'              => 'required|string|max:255',
            'approvals.0.user_id'           => 'required|string|max:255',
            'approvals.*.status'            => 'required|string|max:255',
            'approvals.0.approval_date'     => 'required|date',
        ]);

        dd($validated);

        $approvalsToStore = [];
        $isSequence = ($validated['sequence'] === 1);

        foreach ($validated['approvals'] as $index => $approvalData) {
            $order = 1; // Default order untuk non-sekuensial
            if ($isSequence) {
                $order = $index + 1; // Order increment untuk sekuensial
            }

            // Yang pertama ('Submitted by') otomatis approved
            $isFirstApprover = ($index === 0);

            // Tambahkan data ke array baru yang akan disimpan
            $approvalsToStore[] = [
                'approval_action'             => $approvalData['approval_action'],
                'role'     => $approvalData['role'],
                'approval_order'    => $order, // <-- LOGIKA INTI DI SINI
                'status'            => $isFirstApprover ? 'approved' : 'pending',
                'user_id' => $isFirstApprover ? $approvalData['user_id'] : null,
                'approval_date'         => $isFirstApprover ? now() : null,
            ];
        }

        dd($approvalsToStore);

        try {
            DB::transaction(function () use ($validated) {
                $registerAsset = RegisterAsset::create([
                    'form_no'       => $validated['form_no'],
                    'department_id' => $validated['department_id'],
                    'location_id'   => $validated['location_id'],
                    'insured'       => ($validated['insured'] == 'Y') ? 1 : 0,
                    'status'    => 'Waiting',
                    'company_id'    => $validated['company_id'],
                    'sequence'    => ($validated['sequence'] == 'Y') ? 1 : 0,
                ]);

                foreach ($validated['assets'] as $assetData) {
                    $registerAsset->detailRegisters()->create($assetData);
                }

                foreach ($validated['approvals'] as $approvalData) {
                    $registerAsset->approvals()->create($approvalData);
                }

            });
        } catch (\Exception $e) {
            // Jika terjadi error di tengah jalan, redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }


        return redirect()->route('register-asset.index')->with('success', 'Data berhasil ditambah');
    }
}
