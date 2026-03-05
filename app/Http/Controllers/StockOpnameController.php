<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOpnameSession;
use App\Models\AssetName;
use App\Models\Asset;
use App\Models\Location;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('stock-opname.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('is-admin');

        $so_exist = StockOpnameSession::where('company_id', session('active_company_id'))->where('status', 'Open')->exists();

        if ($so_exist) {
            return redirect()->route('stock-opname.index')->with('error', 'Stock Opname sedang berjalan!');
        }

        return view('stock-opname.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('is-admin');

        $so_exist = StockOpnameSession::where('company_id', session('active_company_id'))->where('status', 'Open')->exists();

        if ($so_exist) {
            return redirect()->route('stock-opname.index')->with('error', 'Stock Opname sedang berjalan!');
        }

        $companyId = $request->input('company_id');

        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stock_opname_sessions')->where('company_id', $companyId)
            ],
            'description' => 'max:255',
            'start_date' => 'required | date',
            'company_id' => 'required',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $request['status'] = 'Open';
                $request['created_by'] = Auth::user()->id;

                // Simpan ke variabel agar bisa ambil $session->id
                $session = StockOpnameSession::create($request->all());

                DB::table('stock_opname_details')->insertUsing(
                    ['so_session_id', 'asset_id', 'status', 'system_location_id', 'system_user', 'system_condition', 'created_at', 'updated_at'],
                    DB::table('assets')
                        ->select([
                            DB::raw($session->id),
                            'id',
                            DB::raw("'Missing'"),
                            'location_id',
                            'user',
                            'status',
                            DB::raw('CURRENT_TIMESTAMP'),
                            DB::raw('CURRENT_TIMESTAMP')
                        ])
                        ->where('company_id', $request->company_id)
                        ->whereNull('deleted_at')
                        ->whereNotIn('status', ['Sold', 'Disposal'])
                );

                return redirect()->route('stock-opname.index')->with('success', 'Data berhasil ditambah');
            });
        } catch (\Exception $e) {
            return redirect()->route('stock-opname.index')->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOpnameSession $stock_opname)
    {
        $stock_opname->load('createdBy', 'company');

        // Hitung Found/Missing dalam 1 query groupBy
        $statusCounts = $stock_opname->soDetails()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => $statusCounts->sum(),
            'found' => $statusCounts->get('Found', 0),
            'missing' => $statusCounts->get('Missing', 0),
        ];

        $assetNames = AssetName::orderBy('name')->pluck('name', 'id');

        return view('stock-opname.show', [
            'stockOpnameSession' => $stock_opname,
            'stats' => $stats,
            'assetNames' => $assetNames,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOpnameSession $stock_opname)
    {
        Gate::authorize('is-admin');

        return view('stock-opname.edit', ['stock_opname' => $stock_opname]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockOpnameSession $stock_opname)
    {
        Gate::authorize('is-admin');

        $companyId = $stock_opname->company_id;

        $validatedData = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stock_opname_sessions')->ignore($stock_opname->id)->where('company_id', $companyId)
            ],
            'description' => 'max:255',
            'status' => 'required',
            'start_date' => 'required | date',
        ]);

        $stock_opname->update($validatedData);

        return redirect()->route('stock-opname.index')->with('success', 'Data berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOpnameSession $stock_opname)
    {
        Gate::authorize('is-admin');

        $stock_opname->delete();

        return redirect()->route('stock-opname.index')->with('success', 'Data berhasil dihapus!');
    }

    public function scan(Request $request)
    {
        Gate::authorize('is-admin');
        return view('stock-opname.scan');
    }

    public function datatables(Request $request)
    {
        $query = StockOpnameSession::with(['createdBy']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('created_by_name', function ($stockOpnameSession) {
                return $stockOpnameSession->createdBy->name ?? '-';
            })
            ->addColumn('action', function ($stockOpnameSession) {
                return view('components.action-form-buttons', [
                    'model'     => $stockOpnameSession,
                    'showUrl'   => route('stock-opname.show', $stockOpnameSession->id),
                    'editUrl'   => route('stock-opname.edit', $stockOpnameSession->id),
                    'deleteUrl' => route('stock-opname.destroy', $stockOpnameSession->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function detailsDatatables(Request $request, StockOpnameSession $stock_opname)
    {
        $query = $stock_opname->soDetails()
            ->with([
                'asset.assetName',
                'systemLocation',
                'actualLocation',
            ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('asset_number', fn($d) => $d->asset?->asset_number ?? '-')
            ->addColumn('asset_name', fn($d) => $d->asset?->assetName?->name ?? '-')
            ->addColumn('asset_description', fn($d) => $d->asset?->description ?? '-')
            ->addColumn('system_location_name', fn($d) => $d->systemLocation?->name ?? '-')
            ->addColumn('actual_location_name', fn($d) => $d->actualLocation?->name ?? '-')
            ->editColumn('mark', fn($d) => $d->mark ?? false)
            ->addColumn('status_badge', function ($d) {
                $color = match ($d->status) {
                    'Found' => 'green',
                    'Missing' => 'red',
                    default => 'gray',
                };
                return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800 dark:bg-{$color}-900 dark:text-{$color}-300\">{$d->status}</span>";
            })
            // Filter per kolom — dipanggil saat DataTable kirim search[columns][i][search][value]
            ->filterColumn('asset_number', function ($query, $keyword) {
                $query->whereHas('asset', fn($q) => $q->where('asset_number', 'like', "%{$keyword}%"));
            })
            ->filterColumn('asset_name', function ($query, $keyword) {
                $query->whereHas('asset.assetName', fn($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->filterColumn('system_location_name', function ($query, $keyword) {
                $query->whereHas('systemLocation', fn($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->filterColumn('actual_location_name', function ($query, $keyword) {
                $query->whereHas('actualLocation', fn($q) => $q->where('name', 'like', "%{$keyword}%"));
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('status', $keyword);
            })
            ->filterColumn('system_condition', function ($query, $keyword) {
                $query->where('system_condition', 'like', "%{$keyword}%");
            })
            ->filterColumn('actual_condition', function ($query, $keyword) {
                $query->where('actual_condition', 'like', "%{$keyword}%");
            })
            ->filterColumn('system_user', function ($query, $keyword) {
                $query->where('system_user', 'like', "%{$keyword}%");
            })
            ->filterColumn('actual_user', function ($query, $keyword) {
                $query->where('actual_user', 'like', "%{$keyword}%");
            })
            ->orderColumn('asset_number', function ($query, $order) {
                $query->orderBy(
                    Asset::select('asset_number')
                        ->whereColumn('assets.id', 'stock_opname_details.asset_id')
                        ->limit(1),
                    $order
                );
            })
            ->orderColumn('asset_name', function ($query, $order) {
                $query->orderBy(
                    AssetName::select('asset_names.name')
                        ->join('assets', 'asset_names.id', '=', 'assets.asset_name_id')
                        ->whereColumn('assets.id', 'stock_opname_details.asset_id')
                        ->limit(1),
                    $order
                );
            })
            ->orderColumn('system_location_name', function ($query, $order) {
                $query->orderBy(
                    Location::select('locations.name')
                        ->whereColumn('locations.id', 'stock_opname_details.system_location_id')
                        ->limit(1),
                    $order
                );
            })
            ->orderColumn('actual_location_name', function ($query, $order) {
                $query->orderBy(
                    Location::select('locations.name')
                        ->whereColumn('locations.id', 'stock_opname_details.actual_location_id')
                        ->limit(1),
                    $order
                );
            })
            ->rawColumns(['status_badge'])
            ->toJson();
    }
}
