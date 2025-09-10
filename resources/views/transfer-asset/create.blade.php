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
        <div class="border-b bg-white rounded-t-lg border-gray-200 dark:border-gray-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="formulir-tab" data-tabs-target="#formulir" type="button" role="tab" aria-controls="formulir" aria-selected="false">Form <span class="text-red-900">*</span></button>
                </li>
                <li class="me-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="asset-tab" data-tabs-target="#asset" type="button" role="tab" aria-controls="asset" aria-selected="false">Asset <span class="text-red-900">*</span></button>
                </li>
            </ul>
        </div>

        <form class="max-w mx-auto" action="{{ route('transfer-asset.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="default-tab-content">
                <div class="hidden rounded-b-lg" id="formulir" role="tabpanel" aria-labelledby="formulir-tab">
                    <div class="relative overflow-x-auto py-5 px-6 bg-white dark:bg-gray-900">

                        <div class="mb-5 flex content-center">
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pengajuan <span class="text-red-900">*</span></label>
                            <span> : </span>
                            <p class="w-full px-2">{{ now()->format('d F Y') }}</p>
                            <input type="hidden" name="submit_date" value="{{ now()->format('Y-m-d') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                            @error('submit_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5 flex content-center">
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Nomor Formulir <span class="text-red-900">*</span></label>
                            <span> : </span>
                            <p class="w-full px-2">{{ $form_no }}</p>
                            <input type="hidden" name="form_no" value="{{ $form_no }}" class="w-full px-1 text-sm text-gray-900 appearance-none dark:text-white" readonly/>
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
                            <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Destination Location <span class="text-red-900">*</span></label>
                            <span> : </span>
                            <select name="destination_loc_id" class="px-1 mx-1 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
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

                        <div class="mb-5">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alasan <span class="text-red-900">*</span></label>
                            <textarea type="text" name="reason" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"></textarea>
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
                                    <tbody id="asset-list-body">
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[0][approval_action]" value="Submitted by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[0][approval_action]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[0][role]" value="User" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[0][role]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <td class="px-2 py-4">
                                                <input type="hidden" name="approvals[0][user_id]" value="{{ Auth::user()->id }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                <input type="text" value="{{ Auth::user()->name }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                @error("approvals[0][user_id]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="text" name="approvals[0][status]" value="Approved" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer" readonly />
                                                @error("approvals[0][status]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="date" name="approvals[0][approval_date]" value="{{ now()->toDateString() }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                                @error("approvals[0][approval_date]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[1][approval_action]" value="Known by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[1][approval_action]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[1][role]" value="User Manager" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[1][role]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <td class="px-2 py-4">
                                                <input type="hidden" name="approvals[1][user_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                <input type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                @error("approvals[1][user_id]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="text" name="approvals[1][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer" readonly />
                                                @error("approvals[1][status]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="date" name="approvals[1][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                                @error("approvals[1][approval_date]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[2][approval_action]" value="Approved by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[2][approval_action]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[2][role]" value="Site Director" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[2][role]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <td class="px-2 py-4">
                                                <input type="hidden" name="approvals[2][user_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                <input type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                @error("approvals[2][user_id]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="text" name="approvals[2][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer" readonly />
                                                @error("approvals[2][status]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="date" name="approvals[2][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                                @error("approvals[2][approval_date]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[3][approval_action]" value="Checked by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[3][approval_action]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[3][role]" value="Asset Management" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[3][role]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <td class="px-2 py-4">
                                                <input type="hidden" name="approvals[3][user_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                <input type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                @error("approvals[3][user_id]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="text" name="approvals[3][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer" readonly />
                                                @error("approvals[3][status]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="date" name="approvals[3][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                                @error("approvals[3][approval_date]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[4][approval_action]" value="Approved by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[4][approval_action]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                
                                                <input type="text" name="approvals[4][role]" value="CFO" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[4][role]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <td class="px-2 py-4">
                                                <input type="hidden" name="approvals[4][user_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                <input type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                @error("approvals[4][user_id]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="text" name="approvals[4][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer" readonly />
                                                @error("approvals[4][status]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="date" name="approvals[4][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                                @error("approvals[4][approval_date]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white"> 
                                                <input type="text" name="approvals[5][approval_action]" value="Approved by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[5][approval_action]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[5][role]" value="Director" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[5][role]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <td class="px-2 py-4">
                                                <input type="hidden" name="approvals[5][user_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                <input type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                @error("approvals[5][user_id]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="text" name="approvals[5][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer" readonly />
                                                @error("approvals[5][status]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="date" name="approvals[5][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                                @error("approvals[5][approval_date]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>

                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[6][approval_action]" value="Accepted by" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[6][approval_action]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                <input type="text" name="approvals[6][role]" value="User" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                                @error("approvals[6][role]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </th>
                                            <td class="px-2 py-4">
                                                <input type="hidden" name="approvals[6][user_id]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                <input type="text" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                                @error("approvals[6][user_id]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="text" name="approvals[6][status]" value="Pending" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer" readonly />
                                                @error("approvals[6][status]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="px-2 py-4">
                                                <input type="date" name="approvals[6][approval_date]" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                                @error("approvals[6][approval_date]")
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <input type="hidden" name="company_id" value="{{ Auth::user()->last_active_company_id }}" required />
                    </div>
                </div>

                <div class="hidden rounded-b-lg" id="asset" role="tabpanel" aria-labelledby="asset-tab">
                    <div class="relative overflow-x-auto py-5 px-6 sm:rounded-b-lg bg-white dark:bg-gray-900">
                        <table id="assetTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
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
                            <span id="selected-count-display" class="font-bold">0 asset(s) selected</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-5 pb-5 rounded-b-lg bg-white shadow-md">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Create</button>
                <a href="{{ route('transfer-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectedAssetIds = new Set();
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
                    { data: 'useful_life_month', name: 'useful_life_month' },
                    { data: 'accum_depre', name: 'accum_depre' },
                    { data: 'net_book_value', name: 'net_book_value' },
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
                        targets: [13, 15, 16], 
                        render: function (data, type, row) {
                            if (type === 'display') {
                                let number = parseFloat(data);

                                if (isNaN(number)) {
                                    return data;
                                }

                                return number.toLocaleString('en-US', {
                                    style: 'currency',
                                    currency: 'USD',
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
    });
</script>
@endpush