@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Gaya untuk Light Mode */
            #assetTable tbody tr:hover {
                background-color: #F9FAFB !important; /* Tailwind's hover:bg-gray-50 */
            }

            /* Gaya untuk Dark Mode */
            .dark #assetTable tbody tr:hover {
                background-color: #374151 !important; /* Tailwind's dark:hover:bg-gray-700 (contoh) */
            }

            /* Matiin Outline */
            #assetTable thead tr th:hover {
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
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800 dark:border-b dark:border-gray-700">
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
                        <a href="{{ route('asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Fixed Asset</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">List</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex gap-2">
            <div id="depreciation-controls" class="flex items-center gap-4">
                <div id="status-container" class="hidden items-center gap-2">
                    <div role="status">
                        <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span id="status-text" class="text-sm font-medium"></span>
                </div>
            </div>

            <div class="flex gap-2">
                @can('is-admin')
                    <div class="hidden sm:block">
                        <a href="{{ route('asset.create') }}" type="button" class="inline-flex items-center text-green-500 bg-white border border-green-300 focus:outline-none hover:bg-green-100 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 dark:bg-green-600 dark:text-gray-200 dark:border-gray-400 dark:hover:bg-green-500 dark:hover:border-green-400">
                            <span class="sr-only">New Data</span>
                            New Data
                            <svg class="w-4 h-4 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                            </svg>
                        </a>
                    </div>

                    <div>
                        <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-400 dark:hover:bg-gray-500 dark:hover:border-gray-400" type="button">
                            <span class="sr-only">Action button</span>
                            Action
                            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                    </div>
                @endcan
                <!-- Dropdown menu -->
                <div id="dropdownAction" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-md w-44 dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownActionButton">
                        @if(!$hasPendingDepreciation)
                            
                        @else
                            <li>
                                <div class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    <button id="run-all-btn" type="button" class="inline-flex items-center me-2">
                                        <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 0 2-2m-2 2-2-2M3 6v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Zm2 2v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8H5Z"/>
                                        </svg>
                                        Run All Depre
                                    </button>
                                </div>
                            </li>
                        @endif
                        <li>
                            <div class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <!-- Modal toggle -->
                                <button data-modal-target="import-modal" data-modal-toggle="import-modal" class="inline-flex items-center me-2" type="button">
                                    <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-4m5-13v4a1 1 0 0 1-1 1H5m0 6h9m0 0-2-2m2 2-2 2"/>
                                    </svg>
                                    Import Data
                                </button>
                            </div>
                        </li>
                        <li>
                            <div class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                <a href="{{ route('asset.export') }}" class="inline-flex items-center me-2">
                                    <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2"/>
                                    </svg>
                                    Export Excel
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main modal -->
            <div id="import-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-md max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                        <!-- Modal header -->
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Import Data Fixed Asset
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
                                    <label for="excel_file" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Upload Excel File (.xlsx, .xls)</label>
                                    <input type="file" name="excel_file" id="excel_file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" required>
                                </div>   
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Silahkan download template Excel jika anda belum memilikinya.
                                    </p>
                                    <a href="{{ asset('template/TemplateAsset.xlsx') }}" class="text-blue-600 hover:underline dark:text-blue-500">Download Template Excel</a>
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

    @if (session('success'))
        <div id="alert-3" class="auto-dismiss-alert flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div id="alert-2" class="auto-dismiss-alert flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('error') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-2" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div id="alert-1" class="auto-dismiss-alert flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('info') }}
            </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-1" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif
    
    <div class="p-5">
        <div class="shadow-md rounded-lg bg-white p-4 dark:bg-gray-800">
            <div class="relative overflow-x-auto">
                <table id="assetTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-100">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
                        <tr>
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
                            <th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th>
                            <th></th><th></th><th></th><th></th>
                            <th></th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>

    const assetNamesData = @json($assetNamesForFilter ?? []);
    const locationsData = @json($locationsForFilter ?? []);
    const departmentsData = @json($departmentsForFilter ?? []);

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof $ !== 'undefined') {

            //DEPRE ALL
            const runBtn = $('#run-all-btn');
            const statusContainer = $('#status-container');
            const statusText = $('#status-text');
            
            let eventSource = null; // Variabel untuk menyimpan koneksi EventSource

            // Fungsi untuk memulai mendengarkan pembaruan dari server
            function listenForUpdates() {
                // Tutup koneksi lama jika ada
                if (eventSource) {
                    eventSource.close();
                }

                // Buka koneksi baru ke stream
                eventSource = new EventSource("{{ route('depreciation.stream') }}");

                // Fungsi ini akan berjalan setiap kali server mengirim data
                eventSource.onmessage = function(event) {
                    const data = JSON.parse(event.data);

                    if (!data) {
                        eventSource.close();
                        updateUI('idle');
                        return;
                    }

                    // Update UI berdasarkan status yang diterima
                    updateUI(data.status, data.progress, data.message, data.error);

                    // Jika proses selesai atau gagal, tutup koneksi
                    if (data.status === 'completed' || data.status === 'failed') {
                        eventSource.close();
                    }
                };

                // Tangani error koneksi
                eventSource.onerror = function() {
                    console.error("Koneksi SSE gagal. Menutup koneksi.");
                    eventSource.close();
                    updateUI('idle');
                };
            }

            // Fungsi terpusat untuk memperbarui tampilan
            function updateUI(status, progress = 0, message = '', error = '') {
                if (status === 'running') {
                    runBtn.prop('disabled', true).addClass('cursor-not-allowed bg-gray-400');
                    statusContainer.removeClass('hidden');
                    statusContainer.addClass('flex');

                    statusText.text('Sedang memproses... (' + Math.round(progress) + '%)');
                } else {
                    runBtn.prop('disabled', false).removeClass('cursor-not-allowed bg-gray-400');
                    statusContainer.removeClass('flex');
                    statusContainer.addClass('hidden');
                    
                    if (status === 'completed') {
                        alert(message || 'Proses depresiasi selesai!');
                        $('#assetTable').DataTable().ajax.reload(null, false);
                        $.post("{{ route('depreciation.clearStatus') }}", { _token: "{{ csrf_token() }}" });
                    } else if (status === 'failed') {
                        alert('Proses depresiasi gagal: ' + error);
                        $.post("{{ route('depreciation.clearStatus') }}", { _token: "{{ csrf_token() }}" });
                    }
                }
            }
            
            // Event listener untuk tombol "Run All"
            runBtn.on('click', function() {
                if (!confirm('Apakah Anda yakin ingin menjalankan depresiasi untuk semua aset?')) return;

                updateUI('running', 0);
                statusText.text('Mengirim permintaan...');

                $.post("{{ route('depreciation.runAll') }}")
                    .done(function() {
                        // Setelah job berhasil dimulai, mulai mendengarkan
                        listenForUpdates();
                    })
                    .fail(function(xhr) {
                        alert('Gagal memulai proses: ' + (xhr.responseJSON?.message || 'Error tidak diketahui.'));
                        updateUI('idle');
                    });
            });
            
            // Cek status saat halaman pertama kali dimuat
            $.get("{{ route('depreciation.status') }}").done(function(data) {
                if (data && data.status === 'running') {
                    // Jika job sudah berjalan, langsung mulai mendengarkan
                    listenForUpdates();
                }
            });

            //TABLE
            $('#assetTable thead tr:eq(0) th').each(function(i) {
                var title = $(this).text().trim();
                var cell = $('#filter-row').children().eq(i);
                if (i === 0 || i === 4 || i === 17) {
                    return;
                }
                else if (i === 3) {
                    let options = assetNamesData.map(assetName =>
                        `<option value="${assetName.name}">${assetName.name}</option>` // Value pakai ID
                    ).join('');
                    $(cell).html(
                        `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option selected value="">Select Asset Name</option>
                            ${options}
                        </select>`
                    );
                }
                else if (i === 8) {
                    let options = locationsData.map(loc =>
                        `<option value="${loc.name}">${loc.name}</option>` // Value pakai ID
                    ).join('');
                    $(cell).html(
                        `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option selected value="">Select Location</option>
                            ${options}
                        </select>`
                    );
                }
                else if (i === 9) {
                    let options = departmentsData.map(dept =>
                        `<option value="${dept.name}">${dept.name}</option>` // Value pakai ID
                    ).join('');
                    $(cell).html(
                        `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option selected value="">Select Department</option>
                            ${options}
                        </select>`
                    );
                }
                else if (i === 10 || i === 13 || i === 14 || i === 15 || i === 16) {
                    $(cell).html('<input type="number" min="1" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
                }
                else if (i === 11 || i === 12) {
                    $(cell).html('<input type="date" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
                }
                else {
                    $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
                }
            });

            var table = $('#assetTable').DataTable({
            dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
            processing: true,
            serverSide: true,
            ajax: "{{ route('api.asset') }}",
            autoWidth: false,
            orderCellsTop: true,
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
                { data: 'asset_number', name: 'asset_number' },
                { data: 'status', name: 'status' },
                { data: 'asset_name_name', name: 'asset_name_name' },
                { data: 'asset_class_obj', name: 'asset_class_obj' },
                { data: 'description', name: 'description' },
                { data: 'pareto', name: 'pareto' },
                { data: 'po_no', name: 'po_no' },
                { data: 'location_name', name: 'location_name' },
                { data: 'department_name', name: 'department_name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'capitalized_date', name: 'capitalized_date' },
                { data: 'start_depre_date', name: 'start_depre_date' },
                { data: 'acquisition_value', name: 'acquisition_value' },
                { data: 'commercial_useful_life_month', name: 'commercial_useful_life_month' },
                { data: 'commercial_accum_depre', name: 'commercial_accum_depre' },
                { data: 'commercial_nbv', name: 'commercial_nbv' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']],
            language: {
                search: "Search : ",
                searchPlaceholder: "Cari di sini...",
            },
            initComplete: function () {
                $('.dt-search input').addClass('w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');

                // --- Logika untuk filter per kolom ---
                this.api().columns().every(function (index) {
                    var column = this;
                    var cell = $('#assetTable thead #filter-row').children().eq(column.index());
                    
                    if (column.settings()[0].bSearchable === false) {
                        return;
                    }
                    
                    var input = $('input', cell);
                    input.on('keyup change clear', function(e) {
                        e.stopPropagation();
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                    input.on('click', function(e) {
                        e.stopPropagation();
                    });

                    var select = $('select', cell);
                    select.on('change', function(e) {
                        e.stopPropagation();
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                    select.on('click', function(e) {
                        e.stopPropagation();
                    });
                });
            },

            columnDefs: [
                {
                    targets: 0,
                    className: 'px-6 py-4'
                },
                {
                    targets: 4, 
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return 'Direct Ownership : ' + data;
                        }
                        return data;
                    }
                },
                {
                    targets: [11, 12],
                    render: function (data, type, row) {
                        if (type === 'display') {
                            if (!data) {
                                return '-';
                            }
                            
                            try {
                                const date = new Date(data);
                                
                                const options = {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                };

                                return date.toLocaleDateString('id-ID', options);
                            } catch (e) {
                                return data;
                            }
                        }
                        return data;
                    }
                },
                {
                    targets: [13, 15, 16], 
                    render: function (data, type, row) {
                        if (type === 'display') {
                            let number = parseFloat(data);

                            if (isNaN(number)) {
                                return data;
                            }

                            const currencyCode = row.currency || 'USD'; 
                            let locale = 'en-US';
                            if (currencyCode === 'IDR') {
                                locale = 'id-ID';
                            }

                            return number.toLocaleString(locale, {
                                style: 'currency',
                                currency: currencyCode,
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 0
                            });
                        }
                        return data;
                    }
                }
            ],

            createdRow: function( row, data, dataIndex ) {
                $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
            },
        });

        table.columns().every(function() {
            var that = this;
            
            // Event untuk filtering saat mengetik
            $('input', $('#assetTable thead #filter-row').children().eq(this.index())).on('keyup change clear', function(e) {
                e.stopPropagation(); // Hentikan event agar tidak memicu sorting
                if (that.search() !== this.value) {
                    that.search(this.value).draw();
                }
            });
        });

        }
    });
</script>
@endpush