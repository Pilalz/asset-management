@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Gaya untuk Light Mode */
            #insuranceTable tbody tr:hover {
                background-color: #F9FAFB !important;
                /* Tailwind's hover:bg-gray-50 */
            }

            /* Gaya untuk Dark Mode */
            .dark #insuranceTable tbody tr:hover {
                background-color: #374151 !important;
                /* Tailwind's dark:hover:bg-gray-700 (contoh) */
            }

            /* Matiin Outline */
            #insuranceTable thead tr th:hover {
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
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('insurance.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Insurance
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

        @can('is-admin')
            <div class="flex">
                <a href="{{ route('insurance.create') }}" type="button"
                    class="inline-flex items-center text-green-500 bg-white border border-green-300 focus:outline-none hover:bg-green-100 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 dark:bg-green-600 dark:text-gray-200 dark:border-gray-400 dark:hover:bg-green-500 dark:hover:border-green-400">
                    <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h14m-7 7V5" />
                    </svg>
                    New Data
                </a>
            </div>
        @endcan
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md rounded-lg bg-white p-4 dark:bg-gray-800">
            <table id="insuranceTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-100">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Polish No</th>
                        <th scope="col" class="px-6 py-3">Start Date</th>
                        <th scope="col" class="px-6 py-3">End Date</th>
                        <th scope="col" class="px-6 py-3">Instance</th>
                        <th scope="col" class="px-6 py-3">Annual Payment</th>
                        <!-- <th scope="col" class="px-6 py-3">Schedule</th> -->
                        <!-- <th scope="col" class="px-6 py-3">Next Payment</th> -->
                        <th scope="col" class="px-6 py-3">Status</th>
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
                        <!-- <th></th><th></th> -->
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
        document.addEventListener('DOMContentLoaded', () => {
            if ($('#insuranceTable').length) {
                $('#insuranceTable thead tr:eq(0) th').each(function (i) {
                    var title = $(this).text().trim();
                    var cell = $('#filter-row').children().eq(i);
                    if (i === 0 || i === 7) {
                        return;
                    }
                    else if (i === 2 || i === 3) {
                        $(cell).html('<input type="date" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
                    }
                    else if (i === 5) {
                        $(cell).html('<input type="number" min="1" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
                    }
                    else if (i === 6) {
                        $(cell).html(
                            '<select class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">' +
                            '<option selected value="">Select</option>' +
                            '<option value="Active">Active</option>' +
                            '<option value="Inactive">Inactive</option>' +
                            '</select>');
                    }
                    else {
                        $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
                    }
                });

                var table = $('#insuranceTable').DataTable({
                    dom: "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                        "<'overflow-x-auto'tr>" +
                        "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('api.insurance') }}",
                    autoWidth: false,
                    orderCellsTop: true,
                    columns: [
                        { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
                        { data: 'polish_no', name: 'polish_no' },
                        { data: 'start_date', name: 'start_date' },
                        { data: 'end_date', name: 'end_date' },
                        { data: 'instance_name', name: 'instance_name' },
                        { data: 'annual_premium', name: 'annual_premium' },
                        // { data: 'schedule', name: 'schedule' },
                        // { data: 'next_payment', name: 'next_payment' },
                        { data: 'status', name: 'status' },
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
                            var cell = $('#insuranceTable thead #filter-row').children().eq(column.index());

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
                                    column.search(this.value, false, true).draw();
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
                            className: 'px-6 py-4'
                        },
                        // {
                        //     targets: 6,
                        //     render: function (data, type, row) {
                        //         if (type === 'display') {
                        //             if (data == null) {
                        //                 return "-";
                        //             }
                        //             return data + ' (Month)';
                        //         }
                        //     return data;
                        //     }
                        // },
                        {
                            targets: [2, 3],
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
                            targets: 5,
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

                    createdRow: function (row, data, dataIndex) {
                        $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');

                        var statusCell = $(row).find('td:eq(6)');

                        // Tentukan class berdasarkan nilai 'data.status'
                        let statusClass = '';
                        if (data.status === 'Active') {
                            statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                        } else {
                            statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                        }

                        // Ubah konten sel menjadi span dengan class yang sesuai
                        statusCell.html(`<span class="${statusClass} text-xs font-medium px-2.5 py-0.5 rounded">${data.status}</span>`);
                    },
                });
            }
        })
    </script>
@endpush