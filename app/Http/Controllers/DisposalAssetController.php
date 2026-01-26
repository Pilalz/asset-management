<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisposalAsset;
use App\Models\Department;
use App\Models\Location;
use App\Models\AssetName;
use App\Models\Asset;
use App\Models\Attachment;
use App\Models\User;
use App\Models\Company;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use App\Rules\AssetIsAvailable;

class DisposalAssetController extends Controller
{
    public function index()
    {   
        $companyId = session('active_company_id');
        
        $departmentsForFilter = Department::withoutGlobalScope(CompanyScope::class)
                                       ->where('company_id', $companyId)
                                       ->orderBy('name', 'asc')
                                       ->get(['id', 'name']);

        return view('disposal-asset.index', compact('departmentsForFilter'));
    }

    public function trash()
    {
        return view('disposal-asset.canceled');
    }

    public function create()
    {
        Gate::authorize('is-form-maker');

        $departments = Department::all();
        $assetNamesForFilter = AssetName::withoutGlobalScope(CompanyScope::class)
                                       ->where('company_id', session('active_company_id'))
                                       ->orderBy('name', 'asc')
                                       ->get(['id', 'name']);
        $locationsForFilter = Location::withoutGlobalScope(CompanyScope::class)
                                     ->where('company_id', session('active_company_id'))
                                     ->orderBy('name', 'asc')
                                     ->get(['id', 'name']);
        $departmentsForFilter = Department::withoutGlobalScope(CompanyScope::class)
                                       ->where('company_id', session('active_company_id'))
                                       ->orderBy('name', 'asc')
                                       ->get(['id', 'name']);

        $users = User::join('company_users', 'users.id', '=', 'company_users.user_id')
            ->where('company_users.company_id', session('active_company_id'))
            ->select('users.id', 'users.name', 'company_users.role as user_role')
            ->get();

        $lastDisposalAsset = DisposalAsset::withTrashed()->latest('id')->first();
        $seq = 1;

        if ($lastDisposalAsset){
            $lastSeq = (int) substr($lastDisposalAsset->form_no, -5);
            $seq = $lastSeq + 1;
        }
        
        $formattedSeq = str_pad($seq, 5, '0', STR_PAD_LEFT);
        $form_no = Auth::user()->lastActiveCompany->alias ."/". now()->format('Y/m') ."/". $formattedSeq ;
        
        return view('disposal-asset.create', compact('departments', 'assetNamesForFilter', 'form_no', 'users', 'locationsForFilter', 'departmentsForFilter'));
    }

    public function store(Request $request)
    {
        Gate::authorize('is-form-maker');

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
            'prices'     => ['required', 'array', 'min:1', new AssetIsAvailable],
            'prices.*'      => 'required|numeric',

            //Validasi Attachment
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx|max:5120',

            //Validasi Approval
            'approvals'                     => 'required|array|min:1',
            'approvals.*.approval_action'   => 'required|string|max:255',
            'approvals.*.role'              => 'required|string|max:255',
            'approvals.*.user_id'           => 'required|string|max:255',
            'approvals.*.status'            => 'required|string|max:255',
            'approvals.0.approval_date'     => 'nullable|date',
        ]);

        $pricesByAssetId = $validated['prices'];

        $assetIds = array_keys($pricesByAssetId);

        $approvalsToStore = [];
        $isSequence = ($validated['sequence'] === "1");

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
            DB::transaction(function () use ($validated, $request, $pricesByAssetId, $assetIds, $approvalsToStore) {

                $assetsToDispose = Asset::whereIn('id', $assetIds)->get()->keyBy('id');

                $disposalAsset = DisposalAsset::create([
                    'submit_date'   => $validated['submit_date'],
                    'form_no'       => $validated['form_no'],
                    'department_id' => $validated['department_id'],
                    'reason'        => $validated['reason'],
                    'nbv'           => $validated['nbv'],
                    'esp'           => $validated['esp'],
                    'sequence'      => $validated['sequence'],
                    'status'        => 'Waiting',
                    'company_id'    => $validated['company_id'],
                ]);

                foreach ($pricesByAssetId as $assetId => $price) {
                    $asset = $assetsToDispose->get($assetId); // Ambil data aset dari collection

                    if ($asset) { // Pastikan aset ada
                        // 8. Buat record 'detailDisposals'
                        $disposalAsset->detailDisposals()->create([
                            'asset_id' => $assetId,
                            'kurs'     => $validated['kurs'],
                            'njab'     => $price,
                        ]);
                    }
                }

                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {

                        $filePath = $file->store('attachments', 'public');
                        
                        $disposalAsset->attachments()->create([
                            'file_path' => $filePath,
                            'original_filename' => $file->getClientOriginalName(),
                        ]);
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
        Gate::authorize('is-form-maker');
        
        $departments = Department::all();
        $users = User::join('company_users', 'users.id', '=', 'company_users.user_id')
            ->where('company_users.company_id', session('active_company_id'))
            ->select('users.id', 'users.name', 'company_users.role as user_role')
            ->get();

        $disposal_asset->load('approvals.user', 'department', 'detailDisposals');

        $pricesForJs = $disposal_asset->detailDisposals->pluck('njab', 'asset_id');

        return view('disposal-asset.edit', [ 
            'disposal_asset' => $disposal_asset,
            'departments' => $departments,
            'users' => $users,
            'pricesForJs' => $pricesForJs,
            'activeCompany' => auth()->user()->activeCompany,
        ]);
    }

    public function update(Request $request, DisposalAsset $disposalAsset)
    {
        Gate::authorize('is-form-maker');

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'reason'  => 'required',
            'sequence'  => 'required',
            'nbv'  => 'required',
            'esp'  => 'required',
            'kurs'  => 'required',

            //Validasi Detail Asset
            'prices' => ['required', 'array', 'min:1', new AssetIsAvailable('disposal', $disposalAsset->id)],
            'prices.*'      => 'required|numeric',

            //Validasi Attachments
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,png,xlsx|max:5120',
            'deleted_attachments' => 'nullable|array',
            'deleted_attachments.*' => 'integer|exists:attachments,id',

            //Validasi Approval
            'approvals'                     => 'required|array|min:1',
            'approvals.*.approval_action'   => 'required|string|max:255',
            'approvals.*.role'              => 'required|string|max:255',
            'approvals.*.user_id'           => 'required|string|max:255',
            'approvals.*.status'            => 'required|string|max:255',
            'approvals.*.approval_date'     => 'nullable|date',
        ]);

        $pricesByAssetId = $validated['prices'];
        $assetIds = array_keys($pricesByAssetId);

        try {
            DB::transaction(function () use ($validated, $request, $assetIds, $pricesByAssetId, $disposalAsset) {
                $disposalAsset->update([
                    'department_id' => $validated['department_id'],
                    'reason'        => $validated['reason'],
                    'sequence'      => $validated['sequence'],
                    'nbv'           => $validated['nbv'],
                    'esp'           => $validated['esp'],
                ]);

                $disposalAsset->detailDisposals()->delete();
                $disposalAsset->approvals()->delete();

                $assetsToDispose = Asset::whereIn('id', $assetIds)->get()->keyBy('id');
                
                foreach ($pricesByAssetId as $assetId => $price) {
                    $assetId = trim($assetId);
                    if ($assetId !== '' && $assetsToDispose->has($assetId)) {
                        $asset = $assetsToDispose->get($assetId);
                        $disposalAsset->detailDisposals()->create([
                            'asset_id' => $assetId,
                            'kurs'     => $validated['kurs'],
                            'njab'     => $price,
                        ]);
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
                        $disposalAsset->attachments()->create([
                            'file_path' => $filePath,
                            'original_filename' => $file->getClientOriginalName(),
                        ]);
                    }
                }
                
                // 5. Buat ulang daftar approval dengan logika yang benar
                $isSequence = ($validated['sequence'] === "1");
                foreach ($validated['approvals'] as $index => $approvalData) {
                    $isFirstApprover = ($index === 0);
                    $disposalAsset->approvals()->create([
                        'approval_action'   => $approvalData['approval_action'],
                        'role'              => $approvalData['role'],
                        'approval_order'    => $isSequence ? ($index + 1) : 1,
                        'status'            => $approvalData['status'],
                        'approval_date'     => $approvalData['approval_date'],
                        'user_id'           => $approvalData['user_id'],
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
        Gate::authorize('is-form-maker');

        try {
            $disposal_asset->delete();

        } catch (\Exception $e) {
            return redirect()->route('disposal-asset.index')
                ->with('error', 'Gagal membatalkan data: ' . $e->getMessage());
        }

        return redirect()->route('disposal-asset.index')->with('success', 'Data berhasil dibatalkan!');
    }

    public function restore($id)
    {
        Gate::authorize('is-form-maker');

        try {
            $disposal_asset = DisposalAsset::onlyTrashed()
                ->with([
                    'department'=> function($q) { 
                        $q->withTrashed(); 
                    },
                ])
                ->findOrFail($id);

            if ($disposal_asset->department && $disposal_asset->department->trashed()) {
                return redirect()->route('disposal-asset.trash')
                    ->with('error', "Gagal Restore! Departemen '{$disposal_asset->department->name}' saat ini berstatus Terhapus (Deleted). Silahkan restore departemen tersebut terlebih dahulu di menu Master Data.");
            }

            $disposal_asset->restore();

        } catch (\Exception $e) {
            return redirect()->route('disposal-asset.trash')
                ->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }

        return redirect()->route('disposal-asset.index')->with('success', 'Data berhasil dipulihkan!');
    }

    public function show(DisposalAsset $disposal_asset)
    {
        // Eager load relasi untuk efisiensi
        $disposal_asset->load('approvals.user', 'department', 'detailDisposals', 'attachments');
        
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
                if ($disposal_asset->approvals()->where('status', 'pending')->where('user_id', $user->id)->get()->isEmpty()) {
                    $canApprove = false;
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
            ($disposal_asset->sequence === "1" && (!$nextApprover || $nextApprover->user_id != $user->id)) ||
            ($disposal_asset->sequence === "0" && !$disposal_asset->approvals()->where('user_id', $user->id)->where('status', 'pending')->exists())
        ) {
            return back()->with('error', 'Saat ini bukan giliran Anda untuk melakukan approval.');
        }
        
        try {
            DB::transaction(function () use ($disposal_asset, $user, $request, $nextApprover) {
                // 1. Update baris approval milik user ini
                $approval = $disposal_asset->approvals()
                    ->where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->when($disposal_asset->sequence === 1, function ($query) use ($nextApprover) {
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
                $allApproved = $disposal_asset->approvals()->where('status', '!=', 'approved')->doesntExist();

                if ($allApproved) {
                    $this->finalizeAssetDisposal($disposal_asset);
                }
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back()->with('success', 'Formulir berhasil disetujui.');
    }

    private function finalizeAssetDisposal(DisposalAsset $disposal_asset)
    {
        // 1. Ubah status form menjadi 'approved'
        $disposal_asset->update(['status' => 'Approved']);

        // 2. Kumpulkan semua ID aset dari detail
        $assetIds = $disposal_asset->detailDisposals->pluck('asset_id');

        // 3. Jika ada ID yang terkumpul, update semua aset sekaligus
        if ($assetIds->isNotEmpty()) {
            Asset::whereIn('id', $assetIds)->update([
                'status' => 'Disposal',
            ]);
        }
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = DisposalAsset::withoutGlobalScope(CompanyScope::class)
                        ->with(['department', 'company'])
                        ->withCount(['detailDisposals'])
                        ->where('company_id', $companyId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('currency', function($disposalAsset) {
                return $disposalAsset->company->currency ?? 'USD';
            })
            ->addColumn('department_name', function($disposalAsset) {
                return $disposalAsset->department->name ?? '-';
            })
            ->addColumn('asset_quantity', function($disposalAsset) {
                return $disposalAsset->detail_disposals_count . ' Asset(s)';
            })
            ->addColumn('action', function ($disposal_assets) {
                return view('components.action-form-buttons', [
                    'model'     => $disposal_assets,
                    'showUrl' => route('disposal-asset.show', $disposal_assets->id),
                    'editUrl' => route('disposal-asset.edit', $disposal_assets->id),
                    'deleteUrl' => route('disposal-asset.destroy', $disposal_assets->id)
                ])->render();
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('detail_disposals_count', function ($query, $keyword) {
                if ($keyword !== null && $keyword !== '') {
                    $n = (int) $keyword;
                    if ($n === 0) {
                        $query->whereDoesntHave('detailDisposals');
                    } else {
                        $query->whereHas('detailDisposals', function ($q) {
                            // no constraints
                        }, '=', $n);
                    }
                }
            })
            ->orderColumn('department_name', function ($query, $order) {
                $query->orderBy(
                    Department::select('name')
                        ->whereColumn('departments.id', 'disposal_assets.department_id'),
                    $order
                );
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function datatablesCanceled(Request $request)
    {
        $companyId = session('active_company_id');

        $query = DisposalAsset::withoutGlobalScope(CompanyScope::class)
                        ->with(['department', 'company'])
                        ->withCount('detailDisposals')
                        ->onlyTrashed()
                        ->where('company_id', $companyId);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('currency', function($disposalAsset) {
                return $disposalAsset->company->currency ?? 'USD';
            })
            ->addColumn('department_name', function($disposalAsset) {
                return $disposalAsset->department->name ?? '-';
            })
            ->addColumn('asset_quantity', function($disposalAsset) {
                return $disposalAsset->detail_disposals_count . ' Asset(s)';
            })
            ->addColumn('action', function ($disposal_assets) {
                return view('components.action-form-canceled-buttons', [
                    'model'     => $disposal_assets,
                    'restoreUrl' => route('disposal-asset.restore', $disposal_assets->id)
                ])->render();
            })
            ->filterColumn('department_name', function($query, $keyword) {
                $query->whereHas('department', function($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('department_name', function ($query, $order) {
                $query->orderBy(
                    Department::select('name')
                        ->whereColumn('departments.id', 'disposal_assets.department_id'),
                    $order
                );
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function datatablesAsset(Request $request)
    {
        $companyId = session('active_company_id');

        $formToIgnoreId = $request->input('form_to_ignore_id');
        $formType = $request->input('form_type');

        $query = Asset::withoutGlobalScope(CompanyScope::class)
                        ->join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
                        ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
                        ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
                        ->join('locations', 'assets.location_id', '=', 'locations.id')
                        ->join('departments', 'assets.department_id', '=', 'departments.id')
                        ->join('companies', 'assets.company_id', '=', 'companies.id')
                        ->where('assets.status', '!=', 'Sold')
                        ->where('assets.status', '!=', 'Onboard')
                        ->where('assets.status', '!=', 'Disposal')
                        ->where('assets.company_id', $companyId)
                        ->whereDoesntHave('detailDisposals.disposalAsset', function ($q) use ($formType, $formToIgnoreId) {
                            $q->where('status', 'Waiting');
                            if ($formType === 'disposal' && $formToIgnoreId) { 
                                $q->where('id', '!=', $formToIgnoreId);
                            }
                        })
                        ->whereDoesntHave('detailTransfers.transferAsset', function ($q) use ($formType, $formToIgnoreId) {
                            $q->where('status', 'Waiting');
                            if ($formType === 'transfer' && $formToIgnoreId) {
                                $q->where('id', '!=', $formToIgnoreId);
                            }
                        })
                        ->select([
                            'assets.*',
                            'asset_names.name as asset_name_name',
                            'asset_classes.obj_acc as asset_class_obj',
                            'locations.name as location_name',
                            'departments.name as department_name',
                            'companies.currency as currency_code',
                        ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('currency', function($asset) {
                return $asset->currency_code ?? 'USD';
            })
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

    public function getAssetsByIds(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([]);
        }

        $companyId = session('active_company_id');

        // Gunakan query yang sama dengan DataTables Anda untuk konsistensi data
        $query = Asset::withoutGlobalScope(CompanyScope::class)
                        ->join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
                        ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
                        ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
                        ->join('locations', 'assets.location_id', '=', 'locations.id')
                        ->join('departments', 'assets.department_id', '=', 'departments.id')
                        ->join('companies', 'assets.company_id', '=', 'companies.id') // Join untuk currency
                        ->whereIn('assets.id', $ids) // Ambil berdasarkan ID yang diminta
                        ->where('assets.company_id', $companyId)
                        ->select([
                            'assets.*',
                            'asset_names.name as asset_name_name',
                            'asset_classes.obj_acc as asset_class_obj',
                            'locations.name as location_name',
                            'departments.name as department_name',
                            'companies.currency as currency_code' // Ambil currency code
                        ]);

        // keyBy('id') penting agar mudah dicari di JavaScript
        $assets = $query->get()->keyBy('id'); 

        return response()->json($assets);
    }

    public function exportPdf(DisposalAsset $disposal_asset)
    {
        $disposal_asset->load(
            'department', 
            'detailDisposals', 
            'approvals.user',
            'company'
        );

        $sumQuantity = $disposal_asset->detailDisposals->sum(function($detail) {
            return $detail->asset?->quantity ?? 0;
        });

        $totalNjabPrimary = $disposal_asset->detailDisposals->sum('njab');

        $totalNjabUsd = 0;
        $totalNjabIdr = 0;

        $activeCompany = Company::find(session('active_company_id'));

        if($activeCompany->currency === 'USD') {
            $totalNjabUsd = $totalNjabPrimary;
            // Hitung total konversi ke IDR
            $totalNjabIdr = $disposal_asset->detailDisposals->sum(function($detail) {
                return ($detail->kurs ?? 0) * ($detail->njab ?? 0);
            });
        } elseif ($activeCompany->currency === 'IDR') {
            $totalNjabIdr = $totalNjabPrimary;
            // Hitung total konversi ke USD
            $totalNjabUsd = $disposal_asset->detailDisposals->sum(function($detail) {
                // Hindari pembagian dengan nol
                if ($detail->kurs && $detail->kurs != 0) {
                    return ($detail->njab ?? 0) / $detail->kurs;
                }
                return 0;
            });
            $totalNjabUsd = round($totalNjabUsd);
        }

        $pdf = Pdf::loadView('disposal-asset.pdf', [
            'disposal_asset'    => $disposal_asset,
            'sumQuantity'       => $sumQuantity,
            'totalNjabUsd'      => $totalNjabUsd,
            'totalNjabIdr'      => $totalNjabIdr
        ]);

        $pdf->setPaper('a4', 'portrait');

        $safeFilename = str_replace('/', '-', $disposal_asset->form_no);
        
        return $pdf->stream('Disposal-Asset-' . $safeFilename  . '.pdf');
    }
}