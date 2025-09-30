@extends('layouts.main')

@section('content')
    <div class="bg-white flex p-5 text-lg justify-between items-center dark:bg-gray-800 dark:border-b dark:border-gray-700">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Asset
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('assetLVA.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Low Value</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Detail</span>
                    </div>
                </li>
            </ol>
        </nav>

    @can('is-admin')
        <div class="flex gap-2">
            <a href="{{ route('assetLVA.edit', $asset->id) }}" type="button" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-sm text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700">
                <svg class="w-4 h-4 me-2 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 21">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                </svg>
                Edit
            </a>
        </div>
    @endcan
    </div>
    
    <div class="p-5">
        <div class="shadow-md sm:rounded-lg bg-white p-4 dark:bg-gray-800">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Asset Details</h2>
            <div class="flex flex-col sm:flex-row sm:gap-8">
                <div class="w-full sm:w-1/2">
                    <table>
                        <tr>
                            <td>Asset Number</td>
                            <td class="px-2">:</td>
                            <td>{{ $asset->asset_number }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td class="px-2">:</td>
                            <td>{{ $asset->status }}</td>
                        </tr>
                        <tr>
                            <td>Asset Class</td>
                            <td class="px-2">:</td>
                            <td>{{ $asset->assetName?->assetSubClass?->assetClass?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Asset Sub Class</td>
                            <td class="px-2">:</td>
                            <td>{{ $asset->assetName?->assetSubClass?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Asset Name</td>
                            <td class="px-2">:</td>
                            <td>{{ $asset->assetName?->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Obj Acc</td>
                            <td class="px-2">:</td>
                            <td>Direct Ownership : {{ $asset->assetName?->assetSubClass?->assetClass?->obj_acc ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->description == null) ? "-" : $asset->description }}</td>
                        </tr>
                        <tr>
                            <td>Detail</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->detail == null) ? "-" : $asset->detail }}</td>
                        </tr>
                        <tr>
                            <td>Pareto</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->pareto == null) ? "-" : $asset->pareto }}</td>
                        </tr>
                        <tr>
                            <td>Unit No.</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->unit_no == null) ? "-" : $asset->unit_no }}</td>
                        </tr>
                        <tr>
                            <td>SN Chassis</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->sn_chassis == null) ? "-" : $asset->sn_chassis }}</td>
                        </tr>
                    </table>
                </div>
                <div class="w-full sm:w-1/2">
                    <table>
                        <tr>
                            <td>SN Engine</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->sn_engine == null) ? "-" : $asset->sn_engine }}</td>
                        </tr>
                        <tr>
                            <td>Production Year</td>
                            <td class="px-2">:</td>
                            <td>{{ $asset->production_year ? \Carbon\Carbon::parse($asset->production_year)->format('Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>PO No</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->po_no == null) ? "-" : $asset->po_no }}</td>
                        </tr>
                        <tr>
                            <td>Location</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->location->name == null) ? "-" : $asset->location->name }}</td>
                        </tr>
                        <tr>
                            <td>Department</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->department->name == null) ? "-" : $asset->department->name }}</td>
                        </tr>
                        <tr>
                            <td>Quantity</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->quantity == null) ? "-" : $asset->quantity }}</td>
                        </tr>
                        <tr>
                            <td>Capitalized Date</td>
                            <td class="px-2">:</td>
                            <td>{{ $asset->capitalized_date ? \Carbon\Carbon::parse($asset->capitalized_date)->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Acquisition Value</td>
                            <td class="px-2">:</td>
                            <td>{{ '$ ' . number_format($asset->acquisition_value, 0, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Useful Life Month</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->commercial_useful_life_month == null) ? "-" : $asset->commercial_useful_life_month }} Month</td>
                        </tr>
                        <tr>
                            <td>Net Book Value</td>
                            <td class="px-2">:</td>
                            <td>{{ '$ ' . number_format($asset->commercial_nbv, 0, '.', ',') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="shadow-md sm:rounded-lg mt-3 bg-white p-4 dark:bg-gray-800">
            <a href="{{ route('assetLVA.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Back</a>
        </div>
    </div>
@endsection