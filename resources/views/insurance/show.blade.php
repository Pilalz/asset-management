@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('insurance.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Insurance
                    </a>
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

        <div class="flex gap-2">
            <a href="" type="button" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-sm text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700">
                <svg class="w-4 h-4 me-2 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                </svg>
                Claim
            </a>

            <a href="{{ route('insurance.edit', $insurance->id) }}" type="button" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-sm text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700">
                <svg class="w-4 h-4 me-2 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 21">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                </svg>
                Edit
            </a>
        </div>
    </div>

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Insurance Information </label>
            <table class="mb-4">
                <th>
                    <tr>
                        <td>Polish No.</td>
                        <td>:</td>
                        <td>{{ $insurance->polish_no }}</td>
                    </tr>
                    <tr>
                        <td>Start Date</td>
                        <td>:</td>
                        <td>{{ $insurance->start_date ?? "-" }}</td>
                    </tr>
                    <tr>
                        <td>End Date</td>
                        <td>:</td>
                        <td>{{ $insurance->end_date ?? "-" }}</td>
                    </tr>
                    <tr>
                        <td>Instance Name</td>
                        <td>:</td>
                        <td>{{ $insurance->instance_name ?? "-" }}</td>
                    </tr>
                    <tr>
                        <td>Annual Premium</td>
                        <td>:</td>
                        <td>{{ $insurance->annual_premium ?? "-" }}</td>
                    </tr>
                    <tr>
                        <td>Schedule</td>
                        <td>:</td>
                        <td>{{ $insurance->schedule ?? "-" }}</td>
                    </tr>
                    <tr>
                        <td>Next Payment</td>
                        <td>:</td>
                        <td>{{ $insurance->next_payment ?? "-" }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td>{{ $insurance->status ?? "-" }}</td>
                    </tr>
                </th>
            </table>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Data </label>
                <div class="border-2 border-black rounded-lg p-4">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-2 py-3">Asset Number</th>
                                <th scope="col" class="px-2 py-3">Asset Name</th>
                                <th scope="col" class="px-2 py-3">Description</th>
                                <th scope="col" class="px-2 py-3">ID Pareto</th>
                                <th scope="col" class="px-2 py-3">No. Unit</th>
                                <th scope="col" class="px-2 py-3">No. Mesin</th>
                                <th scope="col" class="px-2 py-3">No. Engine</th>
                                <th scope="col" class="px-2 py-3">Tahun Akuisisi</th>
                                <th scope="col" class="px-2 py-3">Location</th>
                            </tr>
                        </thead>
                        <tbody id="asset-data-body">
                            @foreach($insurance->detailInsurances as $detail)
                                <tr>
                                    <td class="p-4">{{ $detail->asset_number ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->assetName->name ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->description ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->pareto ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->unit_no ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->sn_chassis ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->sn_engine ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->capitalized_date->format('Y') }}</td>
                                    <td class="p-4">{{ $detail->originLocation->name ?? "-" }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div> 
            
            <div class="flex gap-2 content-center">
                <a href="{{ route('insurance.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Back</a>
            </div>
        </div>
    </div>
@endsection