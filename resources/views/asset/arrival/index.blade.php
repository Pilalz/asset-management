@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Gaya untuk Light Mode */
            #assetTable tbody tr:hover {
                background-color: #F9FAFB !important;
                /* Tailwind's hover:bg-gray-50 */
            }

            /* Gaya untuk Dark Mode */
            .dark #assetTable tbody tr:hover {
                background-color: #374151 !important;
                /* Tailwind's dark:hover:bg-gray-700 (contoh) */
            }

            /* Matiin Outline */
            #assetTable thead tr th:hover {
                outline: none !important;
            }

            /* Menghapus background bawaan dari kolom yang diurutkan */
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
                    <a href="{{ route('assetArrival.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Arrival Asset
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
                            <th scope="col" class="px-6 py-3">No</th>
                            <th scope="col" class="px-6 py-3">Asset Type</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Asset Name</th>
                            <th scope="col" class="px-6 py-3">Obj Acc</th>
                            <th scope="col" class="px-6 py-3">Description</th>
                            <th scope="col" class="px-6 py-3">PO No</th>
                            <th scope="col" class="px-6 py-3">Location</th>
                            <th scope="col" class="px-6 py-3">Department</th>
                            <th scope="col" class="px-6 py-3">Qty</th>
                            <th scope="col" class="px-6 py-3">Capitalized Date</th>
                            <th scope="col" class="px-6 py-3">Useful Life Month</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                        <tr id="filter-row">
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
@endsection

@push('scripts')
    <script>
        const assetNamesData = @json($assetNamesForFilter ?? []);
        const locationsData = @json($locationsForFilter ?? []);
        const departmentsData = @json($departmentsForFilter ?? []);

        document.addEventListener('DOMContentLoaded', () => {

            if (typeof $ !== 'undefined') {
                $('#assetTable thead tr:eq(0) th').each(function (i) {
                    var title = $(this).text().trim();
                    var cell = $('#filter-row').children().eq(i);
                    if (i === 0 || i === 2 || i === 4 || i === 12) {
                        return;
                    }
                    else if (i === 1) {
                        $(cell).html(
                            '<select class="filter-select w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm">' +
                            '<option selected value="">Select</option>' +
                            '<option value="FA">Fixed Asset</option>' +
                            '<option value="LVA">Low Value Asset</option>' +
                            '</select>');
                    }
                    else if (i === 3) {
                        let options = assetNamesData.map(assetName =>
                            `<option value="${assetName.name}">${assetName.name}</option>` // Value pakai ID
                        ).join('');
                        $(cell).html(
                            `<select class="filter-select w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm">
                                        <option selected value="">Select Asset Name</option>
                                        ${options}
                                    </select>`
                        );
                    }
                    else if (i === 7) {
                        let options = locationsData.map(loc =>
                            `<option value="${loc.name}">${loc.name}</option>` // Value pakai ID
                        ).join('');
                        $(cell).html(
                            `<select class="filter-select w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm">
                                        <option selected value="">Select Location</option>
                                        ${options}
                                    </select>`
                        );
                    }
                    else if (i === 8) {
                        let options = departmentsData.map(dept =>
                            `<option value="${dept.name}">${dept.name}</option>` // Value pakai ID
                        ).join('');
                        $(cell).html(
                            `<select class="filter-select w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm">
                                        <option selected value="">Select Department</option>
                                        ${options}
                                    </select>`
                        );
                    }
                    else if (i === 9 || i === 11) {
                        $(cell).html('<input type="number" min="0" class="w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm" placeholder="Search..." />');
                    }
                    else if (i === 10) {
                        $(cell).html('<input type="date" class="w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm" placeholder="Search..." />');
                    }
                    else {
                        $(cell).html('<input type="text" class="w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm" placeholder="Search..." />');
                    }
                });

                // Custom Search Input HTML
                const customSearchHTML = `
                            <div class="relative flex items-center w-full sm:w-auto">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                    </svg>
                                </div>
                                <input type="search" class="dt-custom-search pl-9 pr-4 py-2 w-full sm:w-64 bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-colors shadow-sm" placeholder="Search all columns..." aria-controls="assetTable">
                            </div>
                        `;

                var table = $('#assetTable').DataTable({
                    dom: "<'flex flex-col md:flex-row justify-between items-center p-5 border-b border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300 font-medium'l><'text-sm relative custom-search-container'>>" +
                        "<'overflow-x-auto'tr>" +
                        "<'flex flex-col md:flex-row justify-between items-center p-5 border-t border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300'i><'text-sm'p>>",
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('api.assetArrival') }}",
                    autoWidth: false,
                    orderCellsTop: true,
                    columns: [
                        { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
                        { data: 'asset_type', name: 'asset_type' },
                        { data: 'status', name: 'status' },
                        { data: 'asset_name_name', name: 'asset_name_name' },
                        { data: 'asset_class_obj', name: 'asset_class_obj' },
                        { data: 'description', name: 'description' },
                        { data: 'po_no', name: 'po_no' },
                        { data: 'location_name', name: 'location_name' },
                        { data: 'department_name', name: 'department_name' },
                        { data: 'quantity', name: 'quantity' },
                        { data: 'capitalized_date', name: 'capitalized_date' },
                        { data: 'commercial_useful_life_month', name: 'commercial_useful_life_month' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    order: [[0, 'asc']],
                    language: {
                        search: "",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Prev"
                        },
                        zeroRecords: `<div class="flex flex-col items-center justify-center p-8 text-center">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-full p-3 mb-4">
                                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No records found</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">Try adjusting your search or filters</p>
                            </div>`,
                        emptyTable: `<div class="flex flex-col items-center justify-center p-8 text-center">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-full p-3 mb-4">
                                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No data available in table</p>
                            </div>`,
                        processing: `
                                <style>
                                    div.dt-processing > div:not(.dt-spinner-custom) { display: none !important; }
                                    div.dt-processing { background: rgba(255, 255, 255, 0.8) !important; box-shadow: none !important; backdrop-filter: blur(2px); border-radius: 0.5rem; }
                                    html.dark div.dt-processing { background: rgba(31, 41, 55, 0.8) !important; }
                                </style>
                                <div class="dt-spinner-custom flex justify-center items-center py-6">
                                    <svg aria-hidden="true" class="w-10 h-10 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                    </svg>
                                    <span class="sr-only">Please wait...</span>
                                </div>`
                    },
                    initComplete: function () {
                        // Replace default search with custom search
                        $('.custom-search-container').html(customSearchHTML);

                        // Bind custom search input to DataTables search
                        $('.dt-custom-search').on('keyup', function () {
                            table.search(this.value).draw();
                        });

                        // Styling length menu
                        var $lengthMenu = $('.dt-length select');
                        $lengthMenu.addClass('bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 ml-2 py-1.5 px-3 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-blue-400');

                        // --- Logika untuk filter per kolom ---
                        this.api().columns().every(function (index) {
                            var column = this;
                            var cell = $('#assetTable thead #filter-row').children().eq(column.index());

                            if (column.settings()[0].bSearchable === false) {
                                return;
                            }

                            var input = $('input', cell);
                            input.on('keyup change clear', function (e) {
                                e.stopPropagation();
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                            input.on('click', function (e) {
                                e.stopPropagation();
                            });

                            var select = $('select', cell);
                            select.on('change', function (e) {
                                e.stopPropagation();
                                if (column.search() !== this.value) {
                                    column.search(this.value).draw();
                                }
                            });
                            select.on('click', function (e) {
                                e.stopPropagation();
                            });
                        });
                    },

                    columnDefs: [
                        {
                            targets: 0,
                            className: 'px-6 py-4 text-center text-gray-500 dark:text-gray-400 w-16'
                        },
                        {
                            targets: 1,
                            render: function (data, type, row) {
                                if (data === 'LVA') {
                                    return 'Low Value Asset';
                                }
                                if (data === 'FA') {
                                    return 'Fixed Asset';
                                }
                                else {
                                    return 'Unidentified';
                                }
                                return data;
                            }
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
                            targets: 10,
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
                    ],

                    createdRow: function (row, data, dataIndex) {
                        $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors duration-200');
                    },
                });

                table.columns().every(function () {
                    var that = this;

                    // Event untuk filtering saat mengetik
                    $('input', $('#assetTable thead #filter-row').children().eq(this.index())).on('keyup change clear', function (e) {
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