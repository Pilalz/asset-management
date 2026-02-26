import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function () {
    var table = $('#stockOpnameTable').DataTable({
        dom: "<'flex flex-col md:flex-row justify-between items-center p-5 border-b border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300 font-medium'l><'text-sm relative custom-search-container'>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col md:flex-row justify-between items-center p-5 border-t border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300'i><'text-sm'p>>",
        processing: true,
        serverSide: true,
        ajax: "/api/stock-opname",
        autoWidth: false,
        orderCellsTop: true,
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'description', name: 'description' },
            { data: 'status', name: 'status' },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'created_by_name', name: 'created_by_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'asc']],
        language: {
            search: "",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>`,
                next: `<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>`
            }
        },
        initComplete: function () {
            // Modify search input
            $('.dt-search input').addClass('pl-9 pr-4 py-2 w-full sm:w-64 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-400');
            $('.dt-search label').addClass('hidden');

            // Add custom search icon
            const searchHtml = `
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-slate-400 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            `;
            $('.custom-search-container .dt-search').addClass('relative w-full sm:w-auto').prepend(searchHtml);

            // Style length menu
            $('.dt-length select').addClass('bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2 transition-colors dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-400');
        },

        columnDefs: [
            {
                targets: 0,
                className: 'px-6 py-4'
            },
            {
                targets: 2,
                render: function (data, type, row) {
                    if (type === 'display') {
                        if (!data) {
                            return '-';
                        }
                    }
                    return data;
                }
            },
            {
                targets: 4,
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
                        if (!data) {
                            return 'Current';
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
            $(row).addClass('bg-white hover:bg-slate-50 border-b border-slate-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors duration-200');
        },
    });
});