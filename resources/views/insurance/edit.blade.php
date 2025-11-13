@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Gaya untuk Light Mode */
            #assetTable tbody tr:hover {
                background-color: #F9FAFB !important; /* Tailwind's hover:bg-gray-50 */
            }

            /* Gaya untuk Dark Mode */
            .dark #assetTable tbody tr:hover {
                background-color: #374151 !important; /* Tailwind's dark:hover:bg-gray-700 (contoh) */
            }

            /* Menghapus background bawaan dari kolom yang diurutkan */
            table.dataTable tbody tr > .sorting_1,
            table.dataTable tbody tr > .sorting_2,
            table.dataTable tbody tr > .sorting_3 {
                background-color: inherit !important;
            }

            .dark .dt-search,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:hover,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:active,
            .dark div.dt-container .dt-paging .dt-paging-button,
            .dark div.dt-container .dt-paging .ellipsis{
                color: #e4e6eb !important;
            }

            html.dark .dt-container .dt-paging .dt-paging-button.current:hover{
                color: white !important;
            }

            div.dt-container select.dt-input {
                padding: 4px 25px 4px 4px;
            }

            select.dt-input option{
                text-align: center !important;
            }
        </style>
    @endpush
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('insurance.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Insurance
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="p-5">

        <div class="border-b bg-white rounded-t-lg border-gray-200 dark:border-gray-700 dark:bg-gray-800">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="formulir-tab" data-tabs-target="#formulir" type="button" role="tab" aria-controls="formulir" aria-selected="false">Form <span class="text-red-900 dark:text-red-400">*</span></button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="asset-tab" data-tabs-target="#asset" type="button" role="tab" aria-controls="asset" aria-selected="false">Asset <span class="text-red-900 dark:text-red-400">*</span></button>
                </li>
            </ul>
        </div>

        <form class="max-w mx-auto" action="{{ route('insurance.update', $insurance->id) }}" method="POST">
            <div id="default-tab-content">
                <div class="hidden rounded-b-lg" id="formulir" role="tabpanel" aria-labelledby="formulir-tab">
                    <div class="relative overflow-x-auto py-5 px-6 sm:rounded-b-lg bg-white dark:bg-gray-800 dark:text-white">
                        @csrf
                        @method('PUT')

                        <div class="mb-5 flex content-center">
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Polish No. <span class="text-red-900 dark:text-red-400">*</span></label>
                            <span> : </span>
                            <input type="text" name="polish_no" value="{{ old('polish_no', $insurance->polish_no) }}" class="px-1 w-full text-sm border-0 border-b-2 border-gray-300 text-gray-900 appearance-none dark:bg-gray-800 dark:text-white focus:ring-0"/>
                            @error('polish_no')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5 flex content-center">
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Start Date <span class="text-red-900 dark:text-red-400">*</span></label>
                            <span> : </span>
                            <input type="date" name="start_date" value="{{ old('start_date', $insurance->start_date) }}" class="px-1 w-full text-sm border-0 border-b-2 border-gray-300 text-gray-900 appearance-none dark:bg-gray-800 dark:text-white focus:ring-0"/>
                            @error('start_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5 flex content-center">
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">End Date <span class="text-red-900 dark:text-red-400">*</span></label>
                            <span> : </span>
                            <input type="date" name="end_date" value="{{ old('end_date', $insurance->end_date) }}" class="px-1 w-full text-sm border-0 border-b-2 border-gray-300 text-gray-900 appearance-none dark:bg-gray-800 dark:text-white focus:ring-0"/>
                            @error('end_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5 flex content-center">
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Instance Name </label>
                            <span> : </span>
                            <input type="text" name="instance_name" value="{{ old('instance_name', $insurance->instance_name) }}" class="px-1 w-full text-sm border-0 border-b-2 border-gray-300 text-gray-900 appearance-none dark:bg-gray-800 dark:text-white focus:ring-0"/>
                            @error('instance_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5 flex content-center">
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Annual Payment </label>
                            <span> : </span>
                            <input type="text" id="annual-display" value="{{ old('annual_premium', $insurance->annual_premium) }}" class="px-1 w-full text-sm border-0 border-b-2 border-gray-300 text-gray-900 appearance-none dark:bg-gray-800 dark:text-white focus:ring-0"/>
                            <input type="hidden" id="annual-value" name="annual_premium" value="{{ old('annual_premium', $insurance->annual_premium) }}" />
                            @error('annual_premium')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5 flex flex-col gap-2 content-center">
                            <label class="flex w-48 text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-900 dark:text-red-400">*</span></label>
                            <div class="flex flex-row gap-5">
                                <div class="flex items-center ps-4 border border-gray-200 hover:border-green-600 rounded-sm dark:border-gray-700">
                                    <input id="bordered-radio-1" type="radio" value="Active" name="status" class="w-4 h-4 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 dark:focus:ring-green-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('status', $insurance->status) == "Active" ? 'checked' : '' }}>
                                    <label for="bordered-radio-1" class="w-full py-4 pr-4 ms-2 text-sm font-medium"><span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded-sm p-1">Active</span></label>
                                </div>
                                <div class="flex items-center ps-4 border border-gray-200 hover:border-gray-600 rounded-sm dark:border-gray-700">
                                    <input id="bordered-radio-2" type="radio" value="Inactive" name="status" class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('status', $insurance->status) == "Inactive" ? 'checked' : '' }}>
                                    <label for="bordered-radio-2" class="w-full py-4 pr-4 ms-2 text-sm font-medium"><span class="bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300 rounded-sm p-1">Inactive</span></label>
                                </div>
                            </div>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="company_id" value="{{ Auth::user()->last_active_company_id }}" required />
                    </div>
                </div>

                <div class="hidden rounded-b-lg" id="asset" role="tabpanel" aria-labelledby="asset-tab">
                    <div class="relative overflow-x-auto py-5 px-6 sm:rounded-b-lg bg-white dark:bg-gray-800">
                        <table id="assetTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-100">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3"><input type="checkbox" id="select-all-assets"></th>
                                    <th scope="col" class="px-6 py-3">No</th>
                                    <th scope="col" class="px-6 py-3">Asset Number</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Asset Name</th>
                                    <th scope="col" class="px-6 py-3">Obj Acc</th>
                                    <th scope="col" class="px-6 py-3">Description</th>
                                    <th scope="col" class="px-6 py-3">Pareto</th>
                                    <th scope="col" class="px-6 py-3">PO No</th>
                                    <th scope="col" class="px-6 py-3">Location</th>
                                    <th scope="col" class="px-6 py-3">Department</th>
                                    <th scope="col" class="px-6 py-3">Qty</th>
                                    <th scope="col" class="px-6 py-3">Capitalized Date</th>
                                    <th scope="col" class="px-6 py-3">Start Depre Date</th>
                                    <th scope="col" class="px-6 py-3">Acquisition Value</th>
                                    <th scope="col" class="px-6 py-3">Useful Life Month</th>
                                    <th scope="col" class="px-6 py-3">Accum Depre</th>
                                    <th scope="col" class="px-6 py-3">Net Book Value</th>
                                </tr>
                                <tr id="filter-row">
                                    <th><input type="hidden" name="asset_ids" id="selected-asset-ids"></th>
                                    <th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th>
                                    <th></th><th></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                        <div class="p-4">
                            <span id="selected-count-display" class="font-bold dark:text-white">0 asset(s) selected</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-5 pb-5 rounded-b-lg bg-white shadow-md dark:bg-gray-800">
                @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                        <span class="font-medium">Validation Failed!</span> Please check the errors below:
                        <ul class="mt-1.5 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Update</button>
                    <a href="{{ route('insurance.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const preselectedIds = @json($selectedAssetIds->all() ?? []);
        const selectedAssetIds = new Set(preselectedIds.map(String));
        // Temukan semua elemen notifikasi yang memiliki class 'auto-dismiss-alert'
        const alertElements = document.querySelectorAll('.auto-dismiss-alert');

        alertElements.forEach(targetEl => {
            // Ambil tombol 'close' di dalam notifikasi (jika ada)
            const triggerEl = targetEl.querySelector('[data-dismiss-target]');

            // Opsi yang Anda inginkan
            const options = {
                transition: 'transition-opacity',
                duration: 1000,
                timing: 'ease-out',
                onHide: (context, targetEl) => {
                    console.log(`Element dengan ID ${targetEl.id} telah disembunyikan.`);
                }
            };

            // Buat instance Dismiss dari Flowbite
            const dismiss = new Dismiss(targetEl, triggerEl, options);

            // (Opsional) Sembunyikan notifikasi secara otomatis setelah 5 detik
            setTimeout(() => {
                dismiss.hide();
            }, 3000);
        });

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
                ajax: "{{ route('api.disposal-asset-find') }}",
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
                    {
                        targets: 4, 
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return 'Direct Ownership : ' + data;
                            }
                            return data;
                        }
                    },
                    {
                        targets: [11, 12],
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

                createdRow: function( row, data, dataIndex ) {
                    $(row).addClass('bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600');
                },
            });

            function updateSelection() {
                const selectedCount = selectedAssetIds.size;
                $('#selected-count-display').text(`${selectedCount} asset(s) selected`);
                $('#selected-asset-ids').val(Array.from(selectedAssetIds).join(','));
            }

            updateSelection();

            // Event listener untuk checkbox di setiap baris
            $('#assetTable tbody').on('change', '.asset-checkbox', function() {
                const assetId = $(this).val();
                if (this.checked) {
                    selectedAssetIds.add(assetId);
                } else {
                    selectedAssetIds.delete(assetId);
                }
                updateSelection();
            });

            // Event listener untuk checkbox "select all" di header
            $('#select-all-assets').on('change', function() {
                const isChecked = this.checked;
                // Hanya pengaruhi checkbox di halaman saat ini
                $('#assetTable tbody .asset-checkbox').each(function() {
                    $(this).prop('checked', isChecked);
                    const assetId = $(this).val();
                    if (isChecked) {
                        selectedAssetIds.add(assetId);
                    } else {
                        selectedAssetIds.delete(assetId);
                    }
                });
                updateSelection();
            });

            // Event listener saat DataTable digambar ulang (pindah halaman, sorting, dll.)
            table.on('draw', function() {
                // Pastikan checkbox tetap tercentang sesuai data yang tersimpan
                $('#assetTable tbody .asset-checkbox').each(function() {
                    if (selectedAssetIds.has($(this).val())) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
                // Reset checkbox "select all"
                $('#select-all-assets').prop('checked', false);
            });

            table.columns().every(function() {
                var that = this;
                
                // Event untuk filtering saat mengetik
                $('input', $('#assetTable thead #filter-row').children().eq(this.index())).on('keyup change clear', function(e) {
                    e.stopPropagation(); // Hentikan event agar tidak memicu sorting
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }

        function autoFormatCurrency(visibleInput, hiddenInput) {
            const currencyCode = '{{ $activeCompany->currency ?? 'IDR' }}';
            const locale = (currencyCode === 'USD') ? 'en-US' : 'id-ID';

            // Inisialisasi nilai awal jika ada (dari old input)
            if (visibleInput.value) {
                const cleanValue = visibleInput.value.replace(/[^\d]/g, '');
                const formattedValue = new Intl.NumberFormat(locale).format(cleanValue);
                visibleInput.value = formattedValue;
                hiddenInput.value = cleanValue;
            }

            visibleInput.addEventListener('input', function(e) {
                // 1. Ambil nilai input dan hapus semua karakter selain angka
                let cleanValue = e.target.value.replace(/[^\d]/g, '');

                // 2. Simpan nilai bersih ke input tersembunyi
                hiddenInput.value = cleanValue;
                
                // 3. Format nilai yang terlihat dengan pemisah ribuan
                if (cleanValue) {
                    const formattedValue = new Intl.NumberFormat(locale).format(cleanValue);
                    e.target.value = formattedValue;
                } else {
                    e.target.value = '';
                }
            });
        }

        // Terapkan fungsi ke input Nilai Value
        const annualDisplay = document.getElementById('annual-display');
        const annualValue = document.getElementById('annual-value');
        if (annualDisplay && annualValue) {
            autoFormatCurrency(annualDisplay, annualValue);
        }
    });
</script>
@endpush