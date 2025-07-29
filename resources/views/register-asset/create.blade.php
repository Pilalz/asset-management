@extends('layouts.main')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('register-asset.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Asset
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('register-asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Asset Sub Class</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Create</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg m-5 bg-white dark:bg-gray-900">
        <form class="max-w mx-auto" action="{{ route('register-asset.store') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a Department <span class="text-red-900">*</span></label>
                {{-- Nama atribut diubah menjadi department_id --}}
                <select name="department_id" id="department-select" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="">Choose a Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id') {{-- Error message untuk department_id --}}
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a Location <span class="text-red-900">*</span></label>
                {{-- Nama atribut diubah menjadi location_id --}}
                <select name="location_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option selected value="">Choose a Location</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
                @error('location_id') {{-- Error message untuk location_id --}}
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset List <span class="text-red-900">*</span></label>
                <div class="border-2 border-black rounded-lg p-4">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-2 py-3">No</th>
                                <th scope="col" class="px-2 py-3">PO No.</th>
                                <th scope="col" class="px-2 py-3">Invoice No.</th>
                                <th scope="col" class="px-2 py-3">Commission Date</th>
                                <th scope="col" class="px-2 py-3">Specification</th>
                                <th scope="col" class="px-2 py-3">Asset Details</th>
                                <th scope="col" class="px-2 py-3">Asset Class</th>
                                <th scope="col" class="px-2 py-3">Asset Sub Class</th>
                                <th scope="col" class="px-2 py-3">Cost Code</th>
                                <th scope="col" class="px-2 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="asset-list-body">
                            {{-- Initial row, or populate with old data if validation fails --}}
                            @php
                                $initialAssets = old('assets', [[]]); // Start with one empty row or old data
                                if (empty($initialAssets[0])) { // Ensure at least one empty object if old is empty array
                                    $initialAssets = [[]];
                                }
                            @endphp

                            @foreach($initialAssets as $index => $assetData)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white asset-row-number text-center">{{ $loop->iteration }}</td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][po_no]" value="{{ old("assets.$index.po_no", $assetData['po_no'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="PO No." />
                                        @error("assets.$index.po_no")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][invoice_no]" value="{{ old("assets.$index.invoice_no", $assetData['invoice_no'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Invoice No." />
                                        @error("assets.$index.invoice_no")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Specification" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <select name="assets[{{ $index }}][asset_class_id]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 asset-class-select" required>
                                            <option value="">Choose Asset Class</option>
                                            @foreach($assetclasses as $class)
                                                <option value="{{ $class->id }}" {{ old("assets.$index.asset_class_id", $assetData['asset_class_id'] ?? '') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("assets.$index.asset_class_id")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <select name="assets[{{ $index }}][asset_sub_class_id]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 asset-sub-class-select" required>
                                            <option value="">Choose Asset Sub Class</option>
                                            {{-- Options will be populated by JavaScript --}}
                                        </select>
                                        @error("assets.$index.asset_sub_class_id")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        {{-- Disabled input untuk tampilan --}}
                                        <input type="text" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 cost-code-display-input" value="" disabled readonly>
                                        {{-- Hidden input untuk mengirimkan ID ke backend --}}
                                        <input type="hidden" name="assets[{{ $index }}][cost_code_id]" value="{{ old("assets.$index.cost_code_id", $assetData['cost_code_id'] ?? '') }}" class="cost-code-hidden-input">
                                        @error("assets.$index.cost_code_id")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <button type="button" class="text-red-600 hover:text-red-900 delete-row-btn">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" id="add-asset-row" class="mt-4 text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Add Asset</button>
                </div>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Insured <span class="text-red-900">*</span></label>
                <div class="flex items-center mb-4">
                    <input id="insured-yes" name="insured" type="radio" value="Y" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('insured', 'Y') == 'Y' ? 'checked' : '' }}>
                    <label for="insured-yes" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ya <span class="italic">(Yes)</span></label>
                </div>
                <div class="flex items-center">
                    <input id="insured-no" name="insured" type="radio" value="N" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('insured') == 'N' ? 'checked' : '' }}>
                    <label for="insured-no" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tidak <span class="italic">(No)</span></label>
                </div>
                @error('insured')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Create</button>
            <a href="{{ route('register-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assetListBody = document.getElementById('asset-list-body');
        const addAssetRowBtn = document.getElementById('add-asset-row');
        const departmentSelect = document.getElementById('department-select');

        // Inisialisasi rowIndex berdasarkan jumlah baris yang ada (termasuk old input)
        let rowIndex = assetListBody.children.length;

        // Fungsi untuk memperbarui nomor baris
        function updateRowNumbers() {
            const rows = assetListBody.querySelectorAll('tr');
            rows.forEach((row, index) => {
                const rowNumberCell = row.querySelector('.asset-row-number');
                if (rowNumberCell) {
                    rowNumberCell.textContent = index + 1;
                }
            });
        }

        // --- Fungsi untuk Mendapatkan Asset Sub Class berdasarkan Asset Class ---
        function populateAssetSubClasses(assetClassSelectElement, assetSubClassSelectElement, selectedSubClassId = null) {
            const assetClassId = assetClassSelectElement.value;
            assetSubClassSelectElement.innerHTML = '<option value="">Loading...</option>'; // Tampilkan loading

            if (!assetClassId) {
                assetSubClassSelectElement.innerHTML = '<option value="">Choose Asset Sub Class</option>';
                return;
            }

            fetch(`/api/asset-sub-classes-by-class/${assetClassId}`) // Sesuaikan URL endpoint Anda
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    assetSubClassSelectElement.innerHTML = '<option value="">Choose Asset Sub Class</option>';
                    data.forEach(subClass => {
                        const option = document.createElement('option');
                        option.value = subClass.id;
                        option.textContent = subClass.name;
                        if (selectedSubClassId && selectedSubClassId == subClass.id) {
                            option.selected = true;
                        }
                        assetSubClassSelectElement.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching asset sub classes:', error);
                    assetSubClassSelectElement.innerHTML = '<option value="">Error loading sub classes</option>';
                });
        }

        // --- Fungsi untuk Mendapatkan Cost Code berdasarkan Department ---
        // Fungsi ini sekarang menerima elemen input display dan hidden
        function populateCostCodes(departmentId, costCodeDisplayInput, costCodeHiddenInput, selectedCostCodeId = null) {
            costCodeDisplayInput.value = 'Loading...'; // Tampilkan loading di input teks
            costCodeHiddenInput.value = ''; // Kosongkan hidden input saat loading

            if (!departmentId) {
                costCodeDisplayInput.value = 'Choose Department first';
                costCodeHiddenInput.value = '';
                return;
            }

            fetch(`/api/cost-codes-by-department/${departmentId}`) // Sesuaikan URL endpoint Anda
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Jika ada data, cari yang cocok atau ambil yang pertama/default
                    if (data.length > 0) {
                        let selectedCostCode = data[0]; // Default ke yang pertama jika tidak ada selectedId
                        if (selectedCostCodeId) {
                            const found = data.find(cc => cc.id == selectedCostCodeId);
                            if (found) {
                                selectedCostCode = found;
                            }
                        }
                        costCodeDisplayInput.value = `${selectedCostCode.code} - ${selectedCostCode.description}`;
                        costCodeHiddenInput.value = selectedCostCode.id;
                    } else {
                        costCodeDisplayInput.value = 'No Cost Code for this Department';
                        costCodeHiddenInput.value = '';
                    }
                })
                .catch(error => {
                    console.error('Error fetching cost codes:', error);
                    costCodeDisplayInput.value = 'Error loading Cost Codes';
                    costCodeHiddenInput.value = '';
                });
        }

        // Fungsi untuk menambahkan baris baru
        addAssetRowBtn.addEventListener('click', function() {
            const newRowHtml = `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white asset-row-number">${rowIndex + 1}</td>
                    <td class="px-2 py-4">
                        <input type="text" name="assets[${rowIndex}][po_no]" value="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="PO No." />
                    </td>
                    <td class="px-2 py-4">
                        <input type="text" name="assets[${rowIndex}][invoice_no]" value="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Invoice No." />
                    </td>
                    <td class="px-2 py-4">
                        <input type="date" name="assets[${rowIndex}][commission_date]" value="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                    </td>
                    <td class="px-2 py-4">
                        <input type="text" name="assets[${rowIndex}][specification]" value="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Specification" />
                    </td>
                    <td class="px-2 py-4">
                        <input type="text" name="assets[${rowIndex}][asset_details]" value="" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Asset Details" />
                    </td>
                    <td class="px-2 py-4">
                        <select name="assets[${rowIndex}][asset_class_id]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 asset-class-select" required>
                            <option value="">Choose Asset Class</option>
                            @foreach($assetclasses as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-2 py-4">
                        <select name="assets[${rowIndex}][asset_sub_class_id]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 asset-sub-class-select" required>
                            <option value="">Choose Asset Sub Class</option>
                        </select>
                    </td>
                    <td class="px-2 py-4">
                        {{-- Disabled input untuk tampilan --}}
                        <input type="text" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-gray-400 cost-code-display-input" value="" disabled readonly>
                        {{-- Hidden input untuk mengirimkan ID ke backend --}}
                        <input type="hidden" name="assets[${rowIndex}][cost_code_id]" value="" class="cost-code-hidden-input">
                    </td>
                    <td class="px-2 py-4">
                        <button type="button" class="text-red-600 hover:text-red-900 delete-row-btn">Delete</button>
                    </td>
                </tr>
            `;

            assetListBody.insertAdjacentHTML('beforeend', newRowHtml);
            const newRowElement = assetListBody.lastElementChild; // Dapatkan elemen baris baru
            rowIndex++;
            updateRowNumbers();
            attachEventListenersToNewRow(newRowElement); // Lampirkan event listener ke baris baru

            // Panggil populateCostCodes untuk baris baru jika departemen sudah dipilih
            const currentDepartmentId = departmentSelect.value;
            if (currentDepartmentId) {
                const newCostCodeDisplayInput = newRowElement.querySelector('.cost-code-display-input');
                const newCostCodeHiddenInput = newRowElement.querySelector('.cost-code-hidden-input');
                populateCostCodes(currentDepartmentId, newCostCodeDisplayInput, newCostCodeHiddenInput);
            }
        });

        // Event listener untuk tombol delete (delegasi event)
        assetListBody.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-row-btn')) {
                if (assetListBody.children.length > 1) {
                    event.target.closest('tr').remove();
                    rowIndex--; // Kurangi rowIndex saat baris dihapus
                    updateRowNumbers();
                } else {
                    alert('Minimal harus ada satu baris aset.');
                }
            }
        });

        // Fungsi untuk melampirkan event listener ke elemen-elemen di baris baru
        function attachEventListenersToNewRow(rowElement) {
            const assetClassSelect = rowElement.querySelector('.asset-class-select');
            const assetSubClassSelect = rowElement.querySelector('.asset-sub-class-select');
            const costCodeDisplayInput = rowElement.querySelector('.cost-code-display-input'); // Dapatkan input display
            const costCodeHiddenInput = rowElement.querySelector('.cost-code-hidden-input'); // Dapatkan input hidden

            if (assetClassSelect && assetSubClassSelect) {
                assetClassSelect.addEventListener('change', function() {
                    populateAssetSubClasses(assetClassSelect, assetSubClassSelect);
                });
            }

            // Tidak perlu event listener langsung di costCodeSelect karena sekarang diisi otomatis
            // Event listener untuk departmentSelect akan menangani pembaruan cost codes di SEMUA baris
        }

        // Event listener untuk perubahan Department (memperbarui semua Cost Code di tabel)
        departmentSelect.addEventListener('change', function() {
            const currentDepartmentId = departmentSelect.value;
            assetListBody.querySelectorAll('tr').forEach(rowElement => {
                const costCodeDisplayInput = rowElement.querySelector('.cost-code-display-input');
                const costCodeHiddenInput = rowElement.querySelector('.cost-code-hidden-input');
                if (costCodeDisplayInput && costCodeHiddenInput) {
                    populateCostCodes(currentDepartmentId, costCodeDisplayInput, costCodeHiddenInput);
                }
            });
        });


        // Inisialisasi event listener dan populate dropdown untuk baris yang sudah ada saat halaman dimuat
        assetListBody.querySelectorAll('tr').forEach(row => {
            attachEventListenersToNewRow(row);

            const initialAssetClassSelect = row.querySelector('.asset-class-select');
            const initialAssetSubClassSelect = row.querySelector('.asset-sub-class-select');
            const initialCostCodeDisplayInput = row.querySelector('.cost-code-display-input'); // Dapatkan input display
            const initialCostCodeHiddenInput = row.querySelector('.cost-code-hidden-input'); // Dapatkan input hidden

            const initialIndex = Array.from(assetListBody.children).indexOf(row);
            const oldAssetData = @json(old('assets', []));

            // Handle old input for Asset Class and Sub Class
            if (initialAssetClassSelect && initialAssetSubClassSelect && oldAssetData[initialIndex]) {
                const oldClassId = oldAssetData[initialIndex].asset_class_id;
                const oldSubClassId = oldAssetData[initialIndex].asset_sub_class_id;

                if (oldClassId) {
                    initialAssetClassSelect.value = oldClassId;
                    populateAssetSubClasses(initialAssetClassSelect, initialAssetSubClassSelect, oldSubClassId);
                }
            }

            // Handle old input for Cost Code based on initial department selection
            if (initialCostCodeDisplayInput && initialCostCodeHiddenInput && oldAssetData[initialIndex]) {
                const oldCostCodeId = oldAssetData[initialIndex].cost_code_id;
                const currentDepartmentId = departmentSelect.value;

                if (currentDepartmentId) {
                    populateCostCodes(currentDepartmentId, initialCostCodeDisplayInput, initialCostCodeHiddenInput, oldCostCodeId);
                } else {
                    // Jika tidak ada department yang dipilih, pastikan input cost code juga kosong
                    initialCostCodeDisplayInput.value = 'Choose Department first';
                    initialCostCodeHiddenInput.value = '';
                }
            } else if (initialCostCodeDisplayInput && initialCostCodeHiddenInput) {
                 // Jika tidak ada old data sama sekali, dan belum ada department terpilih
                 initialCostCodeDisplayInput.value = 'Choose Department first';
                 initialCostCodeHiddenInput.value = '';
            }
        });

        // Panggil populateCostCodes untuk semua baris saat halaman dimuat jika departemen sudah dipilih
        // Ini memastikan cost codes terisi bahkan jika tidak ada old input untuk baris tersebut
        const initialDepartmentId = departmentSelect.value;
        if (initialDepartmentId) {
            assetListBody.querySelectorAll('tr').forEach(rowElement => {
                const costCodeDisplayInput = rowElement.querySelector('.cost-code-display-input');
                const costCodeHiddenInput = rowElement.querySelector('.cost-code-hidden-input');
                if (costCodeDisplayInput && costCodeHiddenInput) {
                    const initialIndex = Array.from(assetListBody.children).indexOf(rowElement);
                    const oldAssetData = @json(old('assets', []));
                    const oldCostCodeId = oldAssetData[initialIndex] ? (oldAssetData[initialIndex].cost_code_id ?? null) : null;
                    populateCostCodes(initialDepartmentId, costCodeDisplayInput, costCodeHiddenInput, oldCostCodeId);
                }
            });
        } else {
            // Jika tidak ada department yang dipilih saat load, pastikan semua cost code input kosong
            assetListBody.querySelectorAll('tr').forEach(rowElement => {
                const costCodeDisplayInput = rowElement.querySelector('.cost-code-display-input');
                const costCodeHiddenInput = rowElement.querySelector('.cost-code-hidden-input');
                if (costCodeDisplayInput && costCodeHiddenInput) {
                    costCodeDisplayInput.value = 'Choose Department first';
                    costCodeHiddenInput.value = '';
                }
            });
        }


        updateRowNumbers(); // Perbarui nomor baris awal
    });
</script>
@endpush
