@extends('layouts.main')

@section('content')

    @push('styles')
        <style>
            .ts-control {
                border: 0 solid #F9FAFB !important;
                font-size: 15px !important;
            }

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

        <form class="max-w mx-auto" action="{{ route('transfer-asset.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="default-tab-content">
                <div class="hidden" id="formulir" role="tabpanel" aria-labelledby="formulir-tab">
                    <div class="relative overflow-x-auto py-5 px-6 bg-white dark:bg-gray-800">

                        <div class="grid grid-cols-1 gap-y-5 mb-5 dark:text-white">
                            <div class="md:col-span-1">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2">
                                    Basic Information
                                </h2>
                            </div>

                            <div class="flex content-center">
                                <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pengajuan <span class="text-red-900 dark:text-red-400">*</span></label>
                                <span> : </span>
                                <p class="w-full px-2">{{ now()->format('d F Y') }}</p>
                                <input type="hidden" name="submit_date" value="{{ now()->format('Y-m-d') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                @error('submit_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="flex content-center">
                                <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Nomor Formulir <span class="text-red-900 dark:text-red-400">*</span></label>
                                <span> : </span>
                                <p class="w-full px-2">{{ $form_no }}</p>
                                <input type="hidden" name="form_no" value="{{ $form_no }}" class="w-full px-1 text-sm text-gray-900 appearance-none dark:text-white" readonly/>
                                @error('form_no')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="flex content-center">
                                <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Select Department <span class="text-red-900 dark:text-red-400">*</span></label>
                                <span> : </span>
                                <select name="department_id" id="department-select" class="px-1 mx-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
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

                            <div class="flex content-center">
                                <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Destination Location <span class="text-red-900 dark:text-red-400">*</span></label>
                                <span> : </span>
                                <select name="destination_loc_id" id="location-select" class="px-1 mx-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                    <option selected value="">Choose a Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ old('destination_loc_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('destination_loc_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alasan <span class="text-red-900 dark:text-red-400">*</span></label>
                                <textarea type="text" name="reason" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">{{ old('reason') }}</textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-5 mb-5">
                            <div class="md:col-span-1">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2">
                                    Attachment List
                                </h2>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="attachments">Upload Attachment</label>
                                <input name="attachments[]" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" id="attachments" type="file" multiple>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">Anda bisa melampirkan lebih dari satu file, satu file maksimal 5MB.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-5 mb-5">
                            <div class="md:col-span-1">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2">
                                    Approval List
                                </h2>
                            </div>

                            <div>
                                <div class="border-2 border-black rounded-lg p-4 dark:border-gray-400">
                                    <div class="flex flex-row mb-2">
                                        <label class="w-auto mr-2 text-sm font-medium text-gray-900 dark:text-white">Sequence <span class="text-red-900 dark:text-red-400">*</span> : </label>
                                        <div class="flex items-center pr-4">
                                            <input id="sequence-yes" name="sequence" type="radio" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('sequence', 1) == 1 ? 'checked' : '' }}>
                                            <label for="sequence-yes" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ya <span class="italic">(Yes)</span></label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="sequence-no" name="sequence" type="radio" value="0" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('sequence') == 0 ? 'checked' : '' }}>
                                            <label for="sequence-no" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Tidak <span class="italic">(No)</span></label>
                                        </div>
                                        @error('sequence')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="mb-2">
                                    
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-200">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                                                <tr class="text-center">
                                                    <th scope="col" colspan="2" class="px-2 py-3">Persetujuan Approval</th>
                                                    <th scope="col" class="px-2 py-3">Name</th>
                                                    <th scope="col" class="px-2 py-3">Signature</th>
                                                    <th scope="col" class="px-2 py-3">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody id="asset-list-body">
                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[0][approval_action]" value="Submitted by" class="border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[0][approval_action]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[0][role]" value="User" class="approval-role border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[0][role]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <td class="px-2 py-4">
                                                        <select name="approvals[0][user_id]" class="approval-user-select overflow-auto block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach($users as $user)
                                                                {{-- Tambahkan atribut data-role di sini --}}
                                                                <option value="{{ $user->id }}" 
                                                                        data-role="{{ $user->user_role }}"
                                                                        {{ old("approvals.0.user_id", $approvals[0]->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("approvals[0][user_id]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="text" name="approvals[0][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[0][status]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="date" name="approvals[0][approval_date]" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[0][approval_date]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>

                                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[1][approval_action]" value="Known by" class="border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[1][approval_action]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[1][role]" value="User Manager" class="approval-role border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[1][role]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <td class="px-2 py-4">
                                                        <select name="approvals[1][user_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach($users as $user)
                                                                {{-- Tambahkan atribut data-role di sini --}}
                                                                <option value="{{ $user->id }}" 
                                                                        data-role="{{ $user->user_role }}"
                                                                        {{ old("approvals.1.user_id", $approvals[1]->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("approvals[1][user_id]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="text" name="approvals[1][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[1][status]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="date" name="approvals[1][approval_date]" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[1][approval_date]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>

                                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[2][approval_action]" value="Approved by" class="border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[2][approval_action]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[2][role]" value="Site Director" class="approval-role border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[2][role]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <td class="px-2 py-4">
                                                        <select name="approvals[2][user_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach($users as $user)
                                                                {{-- Tambahkan atribut data-role di sini --}}
                                                                <option value="{{ $user->id }}" 
                                                                        data-role="{{ $user->user_role }}"
                                                                        {{ old("approvals.2.user_id", $approvals[2]->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("approvals[2][user_id]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="text" name="approvals[2][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[2][status]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="date" name="approvals[2][approval_date]" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[2][approval_date]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>

                                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[3][approval_action]" value="Checked by" class="border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[3][approval_action]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[3][role]" value="Asset Management" class="approval-role border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[3][role]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <td class="px-2 py-4">
                                                        <select name="approvals[3][user_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach($users as $user)
                                                                {{-- Tambahkan atribut data-role di sini --}}
                                                                <option value="{{ $user->id }}" 
                                                                        data-role="{{ $user->user_role }}"
                                                                        {{ old("approvals.3.user_id", $approvals[3]->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("approvals[3][user_id]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="text" name="approvals[3][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[3][status]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="date" name="approvals[3][approval_date]" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[3][approval_date]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>

                                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[4][approval_action]" value="Approved by" class="border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[4][approval_action]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[4][role]" value="CFO" class="approval-role border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[4][role]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <td class="px-2 py-4">
                                                        <select name="approvals[4][user_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach($users as $user)
                                                                {{-- Tambahkan atribut data-role di sini --}}
                                                                <option value="{{ $user->id }}" 
                                                                        data-role="{{ $user->user_role }}"
                                                                        {{ old("approvals.4.user_id", $approvals[4]->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("approvals[4][user_id]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="text" name="approvals[4][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[4][status]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="date" name="approvals[4][approval_date]" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[4][approval_date]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>

                                                <tr class="approval-row bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"> 
                                                        <input type="text" name="approvals[5][approval_action]" value="Approved by" class="border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[5][approval_action]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[5][role]" value="Director" class="approval-role border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[5][role]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <td class="px-2 py-4">
                                                        <select name="approvals[5][user_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach($users as $user)
                                                                {{-- Tambahkan atribut data-role di sini --}}
                                                                <option value="{{ $user->id }}" 
                                                                        data-role="{{ $user->user_role }}"
                                                                        {{ old("approvals.5.user_id", $approvals[5]->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("approvals[5][user_id]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="text" name="approvals[5][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[5][status]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="date" name="approvals[5][approval_date]" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[5][approval_date]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>

                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[6][approval_action]" value="Accepted by" class="border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[6][approval_action]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        <input type="text" name="approvals[6][role]" value="User" class="approval-role border-0 focus:ring-0 dark:bg-gray-800" readonly/>
                                                        @error("approvals[6][role]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </th>
                                                    <td class="px-2 py-4">
                                                        <select name="approvals[6][user_id]" class="approval-user-select block py-1 px-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:bg-gray-800 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                                                            <option value="">Pilih Nama</option>
                                                            @foreach($users as $user)
                                                                {{-- Tambahkan atribut data-role di sini --}}
                                                                <option value="{{ $user->id }}" 
                                                                        data-role="{{ $user->user_role }}"
                                                                        {{ old("approvals.6.user_id", $approvals[6]->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("approvals[6][user_id]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="text" name="approvals[6][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[6][status]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td class="px-2 py-4">
                                                        <input type="date" name="approvals[6][approval_date]" class="block py-1 px-0 w-full text-sm text-center text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0 peer" readonly />
                                                        @error("approvals[6][approval_date]")
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="company_id" value="{{ Auth::user()->last_active_company_id }}" required />
                    </div>
                </div>

                <div class="hidden" id="asset" role="tabpanel" aria-labelledby="asset-tab">
                    <div class="relative overflow-x-auto py-5 px-6 bg-white dark:bg-gray-800">
                        <table id="assetTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-200">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
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
                                    <th scope="col" class="px-6 py-3">Commercial Useful Life Month</th>
                                    <th scope="col" class="px-6 py-3">Commercial Accum Depre</th>
                                    <th scope="col" class="px-6 py-3">Commercial Net Book Value</th>
                                    <th scope="col" class="px-6 py-3">Fiscal Useful Life Month</th>
                                    <th scope="col" class="px-6 py-3">Fiscal Accum Depre</th>
                                    <th scope="col" class="px-6 py-3">Fiscal Net Book Value</th>
                                </tr>
                                <tr id="filter-row">
                                    <th><input type="hidden" name="asset_ids" id="selected-asset-ids"></th>
                                    <th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th>
                                    <th></th><th></th><th></th><th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                        <div class="p-4">
                            <span id="selected-count-display" class="font-bold dark:text-gray-200">0 asset(s) selected</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-5 pb-5 rounded-b-lg bg-white shadow-md dark:bg-gray-800">
                <div class="flex flex-col w-full">
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
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Create</button>
                        <a href="{{ route('transfer-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</a>
                    </div>
                </div>            
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const oldAssetIdsString = @json(old('asset_ids'));

        let initialIds = [];

        if (oldAssetIdsString && typeof oldAssetIdsString === 'string') {
            initialIds = oldAssetIdsString.split(',');
        }

        const selectedAssetIds = new Set(initialIds);

        function updateSelection() {
            const selectedCount = selectedAssetIds.size;
            $('#selected-count-display').text(`${selectedCount} asset(s) selected`);
            $('#selected-asset-ids').val(Array.from(selectedAssetIds).join(','));
        }

        updateSelection();

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
                        targets: [14, 16, 17, 19, 20], 
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

            $('#assetTable tbody').on('change', '.asset-checkbox', function() {
                const assetId = $(this).val();
                if (this.checked) {
                    selectedAssetIds.add(assetId);
                } else {
                    selectedAssetIds.delete(assetId);
                }
                updateSelection();
            });

            $('#select-all-assets').on('change', function() {
                const isChecked = this.checked;
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

            table.on('draw', function() {
                $('#assetTable tbody .asset-checkbox').each(function() {
                    if (selectedAssetIds.has($(this).val())) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
                $('#select-all-assets').prop('checked', false);
            });

            table.columns().every(function() {
                var that = this;
                
                $('input', $('#assetTable thead #filter-row').children().eq(this.index())).on('keyup change clear', function(e) {
                    e.stopPropagation();
                    if (that.search() !== this.value) {
                        that.search(this.value).draw();
                    }
                });
            });
        }

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

        document.querySelectorAll('.approval-row').forEach(row => {
            filterUsersByRole(row);
        });
    });
</script>
    @vite('resources/js/pages/alert.js')
@endpush