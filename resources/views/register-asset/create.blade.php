@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('register-asset.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
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
                        <a href="{{ route('register-asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Register Asset</a>
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

    @if (session('success'))
        <div id="alert-3" class="auto-dismiss-alert flex items-center p-4 mb-4 text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-3" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div id="alert-2" class="auto-dismiss-alert flex items-center p-4 mb-4 text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('error') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-2" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div id="alert-1" class="auto-dismiss-alert flex items-center p-4 mb-4 text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
            <svg class="shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div class="ms-3 text-sm font-medium">
                {{ session('info') }}
            </div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700" data-dismiss-target="#alert-1" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <form class="max-w mx-auto" action="{{ route('register-asset.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-5 flex content-center">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Nomor Formulir <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <p class="w-full px-2">{{ $form_no }}</p>
                    <input type="hidden" name="form_no" value="{{ $form_no }}" class="px-1 w-64 text-sm text-gray-900 appearance-none dark:text-white" readonly/>
                    @error('form_no')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5 flex content-center">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Select Department <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <select name="department_id" id="department-select" class="px-1 mx-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
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

                <div class="mb-5 flex content-center">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Select Location <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <select name="location_id" class="px-1 mx-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
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

                <div class="mb-5 flex content-center">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Asset Type <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <div class="w-full flex ml-2">
                        <div class="flex items-center pr-4">
                            <input id="fixed-asset" checked name="asset_type" type="radio" value="FA" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('asset_type', 'FA') == 'FA' ? 'checked' : '' }}>
                            <label for="fixed-asset" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Fixed Asset <span class="italic">(FA)</span></label>
                        </div>
                        <div class="flex items-center">
                            <input id="low-value-asset" name="asset_type" type="radio" value="LVA" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('asset_type') == 'LVA' ? 'checked' : '' }}>
                            <label for="low-value-asset" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Low Value Asset <span class="italic">(LVA)</span></label>
                        </div>
                    </div>
                    @error('asset_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Asset List -->
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset List <span class="text-red-900">*</span></label>
                    <div class="border-2 border-black rounded-lg p-4">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-2 py-3">No</th>
                                    <th scope="col" class="px-2 py-3">PO No.</th>
                                    <th scope="col" class="px-2 py-3">Invoice No.</th>
                                    <th scope="col" class="px-2 py-3 commission-date-th overflow-hidden transition-all duration-500 ease-in-out">Commission Date</th>
                                    <th scope="col" class="px-2 py-3">Specification</th>
                                    <th scope="col" class="px-2 py-3">Asset Class</th>
                                    <th scope="col" class="px-2 py-3">Asset Sub Class</th>
                                    <th scope="col" class="px-2 py-3">Asset Name</th>
                                    <th scope="col" class="px-2 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody id="asset-list-body">
                                @php $initialAssets = old('assets', [[]]); @endphp
                                @foreach($initialAssets as $index => $assetData)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 asset-row">
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
                                        <td class="px-2 py-4 commission-date-td overflow-hidden transition-all duration-500 ease-in-out">
                                            <input type="date" name="assets[{{ $index }}][commission_date]" value="{{ old("assets.$index.commission_date", $assetData['commission_date'] ?? '') }}" class="commission-date-input block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
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
                                            <select name="assets[{{ $index }}][asset_class_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-class-select">
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
                                            <select name="assets[{{ $index }}][asset_sub_class_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-sub-class-select">
                                                <option value="">Choose Asset Sub Class</option>
                                            </select>
                                            @error("assets.$index.asset_sub_class_id")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="px-2 py-4">
                                            <select name="assets[{{ $index }}][asset_name_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-name-select">
                                                <option value="">Choose Asset Name</option>
                                            </select>
                                            @error("assets.$index.asset_name_id")
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
                        <input id="insured-yes" name="insured" type="radio" value="Y" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('insured', 'Y') == 'Y' ? 'checked' : '' }}>
                        <label for="insured-yes" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ya <span class="italic">(Yes)</span></label>
                    </div>
                    <div class="flex items-center">
                        <input id="insured-no" name="insured" type="radio" value="N" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('insured') == 'N' ? 'checked' : '' }}>
                        <label for="insured-no" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tidak <span class="italic">(No)</span></label>
                    </div>
                    @error('insured')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div id="polish-no-wrapper" class="mb-5 flex content-center overflow-hidden transition-all duration-500 ease-in-out">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Polish No. <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <input type="text" id="polish-no-input" name="polish_no" value="{{ old('polish_no') }}" class="block py-1 px-0 ml-2 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"/>
                    @error('polish_no')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="attachments">Upload Lampiran</label>
                    <input name="attachments[]" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="attachments" type="file" multiple>
                    <p class="mt-1 text-sm text-gray-500">Anda bisa melampirkan lebih dari satu file, satu file maksimal 5MB.</p>
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approval List <span class="text-red-900">*</span></label>
                    <div class="border-2 border-black rounded-lg p-4">
                        
                        <div class="flex flex-row mb-2">
                            <label class="w-auto mr-2 text-sm font-medium text-gray-900 dark:text-white">Sequence <span class="text-red-900">*</span> : </label>
                            <div class="flex items-center pr-4">
                                <input id="sequence-yes" name="sequence" type="radio" value="Y" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('sequence', 'Y') == 'Y' ? 'checked' : '' }}>
                                <label for="sequence-yes" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ya <span class="italic">(Yes)</span></label>
                            </div>
                            <div class="flex items-center">
                                <input id="sequence-no" checked name="sequence" type="radio" value="N" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('sequence') == 'N' ? 'checked' : '' }}>
                                <label for="sequence-no" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tidak <span class="italic">(No)</span></label>
                            </div>
                            @error('sequence')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="mb-2">
                        
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr class="text-center">
                                    <th scope="col" colspan="2" class="px-2 py-3">Persetujuan Approval</th>
                                    <th scope="col" class="px-2 py-3">Name</th>
                                    <th scope="col" class="px-2 py-3">Signature</th>
                                    <th scope="col" class="px-2 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody id="approval-list-body">
                                
                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[0][approval_action]" value="Submitted by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[0][approval_action]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>   
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[0][role]" value="Asset Management" class="approval-role border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[0][role]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                    <td class="px-2 py-4">
                                        <select name="approvals[0][pic_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <option value="">Pilih Nama</option>
                                            @foreach($personsInCharge as $pic)
                                                {{-- Tambahkan atribut data-role di sini --}}
                                                <option value="{{ $pic->id }}" 
                                                        data-role="{{ $pic->position }}"
                                                        {{ old("approvals.$index.pic_id", $approvalData->pic_id ?? '') == $pic->id ? 'selected' : '' }}>
                                                    {{ $pic->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("approvals[0][pic_id]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="approvals[0][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[0][status]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="approvals[0][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[0][approval_date]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[1][approval_action]" value="Checked by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[1][approval_action]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[1][role]" value="User Manager" class="approval-role border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[1][role]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                    <td class="px-2 py-4">
                                        <select name="approvals[1][pic_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <option value="">Pilih Nama</option>
                                            @foreach($personsInCharge as $pic)
                                                {{-- Tambahkan atribut data-role di sini --}}
                                                <option value="{{ $pic->id }}" 
                                                        data-role="{{ $pic->position }}"
                                                        {{ old("approvals.$index.pic_id", $approvalData->pic_id ?? '') == $pic->id ? 'selected' : '' }}>
                                                    {{ $pic->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("approvals[1][pic_id]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="approvals[1][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[1][status]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="approvals[1][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[1][approval_date]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[2][approval_action]" value="Approved by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[2][approval_action]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[2][role]" value="CFO" class="approval-role border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[2][role]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                    <td class="px-2 py-4">
                                        <select name="approvals[2][pic_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <option value="">Pilih Nama</option>
                                            @foreach($personsInCharge as $pic)
                                                {{-- Tambahkan atribut data-role di sini --}}
                                                <option value="{{ $pic->id }}" 
                                                        data-role="{{ $pic->position }}"
                                                        {{ old("approvals.$index.pic_id", $approvalData->pic_id ?? '') == $pic->id ? 'selected' : '' }}>
                                                    {{ $pic->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("approvals[2][pic_id]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="approvals[2][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[2][status]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="approvals[2][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[2][approval_date]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>

                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[3][approval_action]" value="Approved by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[3][approval_action]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="text" name="approvals[3][role]" value="Director" class="approval-role border border-white focus:ring-0 focus:border-white-600" readonly/>
                                        @error("approvals[3][role]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </th>
                                    <td class="px-2 py-4">
                                        <select name="approvals[3][pic_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                            <option value="">Pilih Nama</option>
                                            @foreach($personsInCharge as $pic)
                                                {{-- Tambahkan atribut data-role di sini --}}
                                                <option value="{{ $pic->id }}" 
                                                        data-role="{{ $pic->position }}"
                                                        {{ old("approvals.$index.pic_id", $approvalData->pic_id ?? '') == $pic->id ? 'selected' : '' }}>
                                                    {{ $pic->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("approvals[3][pic_id]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="text" name="approvals[3][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[3][status]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td class="px-2 py-4">
                                        <input type="date" name="approvals[3][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" readonly />
                                        @error("approvals[3][approval_date]")
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="company_id" value="{{ Auth::user()->last_active_company_id }}" required />

                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Create</button>
                <a href="{{ route('register-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
            </form>

            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <span class="font-medium">Validasi Gagal!</span> Mohon periksa error di bawah ini:
                    <ul class="mt-1.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        //Insured
        const insuredRadios = document.querySelectorAll('input[name="insured"]');
        const polishNoWrapper = document.getElementById('polish-no-wrapper');
        const polishNoInput = document.getElementById('polish-no-input');

        //commission Date
        const assetTypeRadios = document.querySelectorAll('input[name="asset_type"]');

        //Asset List
        const assetListBody = document.getElementById('asset-list-body');
        const addAssetRowBtn = document.getElementById('add-asset-row');
        const assetClassesData = @json($assetclasses);
        const oldData = @json(old('assets', []));

        //Hide Insured
        function togglePolishNoVisibility() {
            // Cek radio button mana yang sedang dipilih
            const selectedValue = document.querySelector('input[name="insured"]:checked').value;

            if (selectedValue === 'Y') {
                // Tampilkan div dengan transisi
                polishNoWrapper.style.maxHeight = polishNoWrapper.scrollHeight + 'px';
                polishNoWrapper.style.opacity = '1';
                polishNoWrapper.classList.add('mb-5');
                polishNoInput.required = true;
            } else {
                // Sembunyikan div dengan transisi
                polishNoWrapper.style.maxHeight = '0px';
                polishNoWrapper.style.opacity = '0';
                polishNoWrapper.classList.remove('mb-5');
                polishNoInput.required = false;
                polishNoInput.value = '';
            }
        }

        insuredRadios.forEach(radio => {
            radio.addEventListener('change', togglePolishNoVisibility);
        });

        togglePolishNoVisibility();

        //Hide commission Date
        function toggleCommissionDateVisibility() {
            const selectedValue = document.querySelector('input[name="asset_type"]:checked').value;
            const headers = document.querySelectorAll('.commission-date-th');
            const cells = document.querySelectorAll('.commission-date-td');
            const inputs = document.querySelectorAll('.commission-date-input');
            
            if (selectedValue === 'FA') {
                // Tampilkan semua header dan sel kolom
                headers.forEach(el => el.classList.remove('hidden'));
                cells.forEach(el => el.classList.remove('hidden'));
                inputs.forEach(el => el.required = true); // Jadikan semua input required
            } else {
                // Sembunyikan semua header dan sel kolom
                headers.forEach(el => el.classList.add('hidden'));
                cells.forEach(el => el.classList.add('hidden'));
                inputs.forEach(el => {
                    el.required = false; // Hapus required
                    el.value = ''; // Kosongkan nilainya
                });
            }
        }

        assetTypeRadios.forEach(radio => radio.addEventListener('change', toggleCommissionDateVisibility));

        function createRowTemplate(index) {
            let classOptions = assetClassesData.map(cls => `<option value="${cls.id}">${cls.name}</option>`).join('');
            return `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 asset-row">
                    <td class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white asset-row-number text-center">${index + 1}</td>
                    <td class="px-2 py-4">
                        <input type="text" name="assets[${index}][po_no]" value="" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="PO No." />
                        @error("assets.$index.po_no")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="px-2 py-4">
                        <input type="text" name="assets[${index}][invoice_no]" value="" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Invoice No." />
                        @error("assets.$index.invoice_no")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="px-2 py-4 commission-date-td overflow-hidden transition-all duration-500 ease-in-out">
                        <input type="date" name="assets[${index}][commission_date]" value="" class="commission-date-input block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                        @error("assets.$index.commission_date")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="px-2 py-4">
                        <input type="text" name="assets[${index}][specification]" value="" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Specification" />
                        @error("assets.$index.specification")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="px-2 py-4">
                        <select name="assets[${index}][asset_class_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-class-select">
                            <option value="">Choose Asset Class</option>
                            ${classOptions}
                        </select>
                        @error("assets.$index.asset_class_id")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="px-2 py-4">
                        <select name="assets[${index}][asset_sub_class_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-sub-class-select">
                            <option value="">Choose Asset Sub Class</option>
                        </select>
                        @error("assets.$index.asset_sub_class_id")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="px-2 py-4">
                        <select name="assets[${index}][asset_name_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer asset-name-select">
                            <option value="">Choose Asset Name</option>
                        </select>
                        @error("assets.$index.asset_name_id")
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </td>
                    <td class="px-2 py-4">
                        <button type="button" class="text-red-600 hover:text-red-900 delete-row-btn">Delete</button>
                    </td>
                </tr>
            `;
        }

        // Function to update row numbers in the first column
        function updateRowNumbers() {
            assetListBody.querySelectorAll('.asset-row').forEach((row, index) => {
                const rowNumberCell = row.querySelector('.asset-row-number');
                if (rowNumberCell) rowNumberCell.textContent = index + 1;
            });
        }

        // --- Function to Get Asset Sub Classes based on Asset Class ---
        async function populateSubClasses(classSelect, subClassSelect, nameSelect, selectedSubClassId = null) {
            const classId = classSelect.value;
            subClassSelect.innerHTML = '<option value="">Loading...</option>';
            nameSelect.innerHTML = '<option value="">Choose Asset Name</option>';
            if (!classId) {
                subClassSelect.innerHTML = '<option value="">Choose Sub Class</option>';
                return;
            }
            try {
                const response = await fetch(`/api/asset-sub-classes-by-class/${classId}`);
                if (!response.ok) throw new Error('Network error');
                const data = await response.json();
                subClassSelect.innerHTML = '<option value="">Choose Sub Class</option>';
                data.forEach(sub => {
                    const option = new Option(sub.name, sub.id);
                    if (selectedSubClassId && selectedSubClassId == sub.id) option.selected = true;
                    subClassSelect.add(option);
                });
                if (selectedSubClassId) subClassSelect.dispatchEvent(new Event('change'));
            } catch (error) {
                console.error('Error fetching sub classes:', error);
                subClassSelect.innerHTML = '<option value="">Error</option>';
            }
        }

        async function populateAssetNames(subClassSelect, nameSelect, selectedNameId = null) {
            const subClassId = subClassSelect.value;
            nameSelect.innerHTML = '<option value="">Loading...</option>';
            if (!subClassId) {
                nameSelect.innerHTML = '<option value="">Choose Asset Name</option>';
                return;
            }
            try {
                const response = await fetch(`/api/asset-names-by-sub-class/${subClassId}`);
                if (!response.ok) throw new Error('Network error');
                const data = await response.json();
                nameSelect.innerHTML = '<option value="">Choose Asset Name</option>';
                data.forEach(name => {
                    const option = new Option(name.name, name.id);
                    if (selectedNameId && selectedNameId == name.id) option.selected = true;
                    nameSelect.add(option);
                });
            } catch (error) {
                console.error('Error fetching asset names:', error);
                nameSelect.innerHTML = '<option value="">Error</option>';
            }
        }

        function setupRowListeners(row) {
            const classSelect = row.querySelector('.asset-class-select');
            const subClassSelect = row.querySelector('.asset-sub-class-select');
            const nameSelect = row.querySelector('.asset-name-select');
            
            classSelect.addEventListener('change', () => populateSubClasses(classSelect, subClassSelect, nameSelect));
            subClassSelect.addEventListener('change', () => populateAssetNames(subClassSelect, nameSelect));
        }

        // --- LOGIKA UTAMA ---
        assetListBody.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-row-btn')) {
                if (assetListBody.querySelectorAll('.asset-row').length > 1) {
                    event.target.closest('.asset-row').remove();
                    updateRowNumbers();
                } else {
                    alert('At least one asset row must remain.');
                }
            }
        });

        // Event listener for "Add Asset" button
        addAssetRowBtn.addEventListener('click', () => {
            const newIndex = assetListBody.querySelectorAll('.asset-row').length;
            const newRowHtml = createRowTemplate(newIndex);
            assetListBody.insertAdjacentHTML('beforeend', newRowHtml);
            const newRow = assetListBody.lastElementChild;
            setupRowListeners(newRow);
            updateRowNumbers();
            toggleCommissionDateVisibility(); 
        });

        function initializeRows() {
            assetListBody.querySelectorAll('.asset-row').forEach((row, index) => {
                setupRowListeners(row);
                
                if (oldData && oldData[index]) {
                    const classSelect = row.querySelector('.asset-class-select');
                    const subClassSelect = row.querySelector('.asset-sub-class-select');
                    const nameSelect = row.querySelector('.asset-name-select');
                    
                    const oldSubClassId = oldData[index].asset_sub_class_id;
                    const oldNameId = oldData[index].asset_name_id;

                    if (classSelect.value) {
                        populateSubClasses(classSelect, subClassSelect, nameSelect, oldSubClassId)
                            .then(() => {
                                if (subClassSelect.value) {
                                populateAssetNames(subClassSelect, nameSelect, oldNameId);
                                }
                            });
                    }
                }
            });
        }

        toggleCommissionDateVisibility();
        initializeRows();
        updateRowNumbers();

        function filterUsersByRole(row) {
            const roleInput = row.querySelector('.approval-role');
            const userSelect = row.querySelector('.approval-user-select');
            
            if (!roleInput || !userSelect) return;

            const selectedRole = roleInput.value;

            // Loop melalui setiap <option> di dalam dropdown user
            for (const option of userSelect.options) {
                // Lewati opsi pertama ("Pilih Nama")
                if (option.value === '') continue;

                // Tampilkan jika role-nya cocok, sembunyikan jika tidak
                if (option.dataset.role === selectedRole) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                    // Jika opsi yang disembunyikan sedang terpilih, reset dropdown
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
</script>
@endpush