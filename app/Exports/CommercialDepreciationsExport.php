<?php

namespace App\Exports;

use App\Models\Depreciation;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CommercialDepreciationsExport implements FromView, ShouldAutoSize
{
    protected $year;

    public function __construct(int $year)
    {
        $this->year = $year;
    }

    /**
     * Mengembalikan view Blade yang akan dirender menjadi Excel.
     */
    public function view(): View
    {
        // Logika untuk mengambil dan mem-pivot data, sama seperti di controller Anda.
        $startDate = Carbon::create($this->year, 1, 1)->startOfMonth();
        $endDate = Carbon::create($this->year, 12, 1)->endOfMonth();

        $schedules = Depreciation::with([
            'asset', 
            'asset.assetName.assetSubClass.assetClass'
        ])
        ->whereBetween('depre_date', [$startDate, $endDate])
        ->where('type', 'commercial')
        ->whereHas('asset', function ($query) {
            $query->where('status', 'Active')->where('asset_type', 'FA');
        })
        ->orderBy('asset_id')->orderBy('depre_date')->get();

        $pivotedData = [];
        foreach ($schedules as $schedule) {
            $assetId = $schedule->asset_id;
            $monthKey = Carbon::parse($schedule->depre_date)->format('Y-m');

            if (!isset($pivotedData[$assetId])) {
                $pivotedData[$assetId] = [
                    'master_data' => $schedule->asset,
                    'schedule' => []
                ];
            }
            $pivotedData[$assetId]['schedule'][$monthKey] = (object)[
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
        
        // Kirim data yang sudah diolah ke file view khusus export
        return view('depreciation.export', [
            'pivotedData' => $pivotedData,
            'months' => $months,
        ]);
    }
}
