<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisposalAsset;
use App\Models\Department;
use App\Models\Location;
use App\Models\AssetClass;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;

class DisposalAssetController extends Controller
{
    public function index()
    {
        $disposalassets = DisposalAsset::withCount('detailDisposals')->paginate(25);
        
        return view('disposal-asset.index', compact('disposalassets'));
    }

    public function create()
    {
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        $lastDisposalAsset = DisposalAsset::latest('id')->first();
        $seq = 1;

        if ($lastDisposalAsset){
            $lastSeq = (int) substr($lastDisposalAsset->form_no, -5);
            $seq = $lastSeq + 1;
        }
        
        $formattedSeq = str_pad($seq, 5, '0', STR_PAD_LEFT);
        $form_no = Auth::user()->lastActiveCompany->code ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('disposal-asset.create', compact('departments', 'assetclasses', 'form_no'));
    }

    public function store(Request $request)
    {
        //Store Disposal Asset
        $validated = $request->validate([
            'submit_date'  => 'required|date',
            'form_no' => 'required|string|max:255|unique:disposal_assets,form_no',
            'department_id' => 'required|exists:departments,id',
            'reason'  => 'required',
            'sequence'  => 'required',
            'nbv'  => 'required',
            'esp'  => 'required',
            'company_id'  => 'required|exists:companies,id',
            'kurs'  => 'required',

            //Validasi Detail Asset
            'asset_ids'     => 'required|string',

            //Validasi Approval
            'approvals'                     => 'required|array|min:1',
            'approvals.*.approval_action'   => 'required|string|max:255',
            'approvals.*.role'              => 'required|string|max:255',
            'approvals.0.user_id'           => 'required|string|max:255',
            'approvals.*.status'            => 'required|string|max:255',
            'approvals.0.approval_date'     => 'required|date',
        ]);

        $assetIds = explode(',', $validated['asset_ids']);

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
            DB::transaction(function () use ($validated, $assetIds, $approvalsToStore) {

                $assetsToDispose = Asset::whereIn('id', $assetIds)->get()->keyBy('id');

                $disposalAsset = DisposalAsset::create([
                    'submit_date'   => $validated['submit_date'],
                    'form_no'       => $validated['form_no'],
                    'department_id' => $validated['department_id'],
                    'reason'        => $validated['reason'],
                    'nbv'           => $validated['nbv'],
                    'esp'           => $validated['esp'],
                    'sequence'      => ($validated['sequence'] == 'Y') ? 1 : 0,
                    'status'        => 'Waiting',
                    'company_id'    => $validated['company_id'],
                ]);

                foreach ($assetIds as $assetId) {
                    $assetId = trim($assetId);
                    if($assetId !== '') {
                        $asset = $assetsToDispose->get($assetId);
                        if ($asset) {
                            $disposalAsset->detailDisposals()->create([
                                'asset_id' => $asset->id,
                                'kurs'     => $validated['kurs'],
                                'njab'     => $asset->net_book_value,
                            ]);
                        }
                    }
                }

                foreach ($approvalsToStore as $approvalData) {
                    $disposalAsset->approvals()->create($approvalData);
                }

            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }


        return redirect()->route('disposal-asset.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(DisposalAsset $disposal_asset)
    {
        $departments = Department::all();

        $disposal_asset->load('approvals.user', 'department', 'detailDisposals');

        $selectedAssetIds = $disposal_asset->detailDisposals->pluck('asset_id');

        return view('disposal-asset.edit', compact('disposal_asset', 'departments', 'selectedAssetIds'));
    }

    public function update(Request $request, DisposalAsset $disposalAsset)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'reason'  => 'required',
            'sequence'  => 'required',
            'nbv'  => 'required',
            'esp'  => 'required',

            'kurs'  => 'required',

            //Validasi Detail Asset
            'asset_ids'     => 'required|string',

            //Validasi Approval
            'approvals'                     => 'required|array|min:1',
            'approvals.*.approval_action'   => 'required|string|max:255',
            'approvals.*.role'              => 'required|string|max:255',
            'approvals.0.user_id'           => 'required|string|max:255',
            'approvals.*.status'            => 'required|string|max:255',
            'approvals.0.approval_date'     => 'required|date',
        ]);

        $assetIds = explode(',', $validated['asset_ids']);

        try {
            DB::transaction(function () use ($validated, $assetIds, $disposalAsset) {
                $disposalAsset->update([
                    'department_id' => $validated['department_id'],
                    'reason'        => $validated['reason'],
                    'sequence'      => ($validated['sequence'] == 'Y') ? 1 : 0,
                    'nbv'           => $validated['nbv'],
                    'esp'           => $validated['esp'],
                ]);

                $disposalAsset->detailDisposals()->delete();
                $disposalAsset->approvals()->delete();

                $assetsToDispose = Asset::whereIn('id', $assetIds)->get()->keyBy('id');
                
                foreach ($assetIds as $assetId) {
                    $assetId = trim($assetId);
                    if ($assetId !== '' && $assetsToDispose->has($assetId)) {
                        $asset = $assetsToDispose->get($assetId);
                        // PERBAIKAN: Gunakan variabel $disposalAsset yang benar
                        $disposalAsset->detailDisposals()->create([
                            'asset_id' => $asset->id,
                            'kurs'     => $validated['kurs'],
                            'njab'     => $asset->net_book_value,
                        ]);
                    }
                }
                
                // 5. Buat ulang daftar approval dengan logika yang benar
                $isSequence = ($validated['sequence'] === 'Y');
                foreach ($validated['approvals'] as $index => $approvalData) {
                    $isFirstApprover = ($index === 0);
                    $disposalAsset->approvals()->create([
                        'approval_action'             => $approvalData['approval_action'],
                        'role'     => $approvalData['role'],
                        'approval_order'    => $isSequence ? ($index + 1) : 1,
                        'status'            => $isFirstApprover ? 'approved' : 'pending',
                        'user_id' => $isFirstApprover ? Auth::id() : null,
                        'approval_date'         => $isFirstApprover ? now() : null,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('disposal-asset.index')->with('success', 'Data berhasil di-update');
    }

    public function destroy(DisposalAsset $disposal_asset)
    {
        try {
            $disposal_asset->delete();

        } catch (\Exception $e) {
            return redirect()->route('disposal-asset.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->route('register-asset.index')->with('success', 'Data berhasil dihapus!');
    }

    public function show(DisposalAsset $disposal_asset)
    {
        // Eager load relasi untuk efisiensi
        $disposal_asset->load('approvals.user', 'department', 'detailDisposals');
        
        $canApprove = false;
        $userApprovalStatus = null;

        if ($disposal_asset->status === 'Waiting') {
            $user = Auth::user();
            $userApprovalStatus = 'Anda tidak ada dalam daftar approver, atau bukan giliran Anda.';

            if ($user->role) {
                // LOGIKA UNTUK APPROVAL PARALLEL
                if ($disposal_asset->sequence === "0") {
                    if ($disposal_asset->approvals()->where('role', $user->role)->where('status', 'pending')->exists()) {
                        $canApprove = true;
                    }
                }

                // LOGIKA UNTUK APPROVAL SEQUENTIAL
                if ($disposal_asset->sequence === "1") {
                    $nextApprover = $disposal_asset->approvals()->where('status', 'pending')->orderBy('approval_order', 'asc')->first();
                    if ($nextApprover && $nextApprover->role === $user->role) {
                        $canApprove = true;
                    }
                }

                // Cek apakah user sudah pernah approve
                if ($disposal_asset->approvals()->where('user_id', $user->id)->exists()) {
                    $canApprove = false; // Override, pastikan tidak bisa approve dua kali
                    $userApprovalStatus = 'Anda sudah menyetujui formulir ini.';
                }
            } else {
                $userApprovalStatus = 'Anda tidak memiliki role di perusahaan ini.';
            }
        }
        return view('disposal-asset.show', compact('disposal_asset', 'canApprove', 'userApprovalStatus'));
    }

    public function approve(Request $request, DisposalAsset $disposal_asset)
    {
        $user = Auth::user();

        // Validasi ulang di backend untuk keamanan
        $nextApprover = $disposal_asset->approvals()
            ->where('status', 'pending')
            ->orderBy('approval_order', 'asc')
            ->first();

        // Kondisi kapan user TIDAK BOLEH approve
        if (
            ($disposal_asset->sequence === "1" && (!$nextApprover || $nextApprover->role !== $user->role)) ||
            ($disposal_asset->sequence === "0" && !$disposal_asset->approvals()->where('role', $user->role)->where('status', 'pending')->exists())
        ) {
            return back()->with('error', 'Saat ini bukan giliran Anda untuk melakukan approval.');
        }
        
        try {
            DB::transaction(function () use ($disposal_asset, $user, $request, $nextApprover) {
                // 1. Update baris approval milik user ini
                $approval = $disposal_asset->approvals()
                    ->where('role', $user->role)
                    ->where('status', 'pending')
                    ->when($disposal_asset->sequence === 1, function ($query) use ($nextApprover) {
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
                $allApproved = $disposal_asset->approvals()->where('status', '!=', 'approved')->doesntExist();

                if ($allApproved) {
                    $this->finalizeAssetDisposal($disposal_asset);
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('register-asset.index')->with('success', 'Formulir berhasil disetujui.');
    }

    private function finalizeAssetDisposal(DisposalAsset $disposal_asset)
    {
        // Ubah status form menjadi 'approved'
        $disposal_asset->update(['status' => 'Approved']);

        $asset = $disposal_asset->asset; 

        if ($asset) {
            $asset->update([
                'status'   => 'Disposal',
            ]);
        } else {
            // (Opsional) Tambahkan penanganan error jika aset tidak ditemukan
            throw new \Exception("Aset yang terhubung dengan form transfer ini tidak ditemukan.");
        }
    }

    public function datatablesAsset(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Asset::withoutGlobalScope(CompanyScope::class)
                        ->join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
                        ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
                        ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
                        ->join('locations', 'assets.location_id', '=', 'locations.id')
                        ->join('departments', 'assets.department_id', '=', 'departments.id')
                        ->where('assets.company_id', $companyId)
                        ->select([
                            'assets.*',
                            'asset_names.name as asset_name_name',
                            'asset_classes.obj_acc as asset_class_obj',
                            'locations.name as location_name',
                            'departments.name as department_name',
                        ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('checkbox', function ($asset) {
                return '<input type="checkbox" class="asset-checkbox" value="' . $asset->id . '">';
            })
            ->filterColumn('asset_name_name', function($query, $keyword) {
                $query->where('asset_names.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('asset_class_obj', function($query, $keyword) {
                $query->where('asset_classes.obj_acc', 'like', "%{$keyword}%");
            })
            ->filterColumn('location_name', function($query, $keyword) {
                $query->where('locations.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->where('departments.name', 'like', "%{$keyword}%");
            })
            ->rawColumns(['checkbox'])
            ->toJson();
    }
}