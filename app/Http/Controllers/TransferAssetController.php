<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransferAsset;
use App\Models\Asset;
use App\Models\Location;
use App\Models\Department;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

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
        $form_no = Auth::user()->lastActiveCompany->alias ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('transfer-asset.create', compact('locations', 'departments', 'form_no'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'submit_date' => 'required|date',
            'form_no' => 'required|string|max:255|unique:transfer_assets,form_no',
            'department_id' => 'required|exists:departments,id',
            'destination_loc_id'  => 'required|exists:locations,id',
            'reason'  => 'required',
            'sequence'  => 'required',
            'company_id'  => 'required|exists:companies,id',

            //Validasi Detail Asset
            'asset_ids'     => 'required|string',

            //Validasi Attachments
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx|max:5120', // Maks 5MB per file

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
            DB::transaction(function () use ($validated, $assetIds, $approvalsToStore, $request) {

                $assetsToTransfer = Asset::whereIn('id', $assetIds)->get();

                $transferAsset = TransferAsset::create([
                    'submit_date'           => $validated['submit_date'],
                    'form_no'               => $validated['form_no'],
                    'department_id'         => $validated['department_id'],
                    'destination_loc_id'    => $validated['destination_loc_id'],
                    'reason'                => $validated['reason'],
                    'sequence'              => ($validated['sequence'] == 'Y') ? 1 : 0,
                    'status'                => 'Waiting',
                    'company_id'            => $validated['company_id'],
                ]);

                foreach ($assetsToTransfer as $asset) {
                    $transferAsset->detailTransfers()->create([
                        'asset_id'              => $asset->id,
                        'origin_loc_id'         => $asset->location_id,
                        'destination_loc_id'    => $validated['destination_loc_id'],
                    ]);
                }

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        // Simpan file ke storage/app/public/attachments
                        $filePath = $file->store('attachments', 'public');
                        
                        // Buat record di tabel attachments
                        $transferAsset->attachments()->create([
                            'file_path' => $filePath,
                            'original_filename' => $file->getClientOriginalName(),
                        ]);
                    }
                }

                foreach ($approvalsToStore as $approvalData) {
                    $transferAsset->approvals()->create($approvalData);
                }

            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }


        return redirect()->route('transfer-asset.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(TransferAsset $transfer_asset)
    {
        $locations = Location::all();
        $departments = Department::all();

        $transfer_asset->load('approvals.user', 'department', 'destinationLocation', 'detailTransfers');

        $selectedAssetIds = $transfer_asset->detailTransfers->pluck('asset_id');

        return view('transfer-asset.edit', compact('transfer_asset', 'locations', 'departments', 'selectedAssetIds'));
    }

    public function update(Request $request, TransferAsset $transfer_asset)
    {
        $validated = $request->validate([
            'department_id'         => 'required|exists:departments,id',
            'destination_loc_id'    => 'required|exists:locations,id',
            'reason'                => 'required|string',
            'sequence'              => 'required',

            //Asset
            'asset_ids'          => 'required|string',

            //Validasi Attachments
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,png,xlsx|max:5120',
            'deleted_attachments' => 'nullable|array',
            'deleted_attachments.*' => 'integer|exists:attachments,id',

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
            DB::transaction(function () use ($validated, $request, $transfer_asset, $assetIds) {
                $transfer_asset->update([
                    'department_id'         => $validated['department_id'],
                    'destination_loc_id'    => $validated['destination_loc_id'],
                    'reason'                => $validated['reason'],
                    'sequence'              => ($validated['sequence'] == 'Y') ? 1 : 0,
                ]);

                $transfer_asset->detailTransfers()->delete();
                $assetsToTransfer = Asset::whereIn('id', $assetIds)->get();
                foreach ($assetsToTransfer as $asset) {
                    $transfer_asset->detailTransfers()->create([
                        'asset_id'              => $asset->id,
                        'origin_loc_id'         => $asset->location_id,
                        'destination_loc_id'    => $validated['destination_loc_id'],
                    ]);
                }

                if (!empty($validated['deleted_attachments'])) {
                    $attachmentsToDelete = Attachment::find($validated['deleted_attachments']);
                    foreach ($attachmentsToDelete as $attachment) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filePath = $file->store('attachments', 'public');
                        $transfer_asset->attachments()->create([
                            'file_path' => $filePath,
                            'original_filename' => $file->getClientOriginalName(),
                        ]);
                    }
                }

                $transfer_asset->approvals()->delete();
                $isSequence = ($validated['sequence'] === 'Y');
                foreach ($validated['approvals'] as $index => $approvalData) {
                    $order = $isSequence ? ($index + 1) : 1;

                    $transfer_asset->approvals()->create([
                        'approval_action'   => $approvalData['approval_action'],
                        'role'              => $approvalData['role'],
                        'approval_order'    => $order,
                        'status'            => 'pending',
                        'user_id'           => null,
                        'approval_date'     => null,
                    ]);
                }
                
                $firstApprover = $transfer_asset->approvals()->orderBy('approval_order', 'asc')->first();
                if ($firstApprover) {
                    $firstApprover->update([
                        'status'        => 'approved',
                        'user_id'       => Auth::id(),
                        'approval_date' => now(),
                    ]);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('transfer-asset.index')->with('success', 'Data berhasil di-update');
    }

    public function destroy(TransferAsset $transfer_asset)
    {
        try {
            $transfer_asset->delete();

        } catch (\Exception $e) {
            return redirect()->route('transfer-asset.index')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->route('transfer-asset.index')->with('success', 'Data berhasil dihapus!');
    }

    public function show(TransferAsset $transfer_asset)
    {
        // Eager load relasi untuk efisiensi
        $transfer_asset->load('approvals.user', 'department', 'destinationLocation', 'detailTransfers', 'attachments');
        
        $canApprove = false;
        $userApprovalStatus = null;

        if ($transfer_asset->status === 'Waiting') {
            $user = Auth::user();
            $userApprovalStatus = 'Anda tidak ada dalam daftar approver, atau bukan giliran Anda.';

            if ($user->role) {
                // LOGIKA UNTUK APPROVAL PARALLEL
                if ($transfer_asset->sequence === "0") {
                    if ($transfer_asset->approvals()->where('role', $user->role)->where('status', 'pending')->exists()) {
                        $canApprove = true;
                    }
                }

                // LOGIKA UNTUK APPROVAL SEQUENTIAL
                if ($transfer_asset->sequence === "1") {
                    $nextApprover = $transfer_asset->approvals()->where('status', 'pending')->orderBy('approval_order', 'asc')->first();
                    if ($nextApprover && $nextApprover->role === $user->role) {
                        $canApprove = true;
                    }
                }

                // Cek apakah user sudah pernah approve
                if ($transfer_asset->approvals()->where('user_id', $user->id)->exists()) {
                    $canApprove = false; // Override, pastikan tidak bisa approve dua kali
                    $userApprovalStatus = 'Anda sudah menyetujui formulir ini.';
                }
            } else {
                $userApprovalStatus = 'Anda tidak memiliki role di perusahaan ini.';
            }
        }
        return view('transfer-asset.show', compact('transfer_asset', 'canApprove', 'userApprovalStatus'));
    }

    public function approve(Request $request, TransferAsset $transfer_asset)
    {
        $user = Auth::user();

        // Validasi ulang di backend untuk keamanan
        $nextApprover = $transfer_asset->approvals()
            ->where('status', 'pending')
            ->orderBy('approval_order', 'asc')
            ->first();

        // Kondisi kapan user TIDAK BOLEH approve
        if (
            ($transfer_asset->sequence === "1" && (!$nextApprover || $nextApprover->role !== $user->role)) ||
            ($transfer_asset->sequence === "0" && !$transfer_asset->approvals()->where('role', $user->role)->where('status', 'pending')->exists())
        ) {
            return back()->with('error', 'Saat ini bukan giliran Anda untuk melakukan approval.');
        }
        
        try {
            DB::transaction(function () use ($transfer_asset, $user, $request, $nextApprover) {
                // 1. Update baris approval milik user ini
                $approval = $transfer_asset->approvals()
                    ->where('role', $user->role)
                    ->where('status', 'pending')
                    ->when($transfer_asset->sequence === 1, function ($query) use ($nextApprover) {
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
                    // Throw exception jika approval tidak ditemukan, untuk membatalkan transaksi
                    throw new \Exception("Approval yang valid tidak ditemukan untuk peran Anda.");
                }

                // 2. Cek apakah semua approval sudah selesai
                $allApproved = $transfer_asset->approvals()->where('status', '!=', 'approved')->doesntExist();

                if ($allApproved) {
                    // 3. JIKA SELESAI, jalankan proses final
                    $this->finalizeAssetTransfer($transfer_asset);
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('transfer-asset.index')->with('success', 'Formulir berhasil disetujui.');
    }

    private function finalizeAssetTransfer(TransferAsset $transfer_asset)
    {
        // Ubah status form menjadi 'approved'
        $transfer_asset->update(['status' => 'Approved']);

        $asset = $transfer_asset->asset; 

        if ($asset) {
            $asset->update([
                'location_id'   => $transfer_asset->destination_loc_id,
            ]);
        } else {
            // (Opsional) Tambahkan penanganan error jika aset tidak ditemukan
            throw new \Exception("Aset yang terhubung dengan form transfer ini tidak ditemukan.");
        }
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = TransferAsset::withoutGlobalScope(CompanyScope::class)
                        ->with(['destinationLocation', 'department'])
                        ->withCount('detailTransfers')
                        ->where('company_id', $companyId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('destination_location_name', function($transferAsset) {
                return $transferAsset->destinationLocation->name ?? '-';
            })
            ->addColumn('department_name', function($transferAsset) {
                return $transferAsset->department->name ?? '-';
            })
            ->addColumn('asset_quantity', function($transferAsset) {
                return $transferAsset->detail_transfers_count . ' Asset(s)';
            })
            ->addColumn('action', function ($transfer_assets) {
                return view('components.action-buttons-3-buttons', [
                    'model'     => $transfer_assets,
                    'showUrl' => route('transfer-asset.show', $transfer_assets->id),
                    'editUrl' => route('transfer-asset.edit', $transfer_assets->id),
                    'deleteUrl' => route('transfer-asset.destroy', $transfer_assets->id)
                ])->render();
            })
            ->filterColumn('destination_location_name', function($query, $keyword) {
                $query->whereHas('destinationLocation', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('destination_location_name', function ($query, $order) {
                $query->orderBy(
                    Location::select('name')
                        ->whereColumn('locations.id', 'transfer_assets.destination_loc_id'),
                    $order
                );
            })
            ->orderColumn('department_name', function ($query, $order) {
                $query->orderBy(
                    Department::select('name')
                        ->whereColumn('departments.id', 'transfer_assets.department_id'),
                    $order
                );
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
