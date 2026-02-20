import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function() {

    const departmentsData = window.departmentsForFilterData || [];
    const locationsData = window.locationsForFilterData || [];

    if ($('#transferAssetTable').length) {
        $('#transferAssetTable thead tr:eq(0) th').each(function(i) {
            var title = $(this).text().trim();
            var cell = $('#filter-row').children().eq(i);
            if (i === 0 || i === 8) {
                return;
            }
            else if (i === 2) {
                $(cell).html('<input type="date" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />');
            }
            else if (i === 3) {
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
            else if (i === 4) {
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
            else if (i === 5) {
                $(cell).html(
                        '<select class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">' +
                            '<option selected value="">Select</option>' +
                            '<option value="1">Yes</option>' +
                            '<option value="0">No</option>' +  
                        '</select>');
            }
            else if (i === 6) {
                $(cell).html('<input type="number" min="1" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
            }
            else if (i === 7) {
                $(cell).html(
                        '<select class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">' +
                            '<option selected value="">Select</option>' +
                            '<option value="Approved">Approved</option>' +
                            '<option value="Waiting">Waiting</option>' +  
                        '</select>');
            }
            else {
                $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
            }
        });

        var table = $('#transferAssetTable').DataTable({
            dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
            processing: true,
            serverSide: true,
            ajax: "/api/transfer-asset",
            autoWidth: false,
            orderCellsTop: true,
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false },
                { data: 'form_no', name: 'form_no' },
                { data: 'submit_date', name: 'submit_date' },
                { data: 'department_name', name: 'department_name' },
                { data: 'destination_location_name', name: 'destination_location_name' },
                { data: 'sequence', name: 'sequence', render: data => data == 1 ? 'Yes' : 'No' },
                { data: 'detail_transfers_count', name: 'detail_transfers_count' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            language: {
                search: "Search : ",
                searchPlaceholder: "Cari di sini...",
            },
            initComplete: function () {
                $('.dt-search input').addClass('w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');

                // --- Logika untuk filter per kolom ---
                this.api().columns().every(function (index) {
                    var column = this;
                    var cell = $('#transferAssetTable thead #filter-row').children().eq(column.index());
                    
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
                    targets: 2,
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

            createdRow: function( row, data, dataIndex ) {
                $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');

                var statusCell = $(row).find('td:eq(7)');

                // Tentukan class berdasarkan nilai 'data.status'
                let statusClass = '';
                if (data.status === 'Waiting') {
                    statusClass = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
                } else if (data.status === 'Approved') {
                    statusClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
                } else if (data.status === 'Rejected') {
                    statusClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                } else {
                    statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                }

                // Ubah konten sel menjadi span dengan class yang sesuai
                statusCell.html(`<span class="${statusClass} text-xs font-medium px-2.5 py-0.5 rounded">${data.status}</span>`);
            },
        });
    }
})