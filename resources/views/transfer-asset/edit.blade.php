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
                        Asset
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('transfer-asset.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Asset Sub Class</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Edit</span> {{-- Diubah dari Create menjadi Edit --}}
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 sm:rounded-lg bg-white dark:bg-gray-900">
            <form class="max-w mx-auto" action="{{ route('transfer-asset.update', $transfer_asset->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-5 flex content-center">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Tanggal Pengajuan <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <p class="w-full px-2">{{ old("transfer_asset.submit_date", $transfer_asset->submit_date ?? '') }}</p>
                    <input type="hidden" name="submit_date" value="{{ old("transfer_asset.submit_date", $transfer_asset->submit_date ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                    @error('submit_date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5 flex content-center">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Nomor Formulir <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <p class="w-full px-2">{{ old('form_no', $transfer_asset->form_no) }}</p>
                    <input type="hidden" name="form_no" value="{{ old('form_no', $transfer_asset->form_no) }}" class="px-1 w-64 text-sm text-gray-900 appearance-none dark:text-white" readonly/>
                    @error('form_no')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5 flex content-center">
                    <label class="w-48 text-sm font-medium text-gray-900 dark:text-white">Department <span class="text-red-900">*</span></label>
                    <span> : </span>
                    <input type="text" id="department-display" value="{{ old('department_id', $transfer_asset->department->name) }}" class="block py-1 px-0 mx-2 w-full text-sm text-gray-900 bg-transparent border-0 appearance-none dark:text-white focus:outline-none focus:ring-0" readonly/>
                    <input type="hidden" name="department_id" id="department-id-input" value="{{ old('department_id', $transfer_asset->department->id ) }}">
                    @error('department_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Data <span class="text-red-900">*</span></label>
                    <div class="border-2 border-black rounded-lg p-4">
                        <div class="flex content-center mb-2">
                            @php $asset = $transfer_asset->asset; @endphp
                            <label class="w-24 text-sm font-medium text-gray-900 dark:text-white">Asset <span class="text-red-900">*</span></label>
                            <span> : </span>
                            <input type="text" id="asset-number-input" value="{{ $asset->asset_number ?? 'N/A' }}" class="block py-1 mx-2 px-0 w-48 text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
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
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-2 py-4">
                                        {{ $asset->assetName->name ?? 'N/A' }}
                                        <input type="hidden" name="asset_id" value="{{ $asset->id ?? 'N/A' }}">
                                    </td>
                                    <td class="px-2 py-4">{{ $asset->description ?? '-' }}</td>
                                    <td class="px-2 py-4">{{ $asset->pareto ?? '-' }}</td>
                                    <td class="px-2 py-4">{{ $asset->unit_no ?? '-' }}</td>
                                    <td class="px-2 py-4">{{ $asset->sn_chassis ?? '-' }}</td>
                                    <td class="px-2 py-4">{{ $asset->sn_engine ?? '-' }}</td>
                                    <td class="px-2 py-4">{{ $asset->capitalized_date?->format('Y') ?? '-' }}</td>
                                    <td class="px-2 py-4">
                                        {{ $asset->location->name ?? 'N/A' }}
                                        <input type="hidden" name="origin_loc_id" value="{{ $asset->location->id ?? 'N/A' }}">
                                    </td>
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
                            <option value="{{ $location->id }}" {{ (old('destination_loc_id', $transfer_asset->destination_loc_id) == $location->id) ? 'selected' : '' }}>
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
                    <textarea type="text" name="reason" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        {{ old("transfer_asset.reason", $transfer_asset->reason ?? '') }}
                    </textarea>
                    @error('reason')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Approval List <span class="text-red-900">*</span></label>
                    <div class="border-2 border-black rounded-lg p-4">
                        
                        <div class="flex flex-row mb-2">
                            <label class="w-auto mr-2 text-sm font-medium text-gray-900 dark:text-white">Sequence <span class="text-red-900">*</span> : </label>
                            <div class="flex items-center pr-4">
                                <input id="sequence-yes" name="sequence" type="radio" value="Y" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('sequence', $transfer_asset->sequence) == '1' ? 'checked' : '' }}>
                                <label for="sequence-yes" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Ya <span class="italic">(Yes)</span></label>
                            </div>
                            <div class="flex items-center">
                                <input id="sequence-no" name="sequence" type="radio" value="N" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" {{ old('sequence', $transfer_asset->sequence) == '0' ? 'checked' : '' }}>
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
                                @php $initialApprovals = old('approvals', $transfer_asset->approvals); @endphp
                                @foreach($initialApprovals as $index => $approvalData)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            <input type="text" name="approvals[{{$index}}][approval_action]" value="{{ old("approvals.$index.approval_action", $approvalData->approval_action ?? '') }}" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                            @error("approvals[{{$index}}][approval_action]")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </th>   
                                        <th scope="row" class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            <input type="text" name="approvals[{{$index}}][role]" value="{{ old("approvals.$index.role", $approvalData->role ?? '') }}" class="border border-white focus:ring-0 focus:border-white-600" readonly/>
                                            @error("approvals[{{$index}}][role]")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </th>
                                        <td class="px-2 py-4">
                                            <input type="hidden" name="approvals[{{$index}}][user_id]" value="{{ old("approvals.$index.user_id", $approvalData->user_id ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                            <input type="text" value="{{ old("approvals.$index.user_id", $approvalData->user->name ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" />
                                            @error("approvals[{{$index}}][user_id]")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="px-2 py-4">
                                            <input type="text" name="approvals[{{$index}}][status]" value="{{ old("approvals.$index.status", $approvalData->status ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="" />
                                            @error("approvals[{{$index}}][status]")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td class="px-2 py-4">
                                            <input type="date" name="approvals[{{$index}}][approval_date]" value="{{ old("approvals.$index.approval_date", $approvalData->approval_date ?? '') }}" class="block py-1 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder="Asset Details" />
                                            @error("approvals[{{$index}}][approval_date]")
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <input type="hidden" name="company_id" value="{{ Auth::user()->last_active_company_id }}" required />

                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Update</button> {{-- Diubah dari Submit menjadi Update --}}
                <a href="{{ route('transfer-asset.index') }}" class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 ml-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection