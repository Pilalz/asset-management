import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function() {

    const assetClassesData = window.assetclassesForFilterData || [];
    
    $('#assetSubClassTable thead tr:eq(0) th').each(function(i) {
        var title = $(this).text().trim();
        var cell = $('#filter-row').children().eq(i);
        if (i === 0 || i === 3) {
            return;
        }
        else if (i === 1) {
            let options = assetClassesData.map(assetClass =>
                `<option value="${assetClass.name}">${assetClass.name}</option>` // Value pakai ID
            ).join('');
            $(cell).html(
                `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option selected value="">Select Asset Class</option>
                    ${options}
                </select>`
            );
        }
        else {
            $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        }
    });

    var table = $('#assetSubClassTable').DataTable({
        dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
        processing: true,
        serverSide: true,
        ajax: "/api/asset-sub-class",
        autoWidth: false,
        orderCellsTop: true,
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
            { data: 'asset_class_name', name: 'assetClass.name' },
            { data: 'name', name: 'name' },
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
            
            // --- Logika untuk filter per kolom ---
            this.api().columns().every(function (index) {
                var column = this;
                var cell = $('#assetSubClassTable thead #filter-row').children().eq(column.index());
                
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
        ],

        createdRow: function( row, data, dataIndex ) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
        },
    });

    table.columns().every(function() {
        var that = this;
        
        // Event untuk filtering saat mengetik
        $('input', $('#assetSubClassTable thead #filter-row').children().eq(this.index())).on('keyup change clear', function(e) {
            e.stopPropagation(); // Hentikan event agar tidak memicu sorting
            if (that.search() !== this.value) {
                that.search(this.value).draw();
            }
        });
    });
});