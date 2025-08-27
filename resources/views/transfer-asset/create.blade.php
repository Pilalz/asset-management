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
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <form id="main-transfer-form" class="max-w mx-auto" action="{{ route('transfer-asset.store') }}" method="POST">
                @csrf

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
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Department <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <input type="text" id="department-display" value="" class="block py-1 px-0 mx-2 w-full text-sm text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0" readonly/>
                    <input type="hidden" name="department_id" id="department-id-input" value="">
                    @error('department_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Data <span class="text-red-900">*</span></label>
                    <div class="border-2 border-black rounded-lg p-4">
                        <div class="flex content-center mb-2">
                            <label class="w-24 text-sm font-medium text-gray-900 dark:text-white">Asset No. <span class="text-red-900">*</span></label>
                            <span> : </span>
                            <input type="text" id="asset-number-input" class="block py-1 mx-2 px-0 w-48 text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                            <button type="button" id="select-asset-btn" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-3 py-2 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Select</button>
                        </div>

                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-2 py-3">Asset Name</th>
                                    <th scope="col" class="px-2 py-3">Description</th>
                                    <th scope="col" class="px-2 py-3">ID Pareto</th>
                                    <th scope="col" class="px-2 py-3">No. Unit</th>
                                    <th scope="col" class="px-2 py-3">No. Mesin</th>
                                    <th scope="col" class="px-2 py-3">No. Engine</th>
                                    <th scope="col" class="px-2 py-3">Tahun Pembelian</th>
                                    <th scope="col" class="px-2 py-3">Location</th>
                                </tr>
                            </thead>
                            <tbody id="asset-data-body">
                                
                                <tr>
                                    <td colspan="8" class="text-center p-4">Please select an asset.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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

                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Create</button>
                <a href="{{ route('transfer-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        const alertElements = document.querySelectorAll('.auto-dismiss-alert');
        // Hanya jalankan jika ada elemen notifikasi di halaman
        if (alertElements.length > 0) {
            alertElements.forEach(targetEl => {
                // Gunakan inisialisasi sederhana dari Flowbite jika sudah dimuat global
                const dismiss = new Dismiss(targetEl); 
                setTimeout(() => {
                    dismiss.hide();
                }, 5000); // Hilang setelah 5 detik
            });
        }

        const selectAssetBtn = document.getElementById('select-asset-btn');
        const assetNumberInput = document.getElementById('asset-number-input');
        const assetDataBody = document.getElementById('asset-data-body');
        const mainForm = document.getElementById('main-transfer-form');

        selectAssetBtn.addEventListener('click', () => {
            const assetNumber = assetNumberInput.value.trim();
            if (!assetNumber) {
                alert('Please enter an asset number.');
                return;
            }

            // Tampilkan status loading
            assetDataBody.innerHTML = '<tr><td colspan="8" class="text-center p-4">Loading...</td></tr>';
            
            // Buat URL API
            const url = `/api/find-asset/${assetNumber}`;

            // Lakukan panggilan Fetch API (AJAX)
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        // Coba baca pesan error dari JSON jika ada
                        return response.json().then(err => { throw new Error(err.error || 'Asset not found'); });
                    }
                    return response.json();
                })
                .then(data => {
                    // Hapus input asset_id yang mungkin sudah ada sebelumnya
                    let existingInput = mainForm.querySelector('input[name="asset_id"]');
                    if (existingInput) {
                        existingInput.remove();
                    }

                    const departmentDisplayInput = document.getElementById('department-display');
                    const departmentIdInput = document.getElementById('department-id-input');

                    if (departmentDisplayInput && data.department) {
                        departmentDisplayInput.value = data.department.name;
                    }
                    if (departmentIdInput && data.department_id) {
                        departmentIdInput.value = data.department_id;
                    }

                    // Buat input hidden untuk asset_id dan tambahkan ke form utama
                    const assetIdInput = document.createElement('input');
                    assetIdInput.type = 'hidden';
                    assetIdInput.name = 'asset_id';
                    assetIdInput.value = data.id;
                    mainForm.appendChild(assetIdInput);

                    // Buat format mata uang
                    const nbv = new Intl.NumberFormat('en-US', { 
                        style: 'currency', 
                        currency: 'USD',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(data.net_book_value);

                    const depre = new Intl.NumberFormat('en-US', { 
                        style: 'currency', 
                        currency: 'USD',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(data.accum_depre);

                    // Tampilkan data di dalam tabel
                    assetDataBody.innerHTML = `
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-4 py-4">
                                ${data.asset_name_id ? data.asset_name.name : 'N/A'}
                                <input type="hidden" name="asset_id" value="${data.id || '-'}" />
                            </td>
                            <td class="px-4 py-4">${data.description || '-'}</td>
                            <td class="px-4 py-4">${data.pareto || '-'}</td>
                            <td class="px-4 py-4">${data.unit_no || '-'}</td>
                            <td class="px-4 py-4">${data.sn_chassis || '-'}</td>
                            <td class="px-4 py-4">${data.sn_engine || '-'}</td>
                            <td class="px-4 py-4">${data.capitalized_date ? new Date(data.capitalized_date).getFullYear() : '-'}</td>
                            <td class="px-4 py-4">
                                ${data.location ? data.location.name : 'N/A'}
                                <input type="hidden" name="origin_loc_id" value="${data.location_id || '-'}" />
                            </td>
                        </tr>
                    `;
                })
                .catch(error => {
                    assetDataBody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-red-500">${error.message}</td></tr>`;
                });
        });
    });
</script>
@endpush