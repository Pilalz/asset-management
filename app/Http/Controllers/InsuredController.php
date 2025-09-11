<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisterAsset;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;

class InsuredController extends Controller
{
    public function index()
    {
        return view('insured.index');
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = RegisterAsset::withoutGlobalScope(CompanyScope::class)
                          ->select('register_assets.*');

        $query->where('register_assets.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->toJson();
    }
}
