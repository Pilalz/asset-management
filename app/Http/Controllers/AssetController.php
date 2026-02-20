<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Depreciation;
use App\Models\Location;
use App\Models\Department;
use App\Models\AssetClass;
use App\Models\AssetName;
use App\Models\Company;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use App\Imports\AssetsImport;
use App\Exports\AssetsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function index()
    {
        $companyId = session('active_company_id');

        $lastMonth = Carbon::now()->subMonthNoOverflow();
        $year = $lastMonth->year;
        $month = $lastMonth->month;
        $endOfMonthDay = $lastMonth->endOfMonth()->day;

        $cacheKey = 'has_assets_pending_depreciation_' . $companyId . '_' . $year . '-' . $month; // Saya ganti nama key biar sesuai konteks
        $lockKey = "lock_assets_not_depreciated_{$companyId}_{$year}-{$month}";
        $lock = Cache::lock($lockKey, 5);

        // Default false jika gagal lock
        $hasPendingDepreciation = false;

        if ($lock->get()) {
            try {
                // Kita simpan BOOLEAN (true/false) saja, bukan Collection data asetnya.
                $hasPendingDepreciation = Cache::remember(
                    $cacheKey,
                    now()->addHour(),
                    function () use ($companyId, $year, $month, $endOfMonthDay, $lastMonth) {
                        // Gunakan exists() agar database cuma balikin 1 atau 0. Sangat ringan.
                        return Asset::query()
                            ->where('asset_type', 'FA')
                            ->whereNotIn('assets.status', ['Sold', 'Onboard', 'Disposal'])
                            ->where('commercial_nbv', '>', 0)
                            ->where('start_depre_date', '<=', $lastMonth->endOfMonth())
                            ->where('company_id', $companyId)
                            ->where(function ($q) use ($year, $month, $endOfMonthDay) {
                            // Grouping logic whereDoesntHave biar lebih rapi dan optimal
                            $q->whereDoesntHave('depreciations', function ($query) use ($year, $month, $endOfMonthDay) {
                                $query->where('type', 'commercial')
                                    ->whereYear('depre_date', $year)
                                    ->whereMonth('depre_date', $month)
                                    ->whereDay('depre_date', $endOfMonthDay);
                            })
                                ->orWhereDoesntHave('depreciations', function ($query) use ($year, $month, $endOfMonthDay) {
                                $query->where('type', 'fiscal')
                                    ->whereYear('depre_date', $year)
                                    ->whereMonth('depre_date', $month)
                                    ->whereDay('depre_date', $endOfMonthDay);
                            });
                        })
                            ->exists();
                    }
                );
            } finally {
                $lock->release();
            }
        } else {
            // Ambil dari cache, default false
            $hasPendingDepreciation = Cache::get($cacheKey, false);
        }

        // Filter list (ini ringan karena cuma ambil ID dan Name)
        $assetNamesForFilter = AssetName::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)->orderBy('name', 'asc')->get(['id', 'name']);

        $locationsForFilter = Location::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)->orderBy('name', 'asc')->get(['id', 'name']);

        $departmentsForFilter = Department::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)->orderBy('name', 'asc')->get(['id', 'name']);

        // Kirim variable boolean baru ke view
        return view('asset.fixed.index', compact('hasPendingDepreciation', 'assetNamesForFilter', 'locationsForFilter', 'departmentsForFilter'));
    }

    public function show(Request $request, Asset $asset)
    {
        $locs = $asset->locationHistories()->with('location')->get()->map(function ($item) {
            $item->setAttribute('log_type', 'Lokasi');
            $item->setAttribute('display_name', $item->location->name);
            return $item;
        });

        $users = $asset->userHistories()->with('user')->get()->map(function ($item) {
            $item->setAttribute('log_type', 'User');
            $item->setAttribute('display_name', $item->user);
            return $item;
        });

        // Combine, sort by date desc, and group by formatted date (minute precision)
        $timeline = $locs->concat($users)
            ->sortByDesc('start_date')
            ->groupBy(function ($item) {
                return $item->start_date->format('d M Y H:i');
            });

        $year = $request->input('year', now()->year);
        $startDate = Carbon::create($year, 1, 1)->startOfMonth();
        $endDate = Carbon::create($year, 12, 1)->endOfMonth();

        $schedules = Depreciation::where('asset_id', $asset->id)
            ->whereBetween('depre_date', [$startDate, $endDate])
            ->orderBy('depre_date')
            ->get();

        $pivotedData = [];
        $pivotedData[$asset->id] = [
            'master_data' => $asset,
            'schedule' => []
        ];

        foreach ($schedules as $schedule) {
            $monthKey = Carbon::parse($schedule->depre_date)->format('Y-m');
            $pivotedData[$asset->id]['schedule'][$monthKey] = (object)[
                'monthly_depre' => $schedule->monthly_depre,
                'accumulated_depre' => $schedule->accumulated_depre,
                'book_value' => $schedule->book_value,
            ];
        }

        $months = [];
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($period as $date) {
            $months[$date->format('Y-m')] = $date->format('M-y');
        }

        return view('asset.fixed.show', [
            'pivotedData' => $pivotedData,
            'months' => $months,
            'selectedYear' => $year,
            'asset' =>$asset,
            'timeline' =>$timeline
        ]);
    }

    public function create()
    {
        Gate::authorize('is-admin');

        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        $activeCompany = Company::find(session('active_company_id'));

        return view('asset.fixed.create', compact('locations', 'departments', 'assetclasses', 'activeCompany'));
    }

    public function store(Request $request)
    {
        Gate::authorize('is-admin');

        $companyId = session('active_company_id');

        $validatedData = $request->validate([
            'asset_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets')->where('company_id', $companyId)
            ],
            'asset_code'       => 'string',
            'asset_name_id'    => 'required|exists:asset_names,id',
            'description'      => 'required|string|max:255',
            'detail'           => 'nullable|string|max:255',
            'pareto'           => 'nullable|string|max:255',
            'unit_no'          => 'nullable|string|max:255',
            'user'             => 'nullable|string|max:255',
            'sn'               => 'nullable|string|max:255',
            'sn_chassis'       => 'nullable|string|max:255',
            'sn_engine'        => 'nullable|string|max:255',
            'production_year'  => 'nullable|integer',
            'po_no'            => 'required|string|max:255',
            'location_id'      => 'required|exists:locations,id',
            'department_id'    => 'required|exists:departments,id',
            'quantity'         => 'required|integer|min:1',

            'status'           => 'required|string',
            'capitalized_date' => 'required|date',
            'start_depre_date' => 'required|date',

            'acquisition_value' => 'required',
            'current_cost'      => 'required',
            'commercial_nbv'    => 'required',
            'fiscal_nbv'        => 'required',
            'remaks'            => 'nullable|string',

            'commercial_accum_depre' => 'nullable',
            'fiscal_accum_depre'     => 'nullable',
        ]);

        $assetNameID = $validatedData['asset_name_id'];
        $assetName = AssetName::find($assetNameID);

        $validatedData['asset_type'] = "FA";

        $validatedData['commercial_useful_life_month'] = $assetName->commercial * 12;
        $validatedData['fiscal_useful_life_month'] = $assetName->fiscal * 12;

        $validatedData['commercial_accum_depre'] = $request->input('commercial_accum_depre', 0);
        $validatedData['fiscal_accum_depre']     = $request->input('fiscal_accum_depre', 0);

        $validatedData['company_id'] = $companyId;

        Asset::create($validatedData);

        $cacheKey = 'assets_not_depreciated_' . $companyId . '_' . now()->year . '-' . now()->month;
        Cache::forget($cacheKey);

        return redirect()->route('asset.index')->with('success', 'Asset created successfully!');
    }

    public function edit(Asset $asset)
    {
        Gate::authorize('is-admin');

        $locations = Location::all();
        $departments = Department::all();
        $assetclasses = AssetClass::all();

        $asset->load('depreciations');

        return view('asset.fixed.edit', compact('asset', 'locations', 'departments', 'assetclasses'));
    }

    public function update(Request $request, Asset $asset)
    {
        Gate::authorize('is-admin');

        $validatedData = $request->validate([
            'asset_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assets')->ignore($asset->id)->where('company_id', $asset->company_id)
            ],
            'asset_name_id'     => 'required|exists:asset_names,id',
            'description'       => 'required|string|max:255',
            'detail'            => 'max:255',
            'pareto'            => 'max:255',
            'unit_no'           => 'max:255',
            'user'              => 'nullable',
            'sn_chassis'        => 'max:255',
            'sn_engine'         => 'max:255',
            'production_year'   => 'max:255',
            'po_no'             => 'required|string|max:255',
            'location_id'       => 'required|exists:locations,id',
            'department_id'     => 'required|exists:departments,id',
            'quantity'          => 'required',
            'capitalized_date'  => 'required|date',
            'start_depre_date'  => 'required|date',
            'acquisition_value' => 'required',
            'current_cost'      => 'required',
            'commercial_nbv'    => 'required',
            'fiscal_nbv'        => 'required',
            'remaks'            => 'nullable',
            'status'            => 'required|string',
        ]);

        $assetNameID = $validatedData['asset_name_id'];
        $assetName = AssetName::find($assetNameID);

        $validatedData['commercial_useful_life_month'] = $assetName->commercial * 12;
        $validatedData['fiscal_useful_life_month'] = $assetName->fiscal * 12;

        $dataToUpdate = $validatedData;

        $asset->update($dataToUpdate);

        $cacheKey = 'assets_not_depreciated_' . $asset->company_id . '_' . Carbon::now()->year . '-' . Carbon::now()->month;
        Cache::forget($cacheKey);

        return redirect()->route('asset.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Gate::authorize('is-admin');

        $asset = Asset::findOrFail($id);

        if ($asset->detailTransfers()->whereHas('transferAsset', function ($query) {
                $query->where('status', '!=', 'Approved');
        })->exists()) {
            return back()->with('error', 'Gagal dihapus! Aset ini sedang dalam proses transaksi Transfer Asset yang belum selesai.');
        }

        if ($asset->detailDisposals()->whereHas('disposalAsset', function ($query) {
                $query->where('status', '!=', 'Approved');
        })->exists()) {
            return back()->with('error', 'Gagal dihapus! Aset ini sedang dalam proses transaksi Disposal Asset yang belum selesai.');
        }

        $asset->delete();

        return redirect()->back()->with('success', 'Aset berhasil dipindahkan ke sampah (Trash).');
    }

    public function importExcel(Request $request)
    {
        Gate::authorize('is-admin');

        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            Excel::import(new AssetsImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('asset.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }

        return redirect()->route('asset.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function exportExcel()
    {
        $companyName = session('active_company_id');
        $companyName = Company::where('id', $companyName)->first();
        $fileName = 'Fixed-Asset-' . $companyName->name . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new AssetsExport, $fileName);
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Asset::withoutGlobalScope(CompanyScope::class)
            ->join('asset_names', 'assets.asset_name_id', '=', 'asset_names.id')
            ->join('asset_sub_classes', 'asset_names.sub_class_id', '=', 'asset_sub_classes.id')
            ->join('asset_classes', 'asset_sub_classes.class_id', '=', 'asset_classes.id')
            ->join('locations', 'assets.location_id', '=', 'locations.id')
            ->join('departments', 'assets.department_id', '=', 'departments.id')
            ->join('companies', 'assets.company_id', '=', 'companies.id')
            ->where('assets.asset_type', '=', 'FA')
            ->where('assets.status', '!=', 'Sold')
            ->where('assets.status', '!=', 'Onboard')
            ->where('assets.status', '!=', 'Disposal')
            ->where('assets.company_id', $companyId)
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
            ->addColumn('action', function ($asset) {
                $qrContent = Str::isUuid($asset->asset_code)
                    ? route('scan.detail', ['code' => $asset->asset_code])
                    : $asset->asset_code;

                return view('components.action-asset-buttons', [
                    'assetNumber' => $asset->asset_number,
                    'qrContent' => $qrContent,
                    'qrcodeUrl' => route('scan.qr.download', $asset->id),

                    'showUrl' => route('asset.show', $asset->id),
                    'editUrl' => route('asset.edit', $asset->id),
                    'deleteUrl' => route('asset.destroy', $asset->id),
                    'depreUrl' => route('depreciation.depre', $asset->id),
                ])->render();
            })
            ->addColumn('currency', function($asset) {
                return $asset->currency_code ?? 'USD';
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
            ->rawColumns(['action'])
            ->toJson();
    }
}
