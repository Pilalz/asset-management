<?php

namespace App\Exports;

use App\Models\Asset;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LVAExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Asset::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'))
                ->where('status', '!=', 'Onboard')
                ->where('status', '!=', 'Disposal')
                ->where('status', '!=', 'Sold')
                ->where('asset_type', 'LVA')
                ->get();
    }

    public function headings(): array
    {
        return [
            'ID Asset',
            'Asset Number',
            'Asset Class',
            'Asset Sub Class',
            'Asset Name',
            'Obj ID',
            'Obj Acc',
            'Status',
            'Deskripsi',
            'Detail',
            'Pareto',
            'Unit No.',
            'SN Chassis',
            'SN Engine',
            'Production Year',
            'PO No.',
            'Location',
            'Department',
            'Quantity',
            'Capitalized Date',
            'Start Depre Date',
            'Acquisition Value',
            'Current Cost',
            'Commercial Useful Life Month',
            'Commercial Accumulate Depre',
            'Commercial Net Book Value',
            'Fiscal Useful Life Month',
            'Fiscal Accumulate Depre',
            'Fiscal Net Book Value',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->id,
            $asset->asset_number,
            $asset->assetName->assetSubClass->assetClass->name,
            $asset->assetName->assetSubClass->name,
            $asset->assetName->name,
            $asset->assetName->assetSubClass->assetClass->obj_id,
            $asset->assetName->assetSubClass->assetClass->obj_acc,
            $asset->status,
            $asset->description,
            $asset->detail,
            $asset->pareto,
            $asset->unit_no,
            $asset->sn_chassis,
            $asset->sn_engine,
            $asset->production_year,
            $asset->po_no,
            $asset->location->name,
            $asset->department->name,
            $asset->quantity,
            $asset->capitalized_date,
            $asset->start_depre_date,
            $asset->acquisition_value,
            $asset->current_cost,
            $asset->commercial_useful_life_month,
            $asset->commercial_accum_depre,
            $asset->commercial_nbv,
            $asset->fiscal_useful_life_month,
            $asset->fiscal_accum_depre,
            $asset->fiscal_nbv,
        ];
    }
}
