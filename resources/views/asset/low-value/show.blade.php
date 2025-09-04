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

        <div class="flex gap-2">
            <!-- Modal toggle -->
            <button data-modal-target="import-modal" data-modal-toggle="import-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
            Import Data
            </button>
            <!-- Main modal -->
            <div id="import-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Import Data Asset Class
                            </h3>
                            <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="import-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5">
                            <form action="{{ route('asset.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="excel_file" class="block mb-2 text-sm font-medium text-gray-900">Upload Excel File (.xlsx, .xls)</label>
                                    <input type="file" name="excel_file" id="excel_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" required>
                                </div>   
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">
                                        Pastikan file Excel Anda memiliki header pada baris pertama dengan nama kolom seperti: `id`, `name`.
                                    </p>
                                    <a href="/path/to/template.xlsx" class="text-blue-600 hover:underline">Download Template Excel</a>
                                </div>
                                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                                    Import Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-5">
        <div class="shadow-md sm:rounded-lg bg-white p-4 dark:bg-gray-800">
            <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Asset Details</h2>
            <div class="flex flex-row gap-8">
                <div class="w-1/2">
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
                            <td>Pareto</td>
                            <td class="px-2">:</td>
                            <td>{{ ($asset->pareto == null) ? "-" : $asset->pareto }}</td>
                        </tr>
                    </table>
                </div>
                <div class="w-1/2">
                    <table>
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
                            <td>{{ ($asset->useful_life_month == null) ? "-" : $asset->useful_life_month }} Month</td>
                        </tr>
                        <tr>
                            <td>Net Book Value</td>
                            <td class="px-2">:</td>
                            <td>{{ '$ ' . number_format($asset->net_book_value, 0, '.', ',') }}</td>
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