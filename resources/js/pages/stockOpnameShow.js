import $ from 'jquery';
import 'datatables.net-dt';

$(document).ready(function () {
    const table = document.getElementById('soDetailTable');
    if (!table) return;

    const ajaxUrl = table.dataset.url;

    // ── Input filter CSS class ──────────────────────────────────────────
    const inputCls = 'w-full p-1.5 mx-1 my-1 text-xs border border-gray-300 rounded-md ' +
        'focus:ring-blue-500 focus:border-blue-500 ' +
        'dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400';

    // ── Build filter inputs in filter-row ───────────────────────────────
    // Mapping: kolom index → tipe filter ('text' | 'select:<opt1>,<opt2>' | 'none')
    const filterTypes = {
        0: 'none',                            // No
        1: 'text',                            // Asset Number
        2: 'assetname',                       // Asset Name → dropdown
        3: 'none',                            // Description (terlalu panjang untuk filter)
        4: 'text',                            // Lokasi Sistem
        5: 'text',                            // Lokasi Aktual
        6: 'text',                            // Kondisi Sistem
        7: 'text',                            // Kondisi Aktual
        8: 'text',                            // User Sistem
        9: 'text',                            // User Aktual
        10: 'select:Found,Missing',            // Status
        11: 'none',                            // Waktu Scan
    };

    const filterRow = document.getElementById('filter-row');
    if (filterRow) {
        Array.from(filterRow.cells).forEach(function (cell, i) {
            const type = filterTypes[i] ?? 'none';
            if (type === 'none') {
                cell.innerHTML = '';
                return;
            }
            if (type === 'text') {
                cell.innerHTML = `<input type="text" class="${inputCls}" placeholder="Cari..." data-col="${i}" />`;
            } else if (type === 'assetname') {
                const names = window.assetNamesData || {};
                const optHtml = Object.values(names)
                    .map(n => `<option value="${n}">${n}</option>`)
                    .join('');
                cell.innerHTML = `<select class="${inputCls}" data-col="${i}">
                    <option value="">Semua Nama Aset</option>
                    ${optHtml}
                </select>`;
            } else if (type.startsWith('select:')) {
                const opts = type.replace('select:', '').split(',');
                const optHtml = opts.map(o => `<option value="${o}">${o}</option>`).join('');
                cell.innerHTML = `<select class="${inputCls}" data-col="${i}">
                    <option value="">Semua</option>
                    ${optHtml}
                </select>`;
            }
        });
    }

    // ── Init DataTable ──────────────────────────────────────────────────
    const dt = $('#soDetailTable').DataTable({
        dom:
            "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'" +
            "<'text-sm text-gray-700 dark:text-gray-200'l>" +
            "<'text-sm'f>" +
            ">" +
            "<'overflow-x-auto'tr>" +
            "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'" +
            "<'text-sm text-gray-700 dark:text-gray-200'i>" +
            "<'text-sm'p>" +
            ">",
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        autoWidth: false,
        orderCellsTop: true,    // agar klik header kolom tidak bentrok dengan filter-row
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
            { data: 'asset_number', name: 'asset_number', searchable: true },
            { data: 'asset_name', name: 'asset_name', searchable: true },
            { data: 'asset_description', name: 'asset_description', searchable: false },
            { data: 'system_location_name', name: 'system_location_name', searchable: true },
            { data: 'actual_location_name', name: 'actual_location_name', searchable: true },
            { data: 'system_condition', name: 'system_condition', searchable: true },
            { data: 'actual_condition', name: 'actual_condition', searchable: true },
            { data: 'system_user', name: 'system_user', searchable: true },
            { data: 'actual_user', name: 'actual_user', searchable: true },
            { data: 'status_badge', name: 'status', searchable: true, orderable: true },
            { data: 'scanned_at', name: 'scanned_at', searchable: false },
        ],
        order: [[0, 'asc']],
        language: {
            search: "Search : ",
            searchPlaceholder: "Cari aset...",
            processing: "<span class='text-sm text-gray-500 dark:text-gray-400'>Memuat data...</span>",
        },
        initComplete: function () {
            $('.dt-search input').addClass(
                'w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg ' +
                'focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 ' +
                'dark:placeholder-gray-400 dark:text-white'
            );

            // ── Event listener untuk filter per kolom ────────────────────
            // Input text — debounce agar tidak terlalu banyak request
            let debounceTimer;
            $('#filter-row').on('input', 'input[type="text"]', function () {
                const colIdx = parseInt($(this).data('col'));
                const val = this.value;
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function () {
                    dt.column(colIdx).search(val).draw();
                }, 400);
            });

            // Select — langsung trigger
            $('#filter-row').on('change', 'select', function () {
                const colIdx = parseInt($(this).data('col'));
                dt.column(colIdx).search($(this).val()).draw();
            });
        },
        columnDefs: [
            {
                targets: 11,
                render: function (data) {
                    if (!data) return '-';
                    try {
                        return new Date(data).toLocaleString('id-ID', {
                            day: 'numeric', month: 'long', year: 'numeric',
                            hour: '2-digit', minute: '2-digit'
                        });
                    } catch (e) { return data; }
                }
            },
            {
                // Nilai null/empty → tampilkan '-'
                targets: [3, 4, 5, 6, 7, 8, 9],
                render: function (data) {
                    return data || '-';
                }
            },
        ],
        createdRow: function (row) {
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
            $('td', row).addClass('px-4 py-3');
        },
    });
});
