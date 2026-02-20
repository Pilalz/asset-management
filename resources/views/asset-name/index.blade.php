@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Gaya untuk Light Mode */
            #assetNameTable tbody tr:hover {
                background-color: #F9FAFB !important; /* Tailwind's hover:bg-gray-50 */
            }

            /* Gaya untuk Dark Mode */
            .dark #assetNameTable tbody tr:hover {
                background-color: #374151 !important; /* Tailwind's dark:hover:bg-gray-700 (contoh) */
            }

            /* Matiin Outline */
            #assetNameTable thead tr th:hover {
                outline: none !important;
            }

            /* Menghapus background bawaan dari kolom yang diurutkan */
            table.dataTable tbody tr > .sorting_1,
            table.dataTable tbody tr > .sorting_2,
            table.dataTable tbody tr > .sorting_3 {
                background-color: inherit !important;
            }

            .dark .dt-search,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:hover,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:active,
            .dark div.dt-container .dt-paging .dt-paging-button,
            .dark div.dt-container .dt-paging .ellipsis{
                color: #e4e6eb !important;
            }

            html.dark .dt-container .dt-paging .dt-paging-button.current:hover{
                color: white !important;
            }

            div.dt-container select.dt-input {
                padding: 4px 25px 4px 4px;
            }

            select.dt-input option{
                text-align: center !important;
            }
        </style>
    @endpush
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('asset-name.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Asset
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ route('asset-name.index') }}"
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Asset
                            Name</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">List</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex gap-2">
            @can('is-admin')
                <div class="hidden sm:block">
                    <a href="{{ route('asset-name.create') }}" type="button"
                        class="inline-flex items-center text-green-500 bg-white border border-green-300 focus:outline-none hover:bg-green-100 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 dark:bg-green-600 dark:text-gray-200 dark:border-gray-400 dark:hover:bg-green-500 dark:hover:border-green-400">
                        <span class="sr-only">New Data</span>
                        New Data
                        <svg class="w-4 h-4 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-7 7V5" />
                        </svg>
                    </a>
                </div>
                <div>
                    <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction"
                        class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-400 dark:hover:bg-gray-500 dark:hover:border-gray-400"
                        type="button">
                        <span class="sr-only">Action button</span>
                        Action
                        <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownAction"
                        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-44 dark:bg-gray-700 dark:divide-gray-600">
                        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownActionButton">
                            <li class="sm:hidden">
                                <div class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <a href="{{ route('asset-name.create') }}" type="button"
                                        class="inline-flex items-center me-2">
                                        <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 12h14m-7 7V5" />
                                        </svg>
                                        New Data
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <!-- Modal toggle -->
                                    <button data-modal-target="import-modal" data-modal-toggle="import-modal"
                                        class="inline-flex items-center me-2" type="button">
                                        <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 12V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-4m5-13v4a1 1 0 0 1-1 1H5m0 6h9m0 0-2-2m2 2-2 2" />
                                        </svg>
                                        Import Data
                                    </button>
                                </div>
                            </li>
                            <li>
                                <div class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <a href="{{ route('asset-name.export') }}" class="inline-flex items-center me-2">
                                        <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />
                                        </svg>
                                        Export Excel
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            @endcan

            <!-- Main modal -->
            <div id="import-modal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-[99999] justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div
                        class="relative bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-4 md:p-5 border-b border-gray-100 rounded-t dark:border-gray-700">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Import Data Asset Name
                            </h3>
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-700 dark:hover:text-white"
                                data-modal-hide="import-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5">
                            <form id="importForm" action="{{ route('asset-name.import') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <div class="flex items-center justify-center w-full">
                                        <label for="excel_file"
                                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-colors">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-10 h-10 mb-3 text-gray-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                </svg>
                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                                        class="font-semibold">Click to upload</span> or drag and drop</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">XLSX or XLS (MAX. 10MB)
                                                </p>
                                            </div>
                                            <input id="excel_file" name="excel_file" type="file" accept=".xlsx, .xls"
                                                class="hidden" required />
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-6 flex justify-center">
                                    <a href="{{ asset('template/TemplateAssetName.xlsx') }}"
                                        class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 15v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2m-8 1V4m0 12-4-4m4 4 4-4" />
                                        </svg>
                                        Download Excel Template
                                    </a>
                                </div>
                                <button type="submit" id="btnSubmit"
                                    class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-xl text-sm px-5 py-3 text-center dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800 transition-colors shadow-lg shadow-indigo-200 dark:shadow-indigo-900/30">
                                    Import Data
                                </button>
                                <div id="importError" class="text-red-500 text-sm mt-3 hidden text-center"></div>
                            </form>

                            <div id="progressContainer"
                                class="hidden w-full bg-gray-200 rounded-full h-4 mb-4 mt-4 dark:bg-gray-700">
                                <div id="progressBar"
                                    class="bg-indigo-600 h-4 rounded-full text-xs font-medium text-indigo-100 text-center p-0.5 leading-none transition-all duration-300"
                                    style="width: 0%"> 0%</div>
                            </div>

                            <p id="statusMessage" class="text-sm text-gray-600 dark:text-gray-300 hidden text-center mt-2">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="shadow-md rounded-lg bg-white p-4 dark:bg-gray-800">
            <table id="assetNameTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-100">
                <tr>
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Asset Sub Class</th>
                        <th scope="col" class="px-6 py-3">Asset Name</th>
                        <th scope="col" class="px-6 py-3">Asset Grouping</th>
                        <th scope="col" class="px-6 py-3">Commercial Life</th>
                        <th scope="col" class="px-6 py-3">Fiscal Life</th>
                        @can('is-admin')
                            <th scope="col" class="px-6 py-3">Actions</th>
                        @else
                            <th scope="col" class="px-6 py-3"></th>
                        @endcan
                </tr>
                <tr id="filter-row">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.assetSubClassesForFilterData = @json($assetSubClassesForFilter ?? []);
    </script>
    @vite('resources/js/pages/assetName.js')
@endpush