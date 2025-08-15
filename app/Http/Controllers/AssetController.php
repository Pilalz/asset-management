<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Imports\AssetsImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Scopes\CompanyScope;

class AssetController extends Controller
{
    public function index()
    {
        return view('asset.index');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            Excel::import(new AssetsImport, $request->file('excel_file'));
        } catch (\Exception $e) {
            return redirect()->route('asset.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
        
        return redirect()->route('asset.index')->with('success', 'Data aset berhasil diimpor!');
    }

    public function datatables(Request $request)
    {
        $companyId = session('active_company_id');

        $query = Asset::withoutGlobalScope(CompanyScope::class)
                          ->select('assets.*');

        $query->where('assets.company_id', $companyId);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($asset) {
                return view('components.action-buttons', [
                    'editUrl' => route('asset.edit', $asset->id),
                    'deleteUrl' => route('asset.destroy', $asset->id)
                ])->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
