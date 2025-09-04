@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Asset
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Arrival</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Complete</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <form class="max-w mx-auto" action="{{ route('asset.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="flex flex-row gap-5 items-start">
                    <div class="w-1/2">
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Number <span class="text-red-900">*</span></label>
                            <input type="text" name="asset_number" value="{{ old('asset_number', $asset->asset_number) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('asset_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Name <span class="text-red-900">*</span></label>
                            <input type="text" value="{{ old('asset_name_id', $asset->assetName->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            <input type="hidden" name="asset_name_id" value="{{ old('asset_name_id', $asset->assetName->id) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('asset_name_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-900">*</span></label>
                            <input type="text" name="status" value="{{ old('status', $asset->status) }}" class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required readonly />
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description <span class="text-red-900">*</span></label>
                            <input type="text" name="description" value="{{ old('description', $asset->description) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Detail </label>
                            <input type="text" name="detail" value="{{ old('detail', $asset->detail) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('detail')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pareto </label>
                            <input type="text" name="pareto" value="{{ old('pareto', $asset->pareto) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('pareto')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit No </label>
                            <input type="text" name="unit_no" value="{{ old('unit_no', $asset->unit_no) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('unit_no')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sn Chassis </label>
                            <input type="text" name="sn_chassis" value="{{ old('sn_chassis', $asset->sn_chassis) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('sn_chassis')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="w-1/2">
                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sn Engine </label>
                            <input type="text" name="sn_engine" value="{{ old('sn_engine', $asset->sn_engine) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                            @error('sn_engine')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PO No <span class="text-red-900">*</span></label>
                            <input type="text" name="po_no" value="{{ old('po_no', $asset->po_no) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('po_no')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location <span class="text-red-900">*</span></label>
                            <input type="text" value="{{ old('location_id', $asset->location->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            <input type="hidden" name="location_id" value="{{ old('location_id', $asset->location->id) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('location_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department <span class="text-red-900">*</span></label>
                            <input type="text" value="{{ old('department_id', $asset->department->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            <input type="hidden" name="department_id" value="{{ old('department_id', $asset->department->id) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('department_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity <span class="text-red-900">*</span></label>
                            <input type="text" name="quantity" value="{{ old('quantity', $asset->quantity) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('quantity')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($asset->depreciations->count() == 0)
                            <div class="mb-5">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Capitalized Date <span class="text-red-900">*</span></label>
                                <input type="date" name="capitalized_date" id="capitalized_date" value="{{ old('capitalized_date', $asset->capitalized_date?->format('Y-m-d')) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                                @error('capitalized_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="mb-5">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Capitalized Date <span class="text-red-900">*</span></label>
                                <input type="date" name="capitalized_date" value="{{ old('capitalized_date', $asset->capitalized_date?->format('Y-m-d')) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                                @error('capitalized_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        @if ($asset->asset_type === 'FA')
                            <div class="mb-5">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Depre Date <span class="text-red-900">*</span></label>
                                <input type="date" name="start_depre_date" id="start_depre_date" value="{{ old('start_depre_date', $asset->start_depre_date?->format('Y-m-d')) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                @error('start_depre_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Acquisition Value <span class="text-red-900">*</span></label>
                            <div class="relative ">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2"/>
                                    </svg>
                                </div>
                                <input type="text" id="acquisition_value-display" value="{{ old('acquisition_value', $asset->acquisition_value) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                                <input type="hidden" name="acquisition_value" id="acquisition_value-value" value="{{ old('acquisition_value', $asset->acquisition_value) }}" required />
                                @error('acquisition_value')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Useful Life Month <span class="text-red-900">*</span></label>
                            <input type="text" name="useful_life_month" value="{{ old('useful_life_month', $asset->useful_life_month) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required />
                            @error('useful_life_month')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Update</button>
                <a href="{{ route('asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const capitalizedDateInput = document.getElementById('capitalized_date');
        const startDepreDateInput = document.getElementById('start_depre_date');

        if (capitalizedDateInput && startDepreDateInput) {
            
            const setStartDepreDate = () => {
                const capitalizedDateValue = capitalizedDateInput.value;
                if (capitalizedDateValue) {
                    const date = new Date(capitalizedDateValue);
                    const year = date.getFullYear();
                    // getMonth() dimulai dari 0 (Januari=0), jadi perlu + 1
                    // String(...).padStart(2, '0') untuk memastikan format bulan selalu 2 digit (e.g., 08)
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const newStartDate = `${year}-${month}-01`;
                    startDepreDateInput.value = newStartDate;
                }
            };

            // Tambahkan event listener untuk dijalankan saat tanggal berubah
            capitalizedDateInput.addEventListener('change', setStartDepreDate);

            // Jalankan sekali saat halaman dimuat untuk mengatur nilai awal
            setStartDepreDate();
        }
        
        /**
         * Fungsi untuk memformat input menjadi format mata uang US Dollar.
         * @param {HTMLInputElement} visibleInput - Input yang dilihat pengguna.
         * @param {HTMLInputElement} hiddenInput - Input tersembunyi untuk menyimpan nilai asli.
         */
        function autoFormatCurrency(visibleInput, hiddenInput) {
            // Fungsi untuk memformat nilai
            const formatValue = (value) => {
                // Hapus semua karakter selain angka
                let cleanValue = value.toString().replace(/[^\d]/g, '');
                if (cleanValue) {
                    // Simpan nilai bersih ke input tersembunyi
                    hiddenInput.value = cleanValue;
                    // Format nilai yang terlihat
                    visibleInput.value = new Intl.NumberFormat('en-US').format(cleanValue);
                } else {
                    hiddenInput.value = '';
                    visibleInput.value = '';
                }
            };

            // Format nilai awal saat halaman dimuat
            formatValue(visibleInput.value);

            // Tambahkan event listener untuk memformat saat pengguna mengetik
            visibleInput.addEventListener('input', (e) => {
                formatValue(e.target.value);
            });
        }

        // Terapkan fungsi ke semua input mata uang
        autoFormatCurrency(
            document.getElementById('acquisition_value-display'),
            document.getElementById('acquisition_value-value')
        );
    });
</script>
@endpush