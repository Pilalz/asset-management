import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function() {
    const historyTable = $('#historyTable');
    
    if (historyTable.length) {

        const ajaxUrl = historyTable.data('url');

        $('#historyTable thead tr:eq(0) th').each(function(i) {
            var title = $(this).text().trim();
            var cell = $('#filter-row').children().eq(i);
            if (i === 2 || i === 3) {
                return;
            }
            $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        });

        var table = $('#historyTable').DataTable({
            dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
            processing: true,
            serverSide: true,
            ajax: {
                url: ajaxUrl,
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
            },
            autoWidth: false,
            orderCellsTop: true,
            columns: [
                { data: 'description', name: 'description' },
                { data: 'causer.name', name: 'causer.name' },
                { data: 'changes', name: 'changes', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
            ],
            order: [[3, 'desc']],
            language: {
                search: "Search : ",
                searchPlaceholder: "Cari di sini...",
            },
            initComplete: function () {
                $('.dt-search input').addClass('w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');

                // --- Logika untuk filter per kolom ---
                this.api().columns().every(function (index) {
                    var column = this;
                    var cell = $('#historyTable thead #filter-row').children().eq(column.index());
                    
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

        $('#start_date, #end_date').change(function(){
            table.draw();
        });
    }
});