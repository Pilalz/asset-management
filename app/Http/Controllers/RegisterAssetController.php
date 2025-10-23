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
use App\Models\Attachment;
use App\Models\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Carbon\Carbon;
use App\Scopes\CompanyScope;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;

class RegisterAssetController extends Controller
{
    public function index()
    {        
        return view('register-asset.index');
    }

    public function trash()
    {
        return view('register-asset.canceled');
    }

    public function create()
    {
        Gate::authorize('is-form-maker');

        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();
        $users = User::join('company_users', 'users.id', '=', 'company_users.user_id')
            ->where('company_users.company_id', session('active_company_id'))
            ->select('users.id', 'users.name', 'company_users.role as user_role')
            ->get();

        $lastRegisterAsset = RegisterAsset::withTrashed()->latest('id')->first();
        $seq = 1;

        if ($lastRegisterAsset){
            $lastSeq = (int) substr($lastRegisterAsset->form_no, -5);
            $seq = $lastSeq + 1;
        }
        
        $formattedSeq = str_pad($seq, 5, '0', STR_PAD_LEFT);
        $form_no = Auth::user()->lastActiveCompany->alias ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('register-asset.create', compact('locations', 'departments', 'assetclasses', 'form_no', 'users'));
    }

    public function store(Request $request)
    {
        //Store Register Asset
        $validated = $request->validate([
            'form_no'       => 'required|string|max:255|unique:register_assets,form_no',
            'department_id' => 'required|exists:departments,id',
            'location_id'   => 'required|exists:locations,id',
            'asset_type'    => 'required',
            'insured'       => 'required',
            'polish_no'     => 'required_if:insured,Y|nullable|string|max:255',
            'sequence'      => 'required',
            'company_id'    => 'required|exists:companies,id',

            //Validasi Detail Asset
            'assets'                    => 'required|array|min:1',
            'assets.*.po_no'            => 'nullable|string|max:255',
            'assets.*.invoice_no'       => 'nullable|string|max:255',
            'assets.*.commission_date'  => 'required_if:asset_type,FA|nullable|date',
            'assets.*.specification'    => 'required|string',
            'assets.*.asset_name_id'    => 'required|exists:asset_names,id',

            //Validasi Attachments
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx|max:5120', // Maks 5MB per file

            //Validasi Approval
            'approvals'                     => 'required|array|min:1',
            'approvals.*.approval_action'   => 'required|string|max:255',
            'approvals.*.role'              => 'required|string|max:255',
            'approvals.*.user_id'           => 'required|exists:users,id',
            'approvals.*.status'            => 'required|string|max:255',
            'approvals.0.approval_date'     => 'nullable|date',
        ]);

        $approvalsToStore = [];
        $isSequence = ($validated['sequence'] === "Y");

        foreach ($validated['approvals'] as $index => $approvalData) {
            $order = 1;
            if ($isSequence) {
                $order = $index + 1;
            }

            $approvalsToStore[] = [
                'approval_action'   => $approvalData['approval_action'],
                'role'              => $approvalData['role'],
                'user_id'            => $approvalData['user_id'],
                'status'            => 'pending',
                'approval_date'     => null,
                'approval_order'    => $order,
            ];
        }

        try {
            DB::transaction(function () use ($validated, $request, $approvalsToStore) {
                $registerAsset = RegisterAsset::create([
                    'form_no'       => $validated['form_no'],
                    'department_id' => $validated['department_id'],
                    'location_id'   => $validated['location_id'],
                    'asset_type'    => $validated['asset_type'],
                    'insured'       => ($validated['insured'] == 'Y') ? 1 : 0,
                    'polish_no'     => $validated['polish_no'],
                    'sequence'      => ($validated['sequence'] == 'Y') ? 1 : 0,
                    'status'        => 'Waiting',
                    'company_id'    => $validated['company_id'],
                ]);

                foreach ($validated['assets'] as $assetData) {
                    $registerAsset->detailRegisters()->create($assetData);
                }

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        // Simpan file ke storage/app/public/attachments
                        $filePath = $file->store('attachments', 'public');
                        
                        // Buat record di tabel attachments
                        $registerAsset->attachments()->create([
                            'file_path' => $filePath,
                            'original_filename' => $file->getClientOriginalName(),
                        ]);
                    }
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
        Gate::authorize('is-form-maker');

        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();
        $users = User::join('company_users', 'users.id', '=', 'company_users.user_id')
            ->where('company_users.company_id', session('active_company_id'))
            ->select('users.id', 'users.name', 'company_users.role as user_role')
            ->get();

        $register_asset->load('approvals.user', 'department', 'location', 'detailRegisters.assetName.assetSubClass.assetClass');

        return view('register-asset.edit', compact('register_asset', 'locations', 'departments', 'assetclasses', 'users'));
    }

    public function update(Request $request, RegisterAsset $registerAsset)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'location_id'   => 'required|exists:locations,id',
            'asset_type'    => 'required',
            'insured'       => 'required',
            'polish_no'     => 'required_if:insured,Y|nullable|string|max:255',
            'sequence'      => 'required',

            //Validasi Detail Asset
            'assets'                    => 'required|array|min:1',
            'assets.*.po_no'            => 'nullable|string|max:255',
            'assets.*.invoice_no'       => 'nullable|string|max:255',
            'assets.*.commission_date'  => 'required_if:asset_type,FA|nullable|date',
            'assets.*.specification'    => 'required|string',
            'assets.*.asset_name_id'    => 'required|exists:asset_names,id',

            //Validasi Attachments
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,png,xlsx|max:5120',
            'deleted_attachments' => 'nullable|array',
            'deleted_attachments.*' => 'integer|exists:attachments,id',

            //Validasi Approval
            'approvals'                     => 'required|array|min:1',
            'approvals.*.approval_action'   => 'required|string|max:255',
            'approvals.*.role'              => 'required|string|max:255',
            'approvals.*.user_id'           => 'required|exists:users,id',
            'approvals.*.status'            => 'required|string|max:255',
            'approvals.*.approval_date'     => 'nullable|date',

        ]);

        try {
            DB::transaction(function () use ($validated, $request, $registerAsset) {
                $registerAsset->update([
                    'department_id' => $validated['department_id'],
                    'location_id'   => $validated['location_id'],
                    'asset_type'   => $validated['asset_type'],
                    'insured'       => ($validated['insured'] == 'Y') ? 1 : 0,
                    'polish_no'   => $validated['polish_no'],
                    'sequence'      => ($validated['sequence'] == 'Y') ? 1 : 0,
                ]);

                $registerAsset->detailRegisters()->delete();
                if (isset($validated['assets'])) {
                    foreach ($validated['assets'] as $assetData) {
                        $registerAsset->detailRegisters()->create($assetData);
                    }
                }

                if (!empty($validated['deleted_attachments'])) {
                    $attachmentsToDelete = Attachment::find($validated['deleted_attachments']);
                    foreach ($attachmentsToDelete as $attachment) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }

                // 2. Tambah lampiran baru
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filePath = $file->store('attachments', 'public');
                        $registerAsset->attachments()->create([
                            'file_path' => $filePath,
                            'original_filename' => $file->getClientOriginalName(),
                        ]);
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
                        'status'            => $approvalData['status'],                        
                        'approval_date'     => $approvalData['approval_date'],
                        'user_id'           => $approvalData['user_id'],
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
                ->with('error', 'Gagal membatalkan data: ' . $e->getMessage());
        }

        return redirect()->route('register-asset.index')->with('success', 'Data berhasil dibatalkan!');
    }

    public function restore($id)
    {
        try {
            $register_asset = RegisterAsset::onlyTrashed()->findOrFail($id);
            $register_asset->restore();

        } catch (\Exception $e) {
            return redirect()->route('register-asset.index')
                ->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }

        return redirect()->route('register-asset.trash')->with('success', 'Data berhasil dipulihkan!');
    }

    public function show(RegisterAsset $register_asset)
    {
        // Eager load relasi untuk efisiensi
        $register_asset->load('approvals.user', 'department', 'location', 'detailRegisters.assetName.assetSubClass.assetClass', 'attachments');

        $canApprove = false;
        $userApprovalStatus = null;

        if ($register_asset->status === 'Waiting') {
            $user = Auth::user();
            $userApprovalStatus = 'Anda tidak ada dalam daftar approver, atau bukan giliran Anda.';

            if ($user->id) {
                // LOGIKA UNTUK APPROVAL PARALLEL
                if ($register_asset->sequence === "0") {
                    if ($register_asset->approvals()->where('user_id', $user->id)->where('status', 'pending')->exists()) {
                        $canApprove = true;
                    }
                }

                // LOGIKA UNTUK APPROVAL SEQUENTIAL
                if ($register_asset->sequence === "1") {
                    $nextApprover = $register_asset->approvals()->where('status', 'pending')->orderBy('approval_order', 'asc')->first();
                    if ($nextApprover && $nextApprover->user_id == $user->id) {
                        $canApprove = true;
                    }
                }

                // Cek apakah user sudah pernah approve
                if ($register_asset->approvals()->where('status', 'approved')->where('user_id', $user->id)->exists()) {
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

        if (empty($user->signature)) {
            return back()->with('error', 'You must save a signature in your profile before approving.');
        }

        // Validasi ulang di backend untuk keamanan
        $nextApprover = $register_asset->approvals()
            ->where('status', 'pending')
            ->orderBy('approval_order', 'asc')
            ->first();

        // Kondisi kapan user TIDAK BOLEH approve
        if (
            ($register_asset->sequence === "1" && (!$nextApprover || $nextApprover->user_id !== $user->id)) ||
            ($register_asset->sequence === "0" && !$register_asset->approvals()->where('user_id', $user->id)->where('status', 'pending')->exists())
        ) {
            return back()->with('error', 'Saat ini bukan giliran Anda untuk melakukan approval.');
        }
        
        try {
            DB::transaction(function () use ($register_asset, $user, $request, $nextApprover) {
                // 1. Update baris approval milik user ini
                $approval = $register_asset->approvals()
                    ->where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->when($register_asset->sequence === 1, function ($query) use ($nextApprover) {
                        return $query->where('approval_order', $nextApprover->approval_order);
                    })
                    ->first();

                if ($approval) {
                    $approval->update([
                        'status' => 'approved',
                        'approval_date' => now(),
                        'user_id' => $user->id,
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

        return back()->with('success', 'Formulir berhasil disetujui.');
    }

    // private function generateAssetNumber($companyId, $assetNameId)
    // {
    //     // 1. Siapkan komponen-komponennya
    //     $prefix = 'FA';
    //     $companyCode = str_pad($companyId, 2, '0', STR_PAD_LEFT); // Contoh: 1 -> 01
    //     $year = now()->format('y'); // yy -> 25
    //     $assetGrouping = $assetNameId;

    //     // 2. Buat prefix lengkap untuk pencarian di database
    //     $searchPrefix = $prefix . $companyCode;

    //     // 3. Cari aset terakhir dengan prefix yang sama
    //     $lastAsset = Asset::where('asset_number', 'like', $searchPrefix . '%')
    //                     ->orderBy('asset_number', 'desc')
    //                     ->first();

    //     $sequence = 1; // Mulai dari 1 jika tidak ada data sebelumnya
    //     if ($lastAsset) {
    //         // Ambil 5 digit terakhir dari nomor aset, ubah ke integer, lalu tambah 1
    //         $lastSequence = (int) substr($lastAsset->asset_number, -5);
    //         $sequence = $lastSequence + 1;
    //     }

    //     // 4. Format nomor urut menjadi 5 digit dengan angka nol di depan
    //     $formattedSequence = str_pad($sequence, 5, '0', STR_PAD_LEFT); // 1 -> 00001

    //     // 5. Gabungkan semuanya
    //     return $searchPrefix . $year . $assetGrouping . $formattedSequence;
    // }

    private function finalizeAssetRegistration(RegisterAsset $register_asset)
    {
        // Ubah status form menjadi 'approved'
        $register_asset->update(['status' => 'Approved']);

        // Loop melalui detail dan buat aset baru
        foreach ($register_asset->detailRegisters as $detail) {

            // $assetNameId = $detail->assetName->grouping;

            // $newAssetNumber = $this->generateAssetNumber($register_asset->company_id, $assetNameId);

            Asset::create([
                'asset_number' => null,
                'asset_name_id' => $detail->asset_name_id,
                'asset_type' => $register_asset->asset_type,
                'status' => 'Onboard',
                'description' => $detail->specification,
                'detail' => null,
                'pareto' => null,
                'unit_no' => null,
                'sn_chassis' => null,
                'sn_engine' => null,
                'po_no' => $detail->po_no,
                'location_id' => $register_asset->location_id,
                'department_id' => $register_asset->department_id,
                'quantity' => 1,
                'capitalized_date' => now(),
                'start_depre_date' => null,
                'acquisition_value' => 0,
                'current_cost' => 0,
                'commercial_useful_life_month' => $detail->assetName->commercial * 12,
                'commercial_accum_depre' => 0,
                'commercial_nbv' => 0,
                'fiscal_useful_life_month' => $detail->assetName->fiscal * 12,
                'fiscal_accum_depre' => 0,
                'fiscal_nbv' => 0,
                'company_id' => $register_asset->company_id,
            ]);
        }
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = RegisterAsset::withoutGlobalScope(CompanyScope::class)
                        ->with(['department', 'location'])
                        ->withCount('detailRegisters')
                        ->where('company_id', $companyId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('department_name', function($registerAsset) {
                return $registerAsset->department->name ?? '-';
            })
            ->addColumn('location_name', function($registerAsset) {
                return $registerAsset->location->name ?? '-';
            })
            ->addColumn('action', function ($register_assets) {
                return view('components.action-form-buttons', [
                    'model'     => $register_assets,
                    'showUrl' => route('register-asset.show', $register_assets->id),
                    'editUrl' => route('register-asset.edit', $register_assets->id),
                    'deleteUrl' => route('register-asset.destroy', $register_assets->id)
                ])->render();
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('location_name', function($query, $keyword) {
                $query->whereHas('location', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('department_name', function ($query, $order) {
                $query->orderBy(
                    Department::select('name')
                        ->whereColumn('departments.id', 'register_assets.department_id'),
                    $order
                );
            })
            ->orderColumn('location_name', function ($query, $order) {
                $query->orderBy(
                    Location::select('name')
                        ->whereColumn('locations.id', 'register_assets.location_id'),
                    $order
                );
            })
            ->rawColumns(['action'])
            ->toJson();        
    }

    public function datatablesCanceled(Request $request)
    {
        $companyId = session('active_company_id');

        $query = RegisterAsset::withoutGlobalScope(CompanyScope::class)
                        ->with(['department', 'location'])
                        ->withCount('detailRegisters')
                        ->onlyTrashed()
                        ->where('company_id', $companyId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('department_name', function($registerAsset) {
                return $registerAsset->department->name ?? '-';
            })
            ->addColumn('location_name', function($registerAsset) {
                return $registerAsset->location->name ?? '-';
            })
            ->addColumn('action', function ($register_assets) {
                return view('components.action-form-canceled-buttons', [
                    'model'     => $register_assets,
                    'restoreUrl' => route('register-asset.restore', $register_assets->id)
                ])->render();
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('location_name', function($query, $keyword) {
                $query->whereHas('location', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('department_name', function ($query, $order) {
                $query->orderBy(
                    Department::select('name')
                        ->whereColumn('departments.id', 'register_assets.department_id'),
                    $order
                );
            })
            ->orderColumn('location_name', function ($query, $order) {
                $query->orderBy(
                    Location::select('name')
                        ->whereColumn('locations.id', 'register_assets.location_id'),
                    $order
                );
            })
            ->rawColumns(['action'])
            ->toJson();        
    }

    public function exportPdf(RegisterAsset $register_asset)
    {
        $register_asset->load(
            'department', 
            'location', 
            'detailRegisters.assetName.assetSubClass.assetClass', 
            'approvals.user', 
            'company'
        );

        $pdf = Pdf::loadView('register-asset.pdf', ['register_asset' => $register_asset]);

        $pdf->setPaper('a4', 'portrait');

        $safeFilename = str_replace('/', '-', $register_asset->form_no);
        
        return $pdf->stream('Register-Asset-' . $safeFilename  . '.pdf');
    }
}
