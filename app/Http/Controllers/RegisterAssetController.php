<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisterAsset;
use App\Models\Location;
use App\Models\Asset;
use App\Models\Department;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\Approval;
use App\Models\CompanyUser;
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
        $form_no = Auth::user()->lastActiveCompany->alias ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('register-asset.create', compact('locations', 'departments', 'assetclasses', 'form_no'));
    }

    public function store(Request $request)
    {
        //Store Register Asset
        $validated = $request->validate([
            'form_no' => 'required|string|max:255|unique:register_assets,form_no',
            'department_id' => 'required|exists:departments,id',
            'location_id'  => 'required|exists:locations,id',
            'insured'  => 'required',
            'sequence'  => 'required',
            'company_id'  => 'required|exists:companies,id',

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

        $approvalsToStore = [];
        $isSequence = ($validated['sequence'] === "Y");

        foreach ($validated['approvals'] as $index => $approvalData) {
            $order = 1;
            if ($isSequence) {
                $order = $index + 1;
            }

            // Yang pertama ('Submitted by') otomatis approved
            $isFirstApprover = ($index === 0);

            $approvalsToStore[] = [
                'approval_action'   => $approvalData['approval_action'],
                'role'              => $approvalData['role'],
                'user_id'           => $isFirstApprover ? $approvalData['user_id'] : null,
                'status'            => $isFirstApprover ? 'approved' : 'pending',
                'approval_date'     => $isFirstApprover ? now() : null,
                'approval_order'    => $order,
            ];
        }

        try {
            DB::transaction(function () use ($validated, $approvalsToStore) {
                $registerAsset = RegisterAsset::create([
                    'form_no'       => $validated['form_no'],
                    'department_id' => $validated['department_id'],
                    'location_id'   => $validated['location_id'],
                    'insured'       => ($validated['insured'] == 'Y') ? 1 : 0,
                    'sequence'      => ($validated['sequence'] == 'Y') ? 1 : 0,
                    'status'        => 'Waiting',
                    'company_id'    => $validated['company_id'],
                ]);

                foreach ($validated['assets'] as $assetData) {
                    $registerAsset->detailRegisters()->create($assetData);
                }

                foreach ($approvalsToStore as $approvalData) {
                    $registerAsset->approvals()->create($approvalData);
                }

            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }


        return redirect()->route('register-asset.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(RegisterAsset $register_asset)
    {
        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        $register_asset->load('approvals.user', 'department', 'location', 'detailRegisters.assetName.assetSubClass.assetClass');

        return view('register-asset.edit', compact('register_asset', 'locations', 'departments', 'assetclasses'));
    }

    public function update(Request $request, RegisterAsset $registerAsset)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'location_id'   => 'required|exists:locations,id',
            'insured'       => 'required',
            'sequence'      => 'required',

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

        try {
            DB::transaction(function () use ($validated, $registerAsset) {
                $registerAsset->update([
                    'department_id' => $validated['department_id'],
                    'location_id'   => $validated['location_id'],
                    'insured'       => ($validated['insured'] == 'Y') ? 1 : 0,
                    'sequence'      => ($validated['sequence'] == 'Y') ? 1 : 0,
                ]);

                $registerAsset->detailRegisters()->delete();
                if (isset($validated['assets'])) {
                    foreach ($validated['assets'] as $assetData) {
                        $registerAsset->detailRegisters()->create($assetData);
                    }
                }

                $registerAsset->approvals()->delete();
                $isSequence = ($validated['sequence'] === 'Y');
                foreach ($validated['approvals'] as $index => $approvalData) {
                    $order = $isSequence ? ($index + 1) : 1;

                    $registerAsset->approvals()->create([
                        'approval_action'   => $approvalData['approval_action'],
                        'role'              => $approvalData['role'],
                        'approval_order'    => $order,
                        'status'            => 'pending',
                        'user_id'           => null,
                        'approval_date'     => null,
                    ]);
                }
                
                $firstApprover = $registerAsset->approvals()->orderBy('approval_order', 'asc')->first();
                if ($firstApprover) {
                    $firstApprover->update([
                        'status' => 'approved',
                        'user_id' => Auth::id(),
                        'approval_date' => now()
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('register-asset.index')->with('success', 'Data berhasil di-update');
    }

    public function destroy(RegisterAsset $register_asset)
    {
        try {
            $register_asset->delete();

        } catch (\Exception $e) {
            return redirect()->route('register-asset.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->route('register-asset.index')->with('success', 'Data berhasil dihapus!');
    }

    public function show(RegisterAsset $register_asset)
    {
        // Eager load relasi untuk efisiensi
        $register_asset->load('approvals.user', 'department', 'location', 'detailRegisters.assetName.assetSubClass.assetClass');
        
        $canApprove = false;
        $userApprovalStatus = null;

        if ($register_asset->status === 'Waiting') {
            $user = Auth::user();
            $userApprovalStatus = 'Anda tidak ada dalam daftar approver, atau bukan giliran Anda.';

            if ($user->role) {
                // LOGIKA UNTUK APPROVAL PARALLEL
                if ($register_asset->sequence === "0") {
                    if ($register_asset->approvals()->where('role', $user->role)->where('status', 'pending')->exists()) {
                        $canApprove = true;
                    }
                }

                // LOGIKA UNTUK APPROVAL SEQUENTIAL
                if ($register_asset->sequence === "1") {
                    $nextApprover = $register_asset->approvals()->where('status', 'pending')->orderBy('approval_order', 'asc')->first();
                    if ($nextApprover && $nextApprover->role === $user->role) {
                        $canApprove = true;
                    }
                }

                // Cek apakah user sudah pernah approve
                if ($register_asset->approvals()->where('user_id', $user->id)->exists()) {
                    $canApprove = false; // Override, pastikan tidak bisa approve dua kali
                    $userApprovalStatus = 'Anda sudah menyetujui formulir ini.';
                }
            } else {
                $userApprovalStatus = 'Anda tidak memiliki role di perusahaan ini.';
            }
        }
        return view('register-asset.show', compact('register_asset', 'canApprove', 'userApprovalStatus'));
    }

    public function approve(Request $request, RegisterAsset $register_asset)
    {
        $user = Auth::user();

        // Validasi ulang di backend untuk keamanan
        $nextApprover = $register_asset->approvals()
            ->where('status', 'pending')
            ->orderBy('approval_order', 'asc')
            ->first();

        // Kondisi kapan user TIDAK BOLEH approve
        if (
            ($register_asset->sequence === "1" && (!$nextApprover || $nextApprover->role !== $user->role)) ||
            ($register_asset->sequence === "0" && !$register_asset->approvals()->where('role', $user->role)->where('status', 'pending')->exists())
        ) {
            return back()->with('error', 'Saat ini bukan giliran Anda untuk melakukan approval.');
        }
        
        try {
            DB::transaction(function () use ($register_asset, $user, $request, $nextApprover) {
                // 1. Update baris approval milik user ini
                $approval = $register_asset->approvals()
                    ->where('role', $user->role)
                    ->where('status', 'pending')
                    ->when($register_asset->sequence === 1, function ($query) use ($nextApprover) {
                        return $query->where('approval_order', $nextApprover->approval_order);
                    })
                    ->first();

                if ($approval) {
                    $approval->update([
                        'status' => 'approved',
                        'user_id' => $user->id,
                        'approval_date' => now(),
                    ]);
                } else {
                    throw new \Exception("Approval yang valid tidak ditemukan untuk peran Anda.");
                }

                // 2. Cek apakah semua approval sudah selesai
                $allApproved = $register_asset->approvals()->where('status', '!=', 'approved')->doesntExist();

                if ($allApproved) {
                    $this->finalizeAssetRegistration($register_asset);
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('register-asset.index')->with('success', 'Formulir berhasil disetujui.');
    }

    private function generateAssetNumber($companyId, $assetNameId)
    {
        // 1. Siapkan komponen-komponennya
        $prefix = 'FA';
        $companyCode = str_pad($companyId, 2, '0', STR_PAD_LEFT); // Contoh: 1 -> 01
        $year = now()->format('y'); // yy -> 25
        $assetGrouping = $assetNameId;

        // 2. Buat prefix lengkap untuk pencarian di database
        $searchPrefix = $prefix . $companyCode;

        // 3. Cari aset terakhir dengan prefix yang sama
        $lastAsset = Asset::where('asset_number', 'like', $searchPrefix . '%')
                        ->orderBy('asset_number', 'desc')
                        ->first();

        $sequence = 1; // Mulai dari 1 jika tidak ada data sebelumnya
        if ($lastAsset) {
            // Ambil 5 digit terakhir dari nomor aset, ubah ke integer, lalu tambah 1
            $lastSequence = (int) substr($lastAsset->asset_number, -5);
            $sequence = $lastSequence + 1;
        }

        // 4. Format nomor urut menjadi 5 digit dengan angka nol di depan
        $formattedSequence = str_pad($sequence, 5, '0', STR_PAD_LEFT); // 1 -> 00001

        // 5. Gabungkan semuanya
        return $searchPrefix . $year . $assetGrouping . $formattedSequence;
    }

    private function finalizeAssetRegistration(RegisterAsset $register_asset)
    {
        // Ubah status form menjadi 'approved'
        $register_asset->update(['status' => 'Approved']);

        // Loop melalui detail dan buat aset baru
        foreach ($register_asset->detailRegisters as $detail) {

            $assetNameId = $detail->assetName->grouping;

            $newAssetNumber = $this->generateAssetNumber($register_asset->company_id, $assetNameId);

            Asset::create([
                'asset_number' => $newAssetNumber,
                'asset_name_id' => $detail->asset_name_id,
                'status' => 'Active',
                'description' => $detail->specification,
                'detail' => null,
                'pareto' => null,
                'unit_no' => null,
                'sn_chassis' => null,
                'sn_engine' => null,
                'po_no' => $register_asset->po_no,
                'location_id' => $register_asset->location_id,
                'department_id' => $register_asset->department_id,
                'quantity' => 1,
                'capitalized_date' => now(),
                'start_depre_date' => now(),
                'acquisition_value' => 0,
                'current_cost' => 0,
                'useful_life_month' => $detail->assetName->commercial * 12,
                'accum_depre' => 0,
                'net_book_value' => 0,
                'company_id' => $register_asset->company_id,
            ]);
        }
    }
}
