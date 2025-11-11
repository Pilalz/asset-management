import $ from 'jquery';
import 'datatables.net-dt';

document.addEventListener('DOMContentLoaded', () => {
    const oldPrices = window.oldPrices || [];
    const initialIds = Object.keys(oldPrices);

    const selectedAssetIds = new Set(initialIds);
    const selectedAssetData = new Map();

    const activeCurrency = window.activeCurrency || 'IDR';
    const globalLocale = (activeCurrency === 'USD') ? 'en-US' : 'id-ID';

    function formatCurrency(value, currencyCode = 'USD') {
        let locale = currencyCode === 'IDR' ? 'id-ID' : 'en-US';
        let number = parseFloat(value);
        if (isNaN(number)) return value;
        
        return number.toLocaleString(locale, {
            style: 'currency',
            currency: currencyCode,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        } catch (e) {
            return dateString;
        }
    }

    function calculateAndDisplayTotalESP() {
        let totalESP = 0;

        // 1. Loop "source of truth" (Map data)
        selectedAssetData.forEach((data, id) => {
            const quantity = parseFloat(data.quantity || 0);
            
            // 2. Ambil harga mentah (dari input) atau default ke NBV
            const rawPrice = parseFloat(
                data.price_raw !== undefined ? data.price_raw : (data.commercial_nbv || 0)
            );
            
            // 3. Kalkulasi total
            totalESP += (quantity * rawPrice);
        });

        // 4. Ambil elemen input ESP di tab "Formulir"
        const espValueInput = document.getElementById('esp-value');
        const espDisplayInput = document.getElementById('esp-display');

        // 5. Update nilainya
        if (espValueInput && espDisplayInput) {
            espValueInput.value = totalESP;
            espDisplayInput.value = formatNumber(totalESP); // Gunakan formatNumber (tanpa Rp)
        }
    }

    function formatNumber(value) {
        let number = parseFloat(value);
        if (isNaN(number)) number = 0;
        
        return new Intl.NumberFormat(globalLocale, {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    function renderChosenTable() {
        const $tbody = $('#chosenTable tbody');
        $tbody.empty();

        if (selectedAssetData.size === 0) {
            // Tampilkan pesan jika kosong
            $tbody.html('<tr><td colspan="14" class="text-center p-4">No assets selected.</td></tr>');
            return;
        }

        let i = 1;
        selectedAssetData.forEach((data, id) => {
            // Gunakan fungsi format yang sama dari DataTables
            const currency = data.currency_code || 'USD';
            const locale = (currency === 'IDR') ? 'id-ID' : 'en-US';
            const formattedPrice = data.price_formatted !== undefined ? data.price_formatted : formatNumber(data.commercial_nbv);
            const rawPrice = data.price_raw !== undefined ? data.price_raw : data.commercial_nbv;

            let currencySymbolHtml = '';
            if (activeCurrency === 'USD') {
                currencySymbolHtml = `
                    <svg class="flex w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2"/>
                    </svg>`;
            } else if (activeCurrency === 'IDR') {
                currencySymbolHtml = `<span class="flex dark:text-white">Rp</span>`;
            }
            const rowHtml = `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4">${i}</td>
                    <td class="px-6 py-4">${data.asset_number || '-'}</td>
                    <td class="px-6 py-4">${data.status || '-'}</td>
                    <td class="px-6 py-4">${data.asset_name_name || '-'}</td>
                    <td class="px-6 py-4">${data.description || '-'}</td>
                    <td class="px-6 py-4">${data.location_name || '-'}</td>
                    <td class="px-6 py-4">${data.department_name || '-'}</td>
                    <td class="px-6 py-4">${data.quantity || '-'}</td>
                    <td class="px-6 py-4">${formatDate(data.capitalized_date)}</td>
                    <td class="px-6 py-4">${formatCurrency(data.acquisition_value, currency)}</td>
                    <td class="px-6 py-4">${formatCurrency(data.commercial_accum_depre, currency)}</td>
                    <td class="px-6 py-4">${formatCurrency(data.commercial_nbv, currency)}</td>
                    <td class="px-6 py-4">${formatCurrency(data.fiscal_accum_depre, currency)}</td>
                    <td class="px-6 py-4">${formatCurrency(data.fiscal_nbv, currency)}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            ${currencySymbolHtml} 
                            <input type="text" class="chosen-price-display flex px-1 w-auto text-sm border-0 border-b-2 border-gray-300 text-gray-900 appearance-none dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:ring-0" data-asset-id="${id}" value="${formattedPrice}"/>
                        </div>
                        <input type="hidden" name="prices[${id}]" class="chosen-price-value" data-asset-id="${id}" value="${rawPrice}">
                    </td>
                </tr>
            `;
            $tbody.append(rowHtml);
            i++;
        });
    }

    function updateSelection() {
        const selectedCount = selectedAssetIds.size;
        $('#selected-count-display').text(`(${selectedCount})`);
        renderChosenTable();
        calculateAndDisplayTotalESP();
    }

    function loadInitialData(ids) {
        if (ids.length === 0) {
            updateSelection();
            return;
        }

        // Panggil API baru yang kita buat
        $.ajax({
            url: window.getAssetsByIdsUrl, // Dari variabel global di Blade
            type: 'POST',
            data: {
                ids: ids,
                _token: window.csrfToken // Dari variabel global di Blade
            },
            success: function(data) {
                for (const id in data) {
                    let asset = data[id];
                    
                    if (oldPrices.hasOwnProperty(id)) {
                        const rawPrice = oldPrices[id]; // Ambil harga dari old()
                        
                        // Simpan harga dari old() ke "source of truth"
                        asset.price_raw = rawPrice;
                        asset.price_formatted = formatNumber(rawPrice);
                    }
                    selectedAssetData.set(String(id), asset);
                }
                updateSelection(); // Render tabel "Chosen" dengan data
            },
            error: function() {
                console.error('Failed to load pre-selected asset data.');
                updateSelection(); // Tetap panggil untuk update count
            }
        });
    }

    loadInitialData(initialIds);

    if (typeof $ !== 'undefined') {
        $('#assetTable thead tr:eq(0) th').each(function(i) {
            var title = $(this).text().trim();
            var cell = $('#filter-row').children().eq(i);
            if (i === 0) {
                return;
            }
            $(cell).html('<input type="text" class="w-auto p-2 mx-2 my-2 text-xs border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Search..." />');
        });

        const table = $('#assetTable').DataTable({
            dom:  "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'l><'text-sm'f>>" +
                "<'overflow-x-auto'tr>" +
                "<'flex flex-col sm:flex-row justify-between items-center p-4 bg-gray-50 dark:bg-gray-700'<'text-sm text-gray-700 dark:text-gray-200'i><'text-sm'p>>",
            processing: true,
            serverSide: true,
            ajax: window.getAssetsByIdsUrl.replace('get-assets-by-ids', 'disposal-asset-find'),
            autoWidth: false,
            orderCellsTop: true,
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false, className: 'text-center' },
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
                { data: 'fiscal_useful_life_month', name: 'fiscal_useful_life_month' },
                { data: 'fiscal_accum_depre', name: 'fiscal_accum_depre' },
                { data: 'fiscal_nbv', name: 'fiscal_nbv' },
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
                    var cell = $('#assetTable thead #filter-row').children().eq(column.index());
                    
                    if (column.settings()[0].bSearchable === false) {
                        return;
                    }
                    
                    var input = $('input', cell);
                    input.on('keyup change clear', function(e) {
                        e.stopPropagation();
                        if (column.search() !== this.value) {
                            column.search(this.value, false, false).draw();
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
                    targets: [14, 16, 17, 19,20], 
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

            createdRow: function( row, data, dataIndex ) {
                $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
            },
        });

        // Event listener untuk checkbox di setiap baris
        $('#assetTable tbody').on('change', '.asset-checkbox', function() {
            const assetId = String($(this).val());
            const rowData = table.row($(this).closest('tr')).data();

            if (this.checked) {
                selectedAssetIds.add(assetId);
                selectedAssetData.set(assetId, rowData);
            } else {
                selectedAssetIds.delete(assetId);
                selectedAssetData.delete(assetId);
            }
            updateSelection();
        });

        // Event listener untuk checkbox "select all" di header
        $('#select-all-assets').on('change', function() {
            const isChecked = this.checked;
            $('#assetTable tbody .asset-checkbox').each(function() {
                $(this).prop('checked', isChecked);
                
                const assetId = String($(this).val());
                const rowData = table.row($(this).closest('tr')).data();

                if (isChecked) {
                    selectedAssetIds.add(assetId);
                    selectedAssetData.set(assetId, rowData);
                } else {
                    selectedAssetIds.delete(assetId);
                    selectedAssetData.delete(assetId);
                }
            });
            updateSelection();
        });

        // Event listener saat DataTable digambar ulang (pindah halaman, sorting, dll.)
        table.on('draw', function() {
            // Pastikan checkbox tetap tercentang sesuai data yang tersimpan
            $('#assetTable tbody .asset-checkbox').each(function() {
                const assetId = String($(this).val());
                if (selectedAssetIds.has(assetId)) {
                    $(this).prop('checked', true);
                    
                    // Ini untuk menangani data 'old()'
                    if (!selectedAssetData.has(assetId) && initialIds.length > 0) { 
                        const rowData = table.row($(this).closest('tr')).data();
                        if (rowData) {
                            selectedAssetData.set(assetId, rowData);
                            updateSelection();
                        }
                    }
                } else {
                    $(this).prop('checked', false);
                }
            });
            // Reset checkbox "select all"
            $('#select-all-assets').prop('checked', false);
        });
    }

    function autoFormatCurrency(visibleInput, hiddenInput) {
        const locale = globalLocale;

        function format(value) {
            let cleanValue = value.replace(/[^\d]/g, '');
            hiddenInput.value = cleanValue;
            visibleInput.value = cleanValue ? new Intl.NumberFormat(locale).format(cleanValue) : '';
        }

        format(visibleInput.value);
        visibleInput.addEventListener('input', (e) => format(e.target.value));        
    }

    // Terapkan fungsi ke input Nilai Value
    const nbvDisplay = document.getElementById('nbv-display');
    const nbvValue = document.getElementById('nbv-value');
    if (nbvDisplay && nbvValue) {
        autoFormatCurrency(nbvDisplay, nbvValue);
    }

    // Terapkan fungsi ke input Kurs
    const kursDisplay = document.getElementById('kurs-display');
    const kursValue = document.getElementById('kurs-value');
    if (kursDisplay && kursValue) {
        autoFormatCurrency(kursDisplay, kursValue);
    }

    $('#chosenTable tbody').on('input', '.chosen-price-display', function(e) {
        const visibleInput = e.target;
        const assetId = String($(visibleInput).data('asset-id'));
        const $hiddenInput = $(`#chosenTable tbody .chosen-price-value[data-asset-id="${assetId}"]`);
        
        const assetData = selectedAssetData.get(assetId);
        if (!assetData) return; // Pengaman jika data tidak ditemukan

        const currencyCode = assetData.currency_code || 'IDR';
        const locale = (currencyCode === 'USD') ? 'en-US' : 'id-ID';

        // 1. Bersihkan nilai
        let cleanValue = visibleInput.value.replace(/[^\d]/g, '');
        
        // 2. Update input tersembunyi
        $hiddenInput.val(cleanValue);
        
        // 3. Format input terlihat
        visibleInput.value = cleanValue ? new Intl.NumberFormat(locale).format(cleanValue) : '';
        
        // 4. Update "Source of Truth"
        assetData.price_raw = cleanValue;
        assetData.price_formatted = visibleInput.value;
        selectedAssetData.set(assetId, assetData);

        calculateAndDisplayTotalESP();
    });

    function filterUsersByRole(row) {
        const roleInput = row.querySelector('.approval-role');
        const userSelect = row.querySelector('.approval-user-select');
        
        if (!roleInput || !userSelect) return;

        const selectedRole = roleInput.value;

        for (const option of userSelect.options) {
            if (option.value === '') continue;

            if (option.dataset.role === selectedRole) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';

                if (option.selected) {
                    userSelect.value = '';
                }
            }
        }
    }

    // Terapkan filter ke semua baris yang ada saat halaman dimuat
    document.querySelectorAll('.approval-row').forEach(row => {
        filterUsersByRole(row);
    });
});