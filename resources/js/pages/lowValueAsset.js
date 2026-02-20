import $ from 'jquery';
import 'datatables.net-dt';

// Global variables
const selectedAssetIds = new Set();

// QR Modal Functions
export function openInstantQr(content, name, qrcodeUrl) {
    const modal = document.getElementById('instantQrModal');
    const container = document.getElementById("qrcode-container");

    const downloadPNG = document.getElementById('pngDownload');
    const downloadPDF = document.getElementById('pdfDownload');
    const downloadSVG = document.getElementById('svgDownload');

    document.getElementById('modalTitle').innerText = name;

    // Set download URL
    if (downloadPNG) downloadPNG.href = qrcodeUrl + "?format=png";
    if (downloadPDF) downloadPDF.href = qrcodeUrl + "?format=pdf";
    if (downloadSVG) downloadSVG.href = qrcodeUrl + "?format=svg";

    // Clear & Generate QR
    container.innerHTML = "";
    if (window.QRCode) {
        new QRCode(container, {
            text: content,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    } else {
        console.error("QRCode library not loaded");
        container.innerText = "Error: QRCode module not loaded.";
    }

    // Show modal with flex display
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Lock page scroll
    document.body.style.overflow = 'hidden';
}

export function closeInstantQr() {
    const modal = document.getElementById('instantQrModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Restore scroll
    document.body.style.overflow = 'auto';
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
    $('#print-asset-ids').val(idsArray);
}

// Initialize DataTable
function initializeDataTable(apiUrl) {
    // Create filter inputs
    // Adjusted indices for filtering inputs since we added a checkbox column at index 0.
    // Original indices: 0(No), 1(AssetNumber)... 
    // New indices: 0(Checkbox), 1(No), 2(AssetNumber)...

    $('#assetTable thead tr:eq(0) th').each(function (i) {
        var cell = $('#filter-row').children().eq(i);
        // Skip Checkbox(0), No(1) and Actions(18)
        if (i === 0 || i === 1 || i === 18) {
            return;
        }

        // Example special handling (if needed)
        // For simple text inputs:
        $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
    });

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
            { data: 'DT_RowIndex', name: 'id', orderable: true, searchable: false }, // Index 1
            { data: 'asset_number', name: 'asset_number' },
            { data: 'status', name: 'status' },
            { data: 'asset_name_name', name: 'asset_name_name' },
            { data: 'asset_class_obj', name: 'asset_class_obj' },
            { data: 'user', name: 'user' },
            { data: 'description', name: 'description' },
            { data: 'detail', name: 'detail' },
            { data: 'sn', name: 'sn' },
            { data: 'po_no', name: 'po_no' },
            { data: 'location_name', name: 'location_name' },
            { data: 'department_name', name: 'department_name' },
            { data: 'quantity', name: 'quantity' },
            { data: 'capitalized_date', name: 'capitalized_date' },
            { data: 'acquisition_value', name: 'acquisition_value' },
            { data: 'commercial_useful_life_month', name: 'commercial_useful_life_month' },
            { data: 'commercial_nbv', name: 'commercial_nbv' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']], // Order by No
        language: {
            search: "Search : ",
            searchPlaceholder: "Cari di sini...",
        },
        initComplete: function () {
            $('.dt-search input').addClass('w-full sm:w-auto bg-white-50 border border-white-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');

            // --- Logika untuk filter per kolom ---
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
            });
        },
        columnDefs: [
            {
                targets: 0,
                className: 'px-6 py-4'
            },
            {
                targets: 5, // Status? No, wait. 
                // Index mapping:
                // 0: Check, 1: No, 2: Num, 3: Status, 4: Name, 5: ObjAcc
                // Original code target 4 was ObjAcc (Direct Ownership).
                // New index for ObjAcc is 5.
                render: function (data, type, row) {
                    if (type === 'display') {
                        return 'Direct Ownership : ' + data;
                    }
                    return data;
                }
            },
            {
                targets: 14, // Capitalized Date (was 13)
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
                targets: [15, 17], // Acq Value (was 14), NBV (was 16)
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

    // Checkbox restoration logic on draw
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

    return table;
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof $ !== 'undefined') {
        const routes = window.routes || {};

        // INITIALIZE DATATABLE
        const table = initializeDataTable(routes.apiAssetLVA);

        // Handle select-all checkbox
        $('#select-all-assets').on('change', function () {
            const isChecked = $(this).prop('checked');

            $('.asset-checkbox:visible').each(function () {
                const assetId = $(this).data('asset-id');

                $(this).prop('checked', isChecked);

                if (isChecked) {
                    selectedAssetIds.add(assetId.toString());
                } else {
                    selectedAssetIds.delete(assetId.toString());
                }
            });

            updateBulkActionButtons();
        });

        // Handle individual checkbox clicks
        $(document).on('change', '.asset-checkbox', function () {
            const assetId = $(this).data('asset-id').toString();

            if ($(this).prop('checked')) {
                selectedAssetIds.add(assetId);
            } else {
                selectedAssetIds.delete(assetId);
                $('#select-all-assets').prop('checked', false);
            }

            updateBulkActionButtons();
        });

        // TEMPLATE TOGGLE - Download Modal
        const downloadTemplate1Radio = $('#download-template1');
        const downloadTemplate2Radio = $('#download-template2');
        const downloadInfoTable = $('#download-info-table');

        function toggleDownloadInfoTable() {
            if (downloadTemplate1Radio.is(':checked')) {
                downloadInfoTable.hide();
            } else if (downloadTemplate2Radio.is(':checked')) {
                downloadInfoTable.show();
            }
        }
        // Note: LVA might not have template radio in download modal (blade didn't show it), 
        // but let's keep it safe or check blade. 
        // Actually, looking at the blade I just wrote, Download Modal has FORMAT selection (png/pdf/svg)
        // NOT templates. Print Modal has TEMPLATES. 
        // So I should remove download template logic if it's not in HTML.
        // But Print Modal DOES have radio buttons for template1/template2.

        // TEMPLATE TOGGLE - Print Modal
        const printTemplate1Radio = $('#print-template1');
        const printTemplate2Radio = $('#print-template2');
        const printInfoTable = $('#print-info-table');

        function togglePrintInfoTable() {
            if (printTemplate1Radio.is(':checked')) {
                printInfoTable.hide();
            } else if (printTemplate2Radio.is(':checked')) {
                printInfoTable.show();
            }
        }

        printTemplate1Radio.on('change', togglePrintInfoTable);
        printTemplate2Radio.on('change', togglePrintInfoTable);
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
