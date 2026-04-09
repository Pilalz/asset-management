import $ from 'jquery';
import 'datatables.net-dt';

// Global variables
const initialAssetIds = window.initialAssetIds || [];
const initialIds = initialAssetIds.map(String);
const selectedAssetIds = new Set(initialIds);
const selectedAssetData = new Map();

// QR Modal Functions
export function openInstantQr(content, name, qrcodeUrl) {
    const modal = document.getElementById('instantQrModal');
    const container = document.getElementById("qrcode-container");

    const downloadPNG = document.getElementById('pngDownload');
    const downloadPDF = document.getElementById('pdfDownload');
    const downloadSVG = document.getElementById('svgDownload');

    document.getElementById('modalTitle').innerText = name;

    // Set download URL
    downloadPNG.href = qrcodeUrl + "?format=png";
    downloadPDF.href = qrcodeUrl + "?format=pdf";
    downloadSVG.href = qrcodeUrl + "?format=svg";

    // Clear & Generate QR
    container.innerHTML = "";
    new QRCode(container, {
        text: content,
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });

    // Show modal with flex display
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Lock page scroll
    document.body.style.overflow = 'hidden';
}


export function closeInstantQr() {
    const modal = document.getElementById('instantQrModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');

    // Restore scroll
    document.body.style.overflow = 'auto';
}

// Depreciation Functions
function listenForUpdates(streamUrl, statusUrl) {
    let eventSource = null;
    let reconnectTimer = null;

    function connect() {
        // Bersihkan timer reconnect jika ada
        if (reconnectTimer) {
            clearTimeout(reconnectTimer);
            reconnectTimer = null;
        }

        // Tutup koneksi lama jika ada
        if (eventSource) {
            eventSource.close();
            eventSource = null;
        }

        // Buka koneksi SSE baru
        eventSource = new EventSource(streamUrl);

        // Handle incoming data
        eventSource.onmessage = function (event) {
            const data = JSON.parse(event.data);

            if (!data) {
                eventSource.close();
                updateUI('idle');
                return;
            }

            // Update UI berdasarkan status yang diterima
            updateUI(data.status, data.progress, data.message, data.error);

            // Tutup koneksi jika job sudah selesai atau gagal (status terminal)
            if (data.status === 'completed' || data.status === 'completed_with_errors' || data.status === 'failed') {
                eventSource.close();
            }
        };

        // Handle error koneksi SSE (termasuk saat server menutup koneksi setelah timeout)
        eventSource.onerror = function () {
            eventSource.close();
            eventSource = null;

            // Cek status cache dulu sebelum reset UI —
            // server mungkin sengaja menutup koneksi (55 detik) padahal job masih running
            if (statusUrl) {
                $.get(statusUrl).done(function (data) {
                    if (data && data.status && ['queued', 'running'].includes(data.status)) {
                        // Job masih berjalan → reconnect setelah 2 detik
                        console.info('SSE putus, job masih running. Auto-reconnect dalam 2 detik...');
                        updateUI(data.status, data.progress, data.message);
                        reconnectTimer = setTimeout(connect, 2000);
                    } else {
                        // Job sudah selesai, gagal, atau idle → reset UI
                        console.info('SSE putus, job sudah tidak aktif. Reset UI.');
                        updateUI('idle');
                    }
                }).fail(function () {
                    // Gagal cek status → aman untuk reset UI
                    updateUI('idle');
                });
            } else {
                updateUI('idle');
            }
        };
    }

    connect();

    // Return object untuk bisa di-close dari luar jika perlu
    return {
        close: function () {
            if (reconnectTimer) clearTimeout(reconnectTimer);
            if (eventSource) eventSource.close();
        }
    };
}

function updateUI(status, progress = 0, message = '', error = '') {
    const runBtn = $('#run-all-btn');
    const statusContainer = $('#status-container');
    const statusText = $('#status-text');
    const progressBar = $('#progress-bar');
    const progressPercentage = $('#progress-percentage');
    const iconContainer = $('#status-icon-container');

    const resetUI = () => {
        statusContainer.removeClass('flex').addClass('hidden');
        progressBar.css('width', '0%').removeClass('from-green-500 to-emerald-600 from-red-500 to-rose-600 shadow-[0_0_8px_rgba(16,185,129,0.6)] shadow-[0_0_8px_rgba(244,63,94,0.6)]').addClass('from-blue-500 to-indigo-600 shadow-[0_0_8px_rgba(59,130,246,0.6)]');
        progressPercentage.text('0%').removeClass('text-green-600 dark:text-green-400 text-red-600 dark:text-red-400');
        statusText.text('Menyiapkan...').removeClass('text-green-600 dark:text-green-400 text-red-600 dark:text-red-400 font-bold');
        iconContainer.removeClass('bg-green-50 dark:bg-green-900/40 text-green-600 dark:text-green-400 bg-red-50 dark:bg-red-900/40 text-red-600 dark:text-red-400').addClass('bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400');
        iconContainer.html('<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>');
    };

    if (status === 'running') {
        runBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed pointer-events-none');
        statusContainer.removeClass('hidden').addClass('flex');

        let roundedProgress = Math.round(progress);
        progressBar.css('width', roundedProgress + '%');
        progressPercentage.text(roundedProgress + '%');
        statusText.text(message || 'Sedang memproses...');
    } else {
        runBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed pointer-events-none bg-gray-400');

        if (status === 'completed') {
            progressBar.css('width', '100%').removeClass('from-blue-500 to-indigo-600 shadow-[0_0_8px_rgba(59,130,246,0.6)]').addClass('from-green-500 to-emerald-600 shadow-[0_0_8px_rgba(16,185,129,0.6)]');
            progressPercentage.text('100%').addClass('text-green-600 dark:text-green-400');
            statusText.text('Selesai!').addClass('text-green-600 dark:text-green-400 font-bold');

            iconContainer.removeClass('bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400').addClass('bg-green-50 dark:bg-green-900/40 text-green-600 dark:text-green-400');
            iconContainer.html('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>');

            setTimeout(() => {
                alert(message || 'Proses depresiasi selesai!');
                $('#assetTable').DataTable().ajax.reload(null, false);
                $.post(window.routes.depreciationClearStatus, { _token: window.csrfToken });
                setTimeout(resetUI, 1500);
            }, 300);

        } else if (status === 'failed') {
            progressBar.removeClass('from-blue-500 to-indigo-600 shadow-[0_0_8px_rgba(59,130,246,0.6)]').addClass('from-red-500 to-rose-600 shadow-[0_0_8px_rgba(244,63,94,0.6)]');
            statusText.text('Gagal!').addClass('text-red-600 dark:text-red-400 font-bold');
            progressPercentage.addClass('text-red-600 dark:text-red-400');

            iconContainer.removeClass('bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400').addClass('bg-red-50 dark:bg-red-900/40 text-red-600 dark:text-red-400');
            iconContainer.html('<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>');

            setTimeout(() => {
                alert('Proses depresiasi gagal: ' + error);
                $.post(window.routes.depreciationClearStatus, { _token: window.csrfToken });
                setTimeout(resetUI, 1500);
            }, 300);
        } else if (status === 'idle') {
            resetUI();
        }
    }
}

// Bulk Action Functions
function updateBulkActionButtons() {
    const printBtn = $('#print-selected-btn');
    const downloadBtn = $('#download-selected-btn');
    const printText = $('#print-selected-text');
    const downloadText = $('#download-selected-text');

    const count = selectedAssetIds.size;
    const countText = `(${count})`;

    printText.text(`Print Selected ${countText}`);
    downloadText.text(`Download Selected ${countText}`);

    if (count > 0) {
        printBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed pointer-events-none disabled');
        downloadBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed pointer-events-none disabled');
    } else {
        printBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed pointer-events-none disabled');
        downloadBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed pointer-events-none disabled');
    }

    // Update hidden inputs for both print and download
    const idsArray = JSON.stringify(Array.from(selectedAssetIds));
    $('#selected-asset-ids').val(idsArray);
    $('#download-asset-ids').val(idsArray);
}

// Initialize DataTable
function initializeDataTable(apiUrl, assetNamesData, locationsData, departmentsData) {
    // Create filter inputs
    $('#assetTable thead tr:eq(0) th').each(function (i) {
        var title = $(this).text().trim();
        var cell = $('#filter-row').children().eq(i);

        if (i === 0 || i === 1 || i === 5 || i === 18) {
            return;
        } else if (i === 4) {
            let options = assetNamesData.map(assetName =>
                `<option value="${assetName.name}">${assetName.name}</option>`
            ).join('');
            $(cell).html(
                `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option selected value="">Select Asset Name</option>
                    ${options}
                </select>`
            );
        } else if (i === 9) {
            let options = locationsData.map(loc =>
                `<option value="${loc.name}">${loc.name}</option>`
            ).join('');
            $(cell).html(
                `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option selected value="">Select Location</option>
                    ${options}
                </select>`
            );
        } else if (i === 10) {
            let options = departmentsData.map(dept =>
                `<option value="${dept.name}">${dept.name}</option>`
            ).join('');
            $(cell).html(
                `<select class="filter-select w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option selected value="">Select Department</option>
                    ${options}
                </select>`
            );
        } else if (i === 11 || i === 14 || i === 15 || i === 16 || i === 17) {
            $(cell).html('<input type="number" min="1" class="filter-input w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm" placeholder="Search..." />');
        } else if (i === 12 || i === 13) {
            $(cell).html('<input type="date" class="filter-input w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm" placeholder="Search..." />');
        } else {
            $(cell).html('<input type="text" class="filter-input w-full p-2 mx-1 text-xs border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 outline-none transition-colors shadow-sm" placeholder="Search..." />');
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

    // Initialize DataTable
    var table = $('#assetTable').DataTable({
        dom: "<'flex flex-col md:flex-row justify-between items-center p-5 border-b border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300 font-medium'l><'text-sm relative custom-search-container'>>" +
            "<'overflow-x-auto'tr>" +
            "<'flex flex-col md:flex-row justify-between items-center p-5 border-t border-slate-200 dark:border-gray-700 gap-4 bg-transparent'<'text-sm text-gray-600 dark:text-gray-300'i><'text-sm'p>>",
        processing: true,
        serverSide: true,
        ajax: apiUrl,
        autoWidth: false,
        orderCellsTop: true,
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'px-6 py-4',
                render: function (data, type, row) {
                    return `<input type="checkbox" class="asset-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" data-asset-id="${row.id}" data-asset-number="${row.asset_number}">`;
                }
            },
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
        order: [[1, 'asc']],
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

            // Column filtering logic
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
                targets: 5,
                render: function (data, type, row) {
                    if (type === 'display') {
                        return 'Direct Ownership : ' + data;
                    }
                    return data;
                }
            },
            {
                targets: [12, 13],
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
                targets: [14, 16, 17],
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
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors duration-200');
        },
    });

    return table;
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof $ !== 'undefined') {
        // Get data from window object (set by blade template)
        const assetNamesData = window.assetNamesData || [];
        const locationsData = window.locationsData || [];
        const departmentsData = window.departmentsData || [];
        const routes = window.routes || {};

        // DEPRECIATION CONTROLS
        const runBtn = $('#run-all-btn');
        const statusContainer = $('#status-container');
        const statusText = $('#status-text');

        let eventSource = null;

        // Run All Depreciation button
        runBtn.on('click', function () {
            updateUI('running', 0);
            statusText.text('Mengirim permintaan...');

            $.post(routes.depreciationRunAll)
                .done(function () {
                    eventSource = listenForUpdates(routes.depreciationStream, routes.depreciationStatus);
                })
                .fail(function (xhr) {
                    alert('Gagal memulai proses: ' + (xhr.responseJSON?.message || 'Error tidak diketahui.'));
                    updateUI('idle');
                });
        });

        // Check status on page load
        $.get(routes.depreciationStatus).done(function (data) {
            if (data && (data.status === 'running' || data.status === 'queued')) {
                updateUI(data.status, data.progress, data.message);
                eventSource = listenForUpdates(routes.depreciationStream, routes.depreciationStatus);
            }
        });

        // INITIALIZE DATATABLE
        const table = initializeDataTable(routes.apiAsset, assetNamesData, locationsData, departmentsData);

        // Handle select-all checkbox
        $('#select-all-assets').on('change', function () {
            const isChecked = $(this).prop('checked');

            $('.asset-checkbox:visible').each(function () {
                const assetId = $(this).data('asset-id');
                const assetNumber = $(this).data('asset-number');

                $(this).prop('checked', isChecked);

                if (isChecked) {
                    selectedAssetIds.add(assetId.toString());
                    selectedAssetData.set(assetId.toString(), {
                        id: assetId,
                        asset_number: assetNumber
                    });
                } else {
                    selectedAssetIds.delete(assetId.toString());
                    selectedAssetData.delete(assetId.toString());
                }
            });

            updateBulkActionButtons();
        });

        // Handle individual checkbox clicks
        $(document).on('change', '.asset-checkbox', function () {
            const assetId = $(this).data('asset-id').toString();
            const assetNumber = $(this).data('asset-number');

            if ($(this).prop('checked')) {
                selectedAssetIds.add(assetId);
                selectedAssetData.set(assetId, {
                    id: assetId,
                    asset_number: assetNumber
                });
            } else {
                selectedAssetIds.delete(assetId);
                selectedAssetData.delete(assetId);
                $('#select-all-assets').prop('checked', false);
            }

            updateBulkActionButtons();
        });

        // Restore checkbox states after table draw
        table.on('draw', function () {
            $('.asset-checkbox').each(function () {
                const assetId = $(this).data('asset-id').toString();
                if (selectedAssetIds.has(assetId)) {
                    $(this).prop('checked', true);
                }
            });

            const visibleCheckboxes = $('.asset-checkbox:visible');
            const checkedCheckboxes = $('.asset-checkbox:visible:checked');
            $('#select-all-assets').prop('checked', visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedCheckboxes.length);
        });

        // TEMPLATE TOGGLE - Download Modal
        const downloadTemplate1Radio = $('#download-template1');
        const downloadTemplate2Radio = $('#download-template2');
        const downloadInfoTable = $('#download-info-table');

        // Function to toggle download info table visibility
        function toggleDownloadInfoTable() {
            if (downloadTemplate1Radio.is(':checked')) {
                // Template 1: QR Code Only - Hide info table
                downloadInfoTable.hide();
            } else if (downloadTemplate2Radio.is(':checked')) {
                // Template 2: QR Code With Info - Show info table
                downloadInfoTable.show();
            }
        }

        // Add event listeners to download radio buttons
        downloadTemplate1Radio.on('change', toggleDownloadInfoTable);
        downloadTemplate2Radio.on('change', toggleDownloadInfoTable);

        // Set initial state for download modal
        toggleDownloadInfoTable();

        // TEMPLATE TOGGLE - Print Modal
        const printTemplate1Radio = $('#print-template1');
        const printTemplate2Radio = $('#print-template2');
        const printInfoTable = $('#print-info-table');

        // Function to toggle print info table visibility
        function togglePrintInfoTable() {
            if (printTemplate1Radio.is(':checked')) {
                // Template 1: QR Code Only - Hide info table
                printInfoTable.hide();
            } else if (printTemplate2Radio.is(':checked')) {
                // Template 2: QR Code With Info - Show info table
                printInfoTable.show();
            }
        }

        // Add event listeners to print radio buttons
        printTemplate1Radio.on('change', togglePrintInfoTable);
        printTemplate2Radio.on('change', togglePrintInfoTable);

        // Set initial state for print modal
        togglePrintInfoTable();
    }
});

// Close QR modal with ESC key
document.addEventListener('keydown', (e) => {
    if (e.key === "Escape") closeInstantQr();
});

// Make functions available globally for inline onclick handlers
window.openInstantQr = openInstantQr;
window.closeInstantQr = closeInstantQr;