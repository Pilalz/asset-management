@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Gaya untuk Light Mode */
            #locationTable tbody tr:hover {
                background-color: #F9FAFB !important; /* Tailwind's hover:bg-gray-50 */
            }

            /* Gaya untuk Dark Mode */
            .dark #locationTable tbody tr:hover {
                background-color: #374151 !important; /* Tailwind's dark:hover:bg-gray-700 (contoh) */
            }

            /* Menghapus background bawaan dari kolom yang diurutkan */
            table.dataTable tbody tr > .sorting_1,
            table.dataTable tbody tr > .sorting_2,
            table.dataTable tbody tr > .sorting_3 {
                background-color: inherit !important;
            }
        </style>
    @endpush
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('insured.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Insured Asset
                    </a>
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
    </div>
    
    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg bg-white p-4 dark:bg-gray-800">
            <table id="insuredTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">No</th>
                        <th scope="col" class="px-6 py-3">Polish No</th>
                        <th scope="col" class="px-6 py-3">Form No</th>
                        <th scope="col" class="px-6 py-3">Department</th>
                        <th scope="col" class="px-6 py-3">Quantity</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
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
        if ($('#insuredTable').length) {
            $('#insuredTable thead tr:eq(0) th').each(function(i) {
                var title = $(this).text().trim();
                var cell = $('#filter-row').children().eq(i);
                if (i === 0 || i === 5) {
                    return;
                }
                $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
            });

            var table = $('#insuredTable').DataTable({
                dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                    "<'overflow-x-auto'tr>" +
                    "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
                processing: true,
                serverSide: true,
                ajax: "{{ route('api.insured') }}",
                autoWidth: false,
                orderCellsTop: true,
                columns: [
                    { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
                    { data: 'polish_no', name: 'polish_no' },
                    { data: 'form_no', name: 'form_no' },
                    { data: 'department_name', name: 'department_name' },
                    { data: 'detail_registers_count', name: 'detail_registers_count' },
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
                        var cell = $('#insuredTable thead #filter-row').children().eq(column.index());
                        
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
                    });
                },

                columnDefs: [
                    {
                        targets: 0,
                        className: 'px-6 py-4'
                    },
                ],

                createdRow: function( row, data, dataIndex ) {
                    $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
                },
            });
        }
    })
</script>
@endpush