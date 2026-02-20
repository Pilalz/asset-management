import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function() {
    var table = $('#stockOpnameTable').DataTable({
        dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
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
            search: "Search : ",
            searchPlaceholder: "Cari di sini...",
        },
        initComplete: function () {
            // --- Tambahkan kelas ke search box utama di sini ---
            $('.dt-search input').addClass('w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');
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

        createdRow: function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
    });
});