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
function listenForUpdates(streamUrl) {
    let eventSource = null;

    // Close old connection if exists
    if (eventSource) {
        eventSource.close();
    }

    // Open new connection to stream
    eventSource = new EventSource(streamUrl);

    // Handle incoming data
    eventSource.onmessage = function (event) {
        const data = JSON.parse(event.data);

        if (!data) {
            eventSource.close();
            updateUI('idle');
            return;
        }

        // Update UI based on received status
        updateUI(data.status, data.progress, data.message, data.error);

        // Close connection if completed or failed
        if (data.status === 'completed' || data.status === 'failed') {
            eventSource.close();
        }
    };

    // Handle connection errors
    eventSource.onerror = function () {
        console.error("SSE connection failed. Closing connection.");
        eventSource.close();
        updateUI('idle');
    };

    return eventSource;
}

function updateUI(status, progress = 0, message = '', error = '') {
    const runBtn = $('#run-all-btn');
    const statusContainer = $('#status-container');
    const statusText = $('#status-text');

    if (status === 'running') {
        runBtn.prop('disabled', true).addClass('cursor-not-allowed bg-gray-400');
        statusContainer.removeClass('hidden').addClass('flex');
        statusText.text('Sedang memproses... (' + Math.round(progress) + '%)');
    } else {
        runBtn.prop('disabled', false).removeClass('cursor-not-allowed bg-gray-400');
        statusContainer.removeClass('flex').addClass('hidden');

        if (status === 'completed') {
            alert(message || 'Proses depresiasi selesai!');
            $('#assetTable').DataTable().ajax.reload(null, false);
            $.post(window.routes.depreciationClearStatus, { _token: window.csrfToken });
        } else if (status === 'failed') {
            alert('Proses depresiasi gagal: ' + error);
            $.post(window.routes.depreciationClearStatus, { _token: window.csrfToken });
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
        printBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
        downloadBtn.prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
    } else {
        printBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
        downloadBtn.prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
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
            $(cell).html('<input type="number" min="1" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        } else if (i === 12 || i === 13) {
            $(cell).html('<input type="date" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        } else {
            $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        }
    });

    // Initialize DataTable
    var table = $('#assetTable').DataTable({
        dom: "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
            "<'overflow-x-auto'tr>" +
            "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
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
            search: "Search : ",
            searchPlaceholder: "Cari di sini...",
        },
        initComplete: function () {
            $('.dt-search input').addClass('w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');

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
                className: 'px-6 py-4'
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
            $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
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
            if (!confirm('Apakah Anda yakin ingin menjalankan depresiasi untuk semua aset?')) return;

            updateUI('running', 0);
            statusText.text('Mengirim permintaan...');

            $.post(routes.depreciationRunAll)
                .done(function () {
                    eventSource = listenForUpdates(routes.depreciationStream);
                })
                .fail(function (xhr) {
                    alert('Gagal memulai proses: ' + (xhr.responseJSON?.message || 'Error tidak diketahui.'));
                    updateUI('idle');
                });
        });

        // Check status on page load
        $.get(routes.depreciationStatus).done(function (data) {
            if (data && data.status === 'running') {
                eventSource = listenForUpdates(routes.depreciationStream);
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