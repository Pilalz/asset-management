@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('transfer-asset.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Action
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('transfer-asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Transfer Asset</a>
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
        <form class="max-w mx-auto" action="{{ route('transfer-asset.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pengajuan <span class="text-red-900">*</span></label>
                <input type="text" id="disabled_standard" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " disabled />
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">No Formulir <span class="text-red-900">*</span></label>
                <input type="text" id="disabled_standard" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " disabled />
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a Department <span class="text-red-900">*</span></label>
                <select name="department_id" id="department-select" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <option selected value="">Choose a Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Data <span class="text-red-900">*</span></label>
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
                            </tr>
                        </thead>
                        <tbody id="asset-list-body">
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
                                        <input type="text" name="assets[{{ $index }}][po_no]" value="{{ old("assets.$index.po_no", $assetData['po_no'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="PO No." />
                                        @error("assets.$index.po_no")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][invoice_no]" value="{{ old("assets.$index.invoice_no", $assetData['invoice_no'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Invoice No." />
                                        @error("assets.$index.invoice_no")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Specification" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <select name="assets[{{ $index }}][asset_class_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-class-select" required>
                                            <option value="">Choose Asset Class</option>
                                            
                                        </select>
                                        @error("assets.$index.asset_class_id")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <select name="assets[{{ $index }}][asset_sub_class_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-sub-class-select" required>
                                            <option value="">Choose Asset Sub Class</option>
                                            {{-- Options will be populated by JavaScript --}}
                                        </select>
                                        @error("assets.$index.asset_sub_class_id")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        {{-- Disabled input for display --}}
                                        <input type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer cost-code-display-input" value="" disabled readonly>
                                        {{-- Hidden input to send Department ID to backend --}}
                                        <input type="hidden" name="assets[{{ $index }}][department_id]" value="{{ old("assets.$index.department_id", $assetData['department_id'] ?? '') }}" class="cost-code-hidden-input">
                                        @error("assets.$index.department_id") {{-- Error message for department_id in assets array --}}
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a Location <span class="text-red-900">*</span></label>
                <select name="location_id" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <option selected value="">Choose a Location</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
                @error('location_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alasan <span class="text-red-900">*</span></label>
                <textarea type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"></textarea>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approval List <span class="text-red-900">*</span></label>
                <div class="border-2 border-black rounded-lg p-4">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr class="text-center">
                                <th scope="col" colspan="2" class="px-2 py-3">Persetujuan Approval</th>
                                <th scope="col" class="px-2 py-3">Name</th>
                                <th scope="col" class="px-2 py-3">Signature</th>
                                <th scope="col" class="px-2 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody id="asset-list-body">
                            @foreach($initialAssets as $index => $assetData)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Submitted by
                                    </th>
                                    
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        User
                                    </th>
                                    <!-- <td class="px-2 py-4">
                                        <select type="text" name="assets[{{ $index }}][po_no]" value="{{ old("assets.$index.po_no", $assetData['po_no'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Diketahui">
                                            <option selected>Choose aproval type</option>
                                            <option value="Submitted by">Submitted by</option>
                                            <option value="Known by">Known by</option>
                                            <option value="Checked by">Checked by</option>
                                            <option value="Approved by">Approved by</option>
                                            <option value="Accepted by">Accepted by</option>
                                        </select>
                                        @error("assets.$index.po_no")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td> -->
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Known by
                                    </th>
                                    
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        User Manager
                                    </th>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Approved by
                                    </th>
                                    
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Site Director
                                    </th>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Checked by
                                    </th>
                                    
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Asset Management
                                    </th>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Approved by
                                    </th>
                                    
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        CFO
                                    </th>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Approved by
                                    </th>
                                    
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Director
                                    </th>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        Accepted by
                                    </th>
                                    
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        User
                                    </th>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                        @error("assets.$index.commission_date")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="assets[{{ $index }}][specification]" value="{{ old("assets.$index.specification", $assetData['specification'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                        @error("assets.$index.specification")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="assets[{{ $index }}][asset_details]" value="{{ old("assets.$index.asset_details", $assetData['asset_details'] ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                        @error("assets.$index.asset_details")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Create</button>
            <a href="{{ route('transfer-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
        </form>
    </div>
@endsection