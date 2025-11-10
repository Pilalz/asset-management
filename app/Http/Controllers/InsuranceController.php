<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Insurance;
use App\Models\Asset;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class InsuranceController extends Controller
{
    public function index()
    {
        return view('insurance.index');
    }

    public function show(Insurance $insurance)
    {
        $insurance->load('detailInsurances');

        return view('insurance.show', compact('insurance'));
    }

    public function create()
    {
        Gate::authorize('is-admin');

        return view('insurance.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'polish_no' => 'required|string|max:255|unique:insurances,polish_no',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'instance_name' => 'nullable|max:255',
            'annual_premium' => 'nullable',
            'company_id'  => 'required',

            //Validasi Detail Asset
            'asset_ids'     => 'required|string',
        ]);

        $assetIds = explode(',', $validated['asset_ids']);

        try {
            DB::transaction(function () use ($validated, $assetIds) {
                $validated['status'] = 'Active';
                $insurance = Insurance::create($validated);
                $insurance->detailInsurances()->attach($assetIds);

                $newAssetNames = $insurance->fresh()->detailInsurances()->pluck('asset_number')->toArray();

                activity()
                    ->performedOn($insurance) // subject_type, id
                    ->causedBy(auth()->user()) // causer_id, type
                    ->inLog(session('active_company_id')) 
                    ->withProperty('attributes', ['assets' => $newAssetNames]) // Simpan daftar nama baru
                    ->log("Created asset list for insurance polish '{$insurance->polish_no}'"); // description

            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('insurance.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(Insurance $insurance)
    {
        Gate::authorize('is-admin');
        
        $insurance->load('detailInsurances');

        $selectedAssetIds = $insurance->detailInsurances->pluck('id');

        return view('insurance.edit', compact('insurance', 'selectedAssetIds'));
    }

    public function update(Request $request, Insurance $insurance)
    {
        $validated = $request->validate([
            'polish_no'      => 'required|string|max:255|unique:insurances,polish_no,' . $insurance->id,
            'start_date'     => 'required|date',
            'end_date'       => 'required|date',
            'next_payment'   => 'nullable|date',
            'instance_name'  => 'nullable|max:255',
            'annual_premium' => 'nullable|numeric',
            'schedule'       => 'nullable|integer|min:1|max:12',
            'status'         => 'required|string',

            //Validasi Detail Asset
            'asset_ids'      => 'required|string',
        ]);

        $assetIds = explode(',', $validated['asset_ids']);

        try {
            DB::transaction(function () use ($validated, $insurance, $assetIds) {  
                $oldAssetNames = $insurance->detailInsurances()->pluck('asset_number')->toArray();

                $insurance->update($validated);
                $result = $insurance->detailInsurances()->sync($assetIds);

                $newAssetNames = $insurance->fresh()->detailInsurances()->pluck('asset_number')->toArray();

                if (count($result['attached']) > 0 || count($result['detached']) > 0) {
                    activity()
                        ->performedOn($insurance) // subject_type, id
                        ->causedBy(auth()->user()) // causer_id, type
                        ->inLog(session('active_company_id')) 
                        ->withProperty('old', ['assets' => $oldAssetNames]) // Simpan daftar nama lama
                        ->withProperty('attributes', ['assets' => $newAssetNames]) // Simpan daftar nama baru
                        ->log("Updated asset list for insurance polish '{$insurance->polish_no}'"); // description
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating data: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('insurance.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(Insurance $insurance)
    {
        $insurance->delete();

        return redirect()->route('insurance.index')->with('success', 'Data berhasil dihapus!');
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Insurance::withoutGlobalScope(CompanyScope::class)
                          ->join('companies', 'insurances.company_id', '=', 'companies.id')
                          ->select('insurances.*', 'companies.currency as currency_code',);

        $query->where('insurances.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('currency', function($insurance) {
                return $insurance->currency_code ?? 'USD';
            })
            ->addColumn('action', function ($insurance) {
                return view('components.action-buttons-3-buttons', [
                    'model'     => $insurance,
                    'showUrl' => route('insurance.show', $insurance->id),
                    'editUrl' => route('insurance.edit', $insurance->id),
                    'deleteUrl' => route('insurance.destroy', $insurance->id)
                ])->render();
            })
            ->filterColumn('status', function($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('insurances.status', '=', $keyword);
                }
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
