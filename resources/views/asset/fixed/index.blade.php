@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            #assetTable tbody tr:hover {
                background-color: #F9FAFB !important;
            }

            .dark #assetTable tbody tr:hover {
                background-color: #374151 !important;
            }

            #assetTable thead tr th:hover {
                outline: none !important;
            }

            table.dataTable tbody tr>.sorting_1,
            table.dataTable tbody tr>.sorting_2,
            table.dataTable tbody tr>.sorting_3 {
                background-color: inherit !important;
            }

            .dark .dt-search,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:hover,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:active,
            .dark div.dt-container .dt-paging .dt-paging-button,
            .dark div.dt-container .dt-paging .ellipsis {
                color: #e4e6eb !important;
            }

            html.dark .dt-container .dt-paging .dt-paging-button.current:hover {
                color: white !important;
            }

            div.dt-container select.dt-input {
                padding: 4px 25px 4px 4px;
            }

            select.dt-input option {
                text-align: center !important;
            }
        </style>
    @endpush
    <div class="bg-white flex p-5 text-lg justify-between border-b border-slate-200 dark:border-gray-700 dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('asset.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Fixed Asset
                    </a>
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
            <div id="depreciation-controls" class="flex items-center gap-4">
                <!-- Depreciation Progress UI -->
                <div id="status-container"
                    class="hidden items-center gap-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 min-w-[320px] shadow-sm transition-all duration-300">
                    <div id="status-icon-container"
                        class="flex items-center justify-center w-9 h-9 min-w-[36px] rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 transition-colors duration-300">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex justify-between items-end mb-1.5">
                            <span id="status-text"
                                class="text-xs font-semibold text-gray-700 dark:text-gray-300 tracking-wide">Menyiapkan...</span>
                            <span id="progress-percentage" class="text-xs font-bold text-gray-900 dark:text-white">0%</span>
                        </div>
                        <div
                            class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden border border-gray-200/50 dark:border-gray-600/50 relative">
                            <div id="progress-bar"
                                class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300 ease-out flex items-center justify-end relative shadow-[0_0_8px_rgba(59,130,246,0.6)]"
                                style="width: 0%">
                                <div class="absolute inset-0 bg-white/20"
                                    style="background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent); background-size: 1rem 1rem; animation: progress-stripes 1s linear infinite;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    @keyframes progress-stripes {
                        0% {
                            background-position: 1rem 0;
                        }

                        100% {
                            background-position: 0 0;
                        }
                    }
                </style>
            </div>

            <div class="flex gap-2">
                @can('is-admin')
                    <div class="hidden sm:block">
                        <a href="{{ route('asset.create') }}" type="button"
                            class="inline-flex items-center text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 transition-colors shadow-sm">
                            <span class="sr-only">New Data</span>
                            New Data
                            <svg class="w-4 h-4 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14m-7 7V5" />
                            </svg>
                        </a>
                    </div>

                    <div>
                        <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction"
                            data-dropdown-placement="bottom-end"
                            class="inline-flex items-center text-gray-700 bg-white border border-slate-300 focus:outline-none hover:bg-slate-50 focus:ring-4 focus:ring-slate-100 font-medium rounded-lg text-sm px-4 py-2 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors shadow-sm"
                            type="button">
                            <span class="sr-only">Action button</span>
                            Actions
                            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                    </div>
                @endcan
                <!-- Dropdown menu -->
                <div id="dropdownAction"
                    class="z-10 hidden bg-white divide-y divide-gray-100 rounded-xl shadow-lg w-56 dark:bg-gray-800 dark:divide-gray-700 my-4 border border-gray-100 dark:border-gray-700">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownActionButton">
                        @if(!$hasPendingDepreciation)

                        @else
                            <li>
                                <a href="javascript:void(0)" data-modal-target="depre-modal" data-modal-toggle="depre-modal"
                                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                    type="button">
                                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-500 dark:text-gray-500 dark:group-hover:text-indigo-400 transition-colors"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 11v5m0 0 2-2m-2 2-2-2M3 6v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Zm2 2v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8H5Z" />
                                    </svg>
                                    Run All Depre
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="javascript:void(0)" data-modal-target="import-modal" data-modal-toggle="import-modal"
                                class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                type="button">
                                <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-500 dark:text-gray-500 dark:group-hover:text-indigo-400 transition-colors"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 12V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-4m5-13v4a1 1 0 0 1-1 1H5m0 6h9m0 0-2-2m2 2-2 2" />
                                </svg>
                                Import Data
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('asset.export') }}" type="button"
                                class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-500 dark:text-gray-500 dark:group-hover:text-indigo-400 transition-colors"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2" />
                                </svg>
                                Export Excel
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" id="print-selected-btn" data-modal-target="print-modal"
                                data-modal-toggle="print-modal"
                                class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors opacity-50 cursor-not-allowed pointer-events-none disabled"
                                disabled>
                                <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-500 dark:text-gray-500 dark:group-hover:text-indigo-400 transition-colors"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                        d="M16.444 18H19a1 1 0 0 0 1-1v-5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h2.556M17 11V5a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v6h10ZM7 15h10v4a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-4Z" />
                                </svg>
                                <span id="print-selected-text">Print Selected (0)</span>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" id="download-selected-btn" data-modal-target="download-modal"
                                data-modal-toggle="download-modal"
                                class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors opacity-50 cursor-not-allowed pointer-events-none disabled"
                                disabled>
                                <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-500 dark:text-gray-500 dark:group-hover:text-indigo-400 transition-colors"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 12V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-4m5-13v4a1 1 0 0 1-1 1H5m0 6h9m0 0-2-2m2 2-2 2" />
                                </svg>
                                <span id="download-selected-text">Download Selected (0)</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main modal -->
            <div id="import-modal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div
                        class="relative bg-white rounded-2xl shadow-2xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                        <!-- Modal header -->
                        <div
                            class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Import Data Fixed Asset
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
                            <form action="{{ route('asset.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <div class="flex items-center justify-center w-full">
                                        <label id="fa-file-upload-label"
                                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-800 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600 transition-colors">
                                            <div id="fa-file-upload-placeholder"
                                                class="flex flex-col items-center justify-center pt-5 pb-6">
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
                                            <div id="fa-file-upload-selected"
                                                class="hidden flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-10 h-10 mb-3 text-green-500" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4" />
                                                </svg>
                                                <p id="fa-file-name-display"
                                                    class="mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300 text-center px-4 break-all">
                                                </p>
                                                <p class="text-xs text-green-500 dark:text-green-400">File selected — click
                                                    to change</p>
                                            </div>
                                            <input id="fa-excel-file-input" name="excel_file" type="file" class="hidden"
                                                accept=".xlsx,.xls" required />
                                        </label>
                                    </div>
                                    <script>
                                        document.getElementById('fa-excel-file-input').addEventListener('change', function () {
                                            var fileName = this.files[0] ? this.files[0].name : null;
                                            if (fileName) {
                                                document.getElementById('fa-file-upload-placeholder').classList.add('hidden');
                                                document.getElementById('fa-file-upload-selected').classList.remove('hidden');
                                                document.getElementById('fa-file-upload-selected').classList.add('flex');
                                                document.getElementById('fa-file-name-display').textContent = fileName;
                                            } else {
                                                document.getElementById('fa-file-upload-placeholder').classList.remove('hidden');
                                                document.getElementById('fa-file-upload-selected').classList.add('hidden');
                                                document.getElementById('fa-file-upload-selected').classList.remove('flex');
                                            }
                                        });
                                    </script>

                                </div>
                                <div class="mb-6 flex justify-center">
                                    <a href="{{ asset('template/TemplateAsset.xlsx') }}"
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
                                <button type="submit"
                                    class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-xl text-sm px-5 py-3 text-center dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800 transition-colors shadow-lg shadow-indigo-200 dark:shadow-indigo-900/30">
                                    Import Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Depre -->
            <div id="depre-modal" tabindex="-1"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <button type="button"
                            class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="depre-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                                run depreciation for all assets?</h3>
                            <button id="run-all-btn" type="button"
                                class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 dark:focus:ring-indigo-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                Yes, I'm sure
                            </button>
                            <button data-modal-hide="depre-modal" type="button"
                                class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                No, cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-alerts />

    <div class="p-6">
        <div
            class="w-full max-w-full bg-white border border-gray-200 shadow-sm rounded-2xl dark:bg-gray-800 dark:border-gray-700">
            <div class="relative overflow-x-auto">
                <table id="assetTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead
                        class="text-xs text-gray-600 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                <input type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600"
                                    id="select-all-assets">
                            </th>
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Asset Number</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Asset Name</th>
                            <th scope="col" class="px-6 py-3">Obj Acc</th>
                            <th scope="col" class="px-6 py-3">Description</th>
                            <th scope="col" class="px-6 py-3">Pareto</th>
                            <th scope="col" class="px-6 py-3">PO No</th>
                            <th scope="col" class="px-6 py-3">Location</th>
                            <th scope="col" class="px-6 py-3">Department</th>
                            <th scope="col" class="px-6 py-3">Qty</th>
                            <th scope="col" class="px-6 py-3">Capitalized Date</th>
                            <th scope="col" class="px-6 py-3">Start Depre Date</th>
                            <th scope="col" class="px-6 py-3">Acquisition Value</th>
                            <th scope="col" class="px-6 py-3">Useful Life Month</th>
                            <th scope="col" class="px-6 py-3">Accum Depre</th>
                            <th scope="col" class="px-6 py-3">Net Book Value</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                        <tr id="filter-row">
                            <th><input type="hidden" name="asset_ids" id="selected-asset-ids"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
    </div>

    <!-- Modal QR Code -->
    <div id="instantQrModal"
        class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-md items-center justify-center p-4 transition-all duration-300"
        onclick="closeInstantQr()">
        <div onclick="event.stopPropagation()"
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 p-6">
                <div class="flex justify-between items-center text-white">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <h3 id="modalTitle" class="text-xl font-bold truncate"></h3>
                    </div>
                    <button onclick="closeInstantQr()" class="hover:bg-white/20 rounded-full p-1 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-8 flex flex-col items-center bg-gray-50 dark:bg-gray-900/50">
                <div class="bg-white p-4 rounded-2xl shadow-inner border dark:border-gray-700">
                    <div id="qrcode-container"></div>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">Arahkan kamera untuk melihat detail aset</p>
            </div>

            <div class="p-6 bg-white dark:bg-gray-800 border-t dark:border-gray-700 flex flex-col gap-3">
                <div class="flex flex-row gap-3">
                    <a id="pngDownload"
                        class="flex justify-center items-center gap-2 w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M16 18H8l2.5-6 2 4 1.5-2 2 4Zm-1-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 3v4a1 1 0 0 1-1 1H5m14-4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 18h8l-2-4-1.5 2-2-4L8 18Zm7-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                        </svg>
                        PNG
                    </a>

                    <a id="pdfDownload"
                        class="flex justify-center items-center gap-2 w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z" />
                        </svg>
                        PDF
                    </a>

                    <a id="svgDownload"
                        class="flex justify-center items-center gap-2 w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            fill="none" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M16 18H8l2.5-6 2 4 1.5-2 2 4Zm-1-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 3v4a1 1 0 0 1-1 1H5m14-4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 18h8l-2-4-1.5 2-2-4L8 18Zm7-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                        </svg>
                        SVG
                    </a>
                </div>
                <button onclick="closeInstantQr()"
                    class="w-full py-3 text-gray-600 dark:text-gray-300 font-medium hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Download -->
    <div id="download-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto z-50 overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Download QR Code Fixed Asset
                    </h3>
                    <button type="button"
                        class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="download-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form action="{{ route('asset.qr.bulk-download') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ids" id="download-asset-ids">
                        <div class="mb-4 flex flex-col gap-3">
                            <div class="font-medium text-gray-900 dark:text-white">Select Format</div>
                            <div class="flex flex-row gap-2">
                                <ul class="grid w-full gap-4 md:grid-cols-3">
                                    <li>
                                        <input type="radio" id="png" name="format" value="png" class="hidden peer" checked
                                            required />
                                        <label for="png"
                                            class="inline-flex items-center justify-center gap-2 w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 peer-checked:bg-blue-50 hover:text-gray-600 hover:bg-gray-100 transition-all">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M16 18H8l2.5-6 2 4 1.5-2 2 4Zm-1-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M10 3v4a1 1 0 0 1-1 1H5m14-4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 18h8l-2-4-1.5 2-2-4L8 18Zm7-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                                            </svg>
                                            <div class="block">
                                                <div class="w-full text-sm font-semibold">PNG</div>
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <input type="radio" id="pdf" name="format" value="pdf" class="hidden peer">
                                        <label for="pdf"
                                            class="inline-flex items-center justify-center gap-2 w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 peer-checked:bg-blue-50 hover:text-gray-600 hover:bg-gray-100 transition-all">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z" />
                                            </svg>
                                            <div class="block">
                                                <div class="w-full text-sm font-semibold">PDF</div>
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <input type="radio" id="svg" name="format" value="svg" class="hidden peer">
                                        <label for="svg"
                                            class="inline-flex items-center justify-center gap-2 w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-600 peer-checked:text-blue-600 peer-checked:bg-blue-50 hover:text-gray-600 hover:bg-gray-100 transition-all">
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                    d="M16 18H8l2.5-6 2 4 1.5-2 2 4Zm-1-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M10 3v4a1 1 0 0 1-1 1H5m14-4v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1ZM8 18h8l-2-4-1.5 2-2-4L8 18Zm7-8.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0Z" />
                                            </svg>
                                            <div class="block">
                                                <div class="w-full text-sm font-semibold">SVG</div>
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                            <button type="submit"
                                class="w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                                Download QR Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Print -->
    <div id="print-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto z-50 overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Print QR Code Fixed Asset
                    </h3>
                    <button type="button"
                        class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="print-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5">
                    <form action="{{ route('asset.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4 flex flex-col gap-2">
                            <div class="font-medium text-gray-900 dark:text-white">Select Template</div>
                            <div class="flex flex-row items-center gap-2">
                                <input type="radio" name="print-template" id="print-template1" value="template1" checked>
                                <label for="print-template1" class="mr-4 text-sm text-gray-900 dark:text-white">QR Code
                                    Only</label>
                                <input type="radio" name="print-template" id="print-template2" value="template2">
                                <label for="print-template2" class="text-sm text-gray-900 dark:text-white">QR Code With
                                    Info</label>
                            </div>
                            <div class="border-dashed border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex flex-row justify-center items-center gap-4">
                                    <table id="print-info-table" class="text-sm text-gray-900 dark:text-white">
                                        <tr>
                                            <td colspan="2" class="font-bold pb-2">[Logo]</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Asset No</td>
                                            <td>: FAXXXXXXX</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Asset Name</td>
                                            <td>: XXXXXXXXX</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Asset Model</td>
                                            <td>: XXXXXXXXX</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">SN</td>
                                            <td>: XXXXXXXXX</td>
                                        </tr>
                                        <tr>
                                            <td class="pr-2">Own Dept</td>
                                            <td>: XXXXXXXXX</td>
                                        </tr>
                                    </table>
                                    <img id="print-qr-image" class="w-32 h-32" src="{{ asset('images/QR-Dummy.png') }}"
                                        alt="QR Dummy">
                                </div>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                            Print QR Code
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Pass data from Laravel to JavaScript module
        window.initialAssetIds = @json($initialAssetIds ?? []);
        window.assetNamesData = @json($assetNamesForFilter ?? []);
        window.locationsData = @json($locationsForFilter ?? []);
        window.departmentsData = @json($departmentsForFilter ?? []);
        window.csrfToken = "{{ csrf_token() }}";

        // Pass routes to JavaScript
        window.routes = {
            apiAsset: "{{ route('api.asset') }}",
            depreciationRunAll: "{{ route('depreciation.runAll') }}",
            depreciationStatus: "{{ route('depreciation.status') }}",
            depreciationClearStatus: "{{ route('depreciation.clearStatus') }}",
            depreciationStream: "{{ route('depreciation.stream') }}"
        };
    </script>
    @vite(['resources/js/pages/fixedAsset.js'])
@endpush