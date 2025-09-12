@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('transfer-asset.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Action
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('transfer-asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Transfer Asset</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Create</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">

            <div class="mb-5 flex content-center">
                <label class="flex w-40 text-sm font-medium text-gray-900 dark:text-white">Nomor Formulir </label>
                <span> : </span>
                <p class="flex ml-1 text-sm text-gray-900">{{ $register_asset->form_no }}</p>
            </div>

            <div class="mb-5 flex content-center">
                <label class="flex w-40 text-sm font-medium text-gray-900 dark:text-white">Department </label>
                <span> : </span>
                <p class="flex ml-1 text-sm text-gray-900">{{ $register_asset->department->name }}</p>
            </div>

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
                            @foreach($register_asset->detailRegisters as $detail)
                                <tr>
                                    <td class="p-4">{{ $detail->asset?->asset_number ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->assetName->name ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->description ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->pareto ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->unit_no ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->sn_chassis ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->sn_engine ?? "-" }}</td>
                                    <td class="p-4">{{ $detail->asset?->capitalized_date->format('Y') }}</td>
                                    <td class="p-4">{{ $detail->originLocation->name ?? "-" }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>           
        </div>
    </div>
@endsection