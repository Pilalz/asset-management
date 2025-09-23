<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Insurance;
use App\Models\Asset;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            'schedule' => 'nullable|integer|min:1|max:12',
            'company_id'  => 'required',

            //Validasi Detail Asset
            'asset_ids'     => 'required|string',
        ]);

        $assetIds = explode(',', $validated['asset_ids']);

        $startDate = Carbon::parse($validated['start_date']);
        $scheduleMonths = (int) $validated['schedule'];
        $nextPaymentDate = $startDate->addMonths($scheduleMonths);

        try {
            DB::transaction(function () use ($validated, $assetIds, $nextPaymentDate) {

                $validated['next_payment'] = $nextPaymentDate;
                $validated['status'] = 'Active';

                $insurance = Insurance::create($validated);

                $insurance->detailInsurances()->attach($assetIds);

            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('insurance.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit(Insurance $insurance)
    {
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

            //Validasi Detail Asset
            'asset_ids'      => 'required|string',
        ]);

        $assetIds = explode(',', $validated['asset_ids']);

        try {
            DB::transaction(function () use ($validated, $insurance, $assetIds) {
                
                $startDate = Carbon::parse($validated['start_date']);
                $scheduleMonths = (int) $validated['schedule'];
                $validated['next_payment'] = $startDate->addMonths($scheduleMonths);
                
                $insurance->update($validated);

                $insurance->detailInsurances()->sync($assetIds);

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
                          ->select('insurances.*');

        $query->where('insurances.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($insurance) {
                return view('components.action-buttons-3-buttons', [
                    'model'     => $insurance,
                    'showUrl' => route('insurance.show', $insurance->id),
                    'editUrl' => route('insurance.edit', $insurance->id),
                    'deleteUrl' => route('insurance.destroy', $insurance->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
