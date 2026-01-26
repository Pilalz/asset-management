@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li
                    class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path
                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                    </svg>
                    Asset
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ route('asset.index') }}"
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Fixed
                            Asset</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md py-5 px-6 rounded-lg bg-white dark:bg-gray-800">
            <form class="max-w mx-auto" action="{{ route('asset.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 mb-5">
                    <div class="md:col-span-2">
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2">
                            Basic Asset Information
                        </h2>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Number <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <input type="text" name="asset_number" value="{{ old('asset_number', $asset->asset_number) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('asset_number')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Class <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <select id="asset-class-select" name="asset_class_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="">Choose an Asset Class</option>
                            @foreach($assetclasses as $assetclass)
                                <option value="{{ $assetclass->id }}" {{ (old('asset_class_id', $asset->assetName->assetSubClass->assetClass->id ?? '') == $assetclass->id) ? 'selected' : '' }}>
                                    {{ $assetclass->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Sub Class <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <select id="asset-sub-class-select" name="asset_sub_class_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Choose an Asset Sub Class</option>
                            {{-- JavaScript akan mengisi pilihan di sini --}}
                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asset Name <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <select id="asset-name-select" name="asset_name_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Choose an Asset Name</option>
                            {{-- JavaScript akan mengisi pilihan di sini --}}
                        </select>
                    </div>

                    <!-- selector input for status -->
                    @php
                        $standardStatuses = ['Active', 'Breakdown', 'RFU', 'Scrap', 'Sold'];
                        $currentStatus = old('status', $asset->status);
                        $isOther = !in_array($currentStatus, $standardStatuses) && !empty($currentStatus);
                    @endphp
                    <div>
                        <label for="status-select"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <select name="status"
                            class="status-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="Active" @if($currentStatus == 'Active') selected @endif>Active</option>
                            <option value="Breakdown" @if($currentStatus == 'Breakdown') selected @endif>Breakdown</option>
                            <option value="RFU" @if($currentStatus == 'RFU') selected @endif>RFU</option>
                            <option value="Scrap" @if($currentStatus == 'Scrap') selected @endif>Scrap</option>
                            <option value="Sold" @if($currentStatus == 'Sold') selected @endif>Sold</option>
                            <option value="Other" @if($isOther) selected @endif>Other</option>
                        </select>
                        @error('status')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- hidden input for status -->
                    <div id="other-status-wrapper" class="hidden">
                        <label for="other-status-input" class="block mb-2 text-sm font-medium">Please specify other
                            status</label>
                        <input type="text" id="other-status-input" value="{{ $isOther ? $currentStatus : '' }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Location <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <select name="location_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="">Choose a Location</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ (old('location_id', $asset->location_id) == $location->id) ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('location_id')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Department <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <select name="department_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected value="">Choose a Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ (old('department_id', $asset->department_id) == $department->id) ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User </label>
                        <input type="text" name="user" value="{{ old('user', $asset->user) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('user')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantity <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <input type="number" name="quantity" value="{{ old('quantity', $asset->quantity) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('quantity')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 mb-5">
                    <div class="md:col-span-2">
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2">
                            Details & Specifications
                        </h2>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <input type="text" name="description" value="{{ old('description', $asset->description) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('description')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Detail </label>
                        <input type="text" name="detail" value="{{ old('detail', $asset->detail) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('detail')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="production-year"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Production Year</label>
                        <select name="production_year" id="production-year"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Choose a Year</option>
                            @for ($year = now()->year; $year >= now()->year - 20; $year--)
                                @php
                                    // Ambil tahun dari database, pastikan null jika tidak ada data
                                    $selectedDbYear = $asset->production_year ? \Carbon\Carbon::parse($asset->production_year)->format('Y') : null;
                                @endphp
                                <option value="{{ $year }}" {{ old('production_year', $selectedDbYear) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                        @error('production_year')
                            <div class="text-red-500 mt-2 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit No </label>
                        <input type="text" name="unit_no" value="{{ old('unit_no', $asset->unit_no) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('unit_no')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sn Engine </label>
                        <input type="text" name="sn_engine" value="{{ old('sn_engine', $asset->sn_engine) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('sn_engine')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Sn Chassis </label>
                        <input type="text" name="sn_chassis" value="{{ old('sn_chassis', $asset->sn_chassis) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('sn_chassis')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pareto </label>
                        <input type="text" name="pareto" value="{{ old('pareto', $asset->pareto) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('pareto')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 mb-5">
                    <div class="md:col-span-2">
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2">
                            Financial Information
                        </h2>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PO No <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <input type="text" name="po_no" value="{{ old('po_no', $asset->po_no) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                        @error('po_no')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($asset->depreciations->count() === 0)
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Capitalized Date <span
                                    class="text-red-900 dark:text-red-400">*</span></label>
                            <input type="date" name="capitalized_date"
                                value="{{ old('capitalized_date', $asset->capitalized_date?->format('Y-m-d')) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required />
                            @error('capitalized_date')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Depre Date <span
                                    class="text-red-900 dark:text-red-400">*</span></label>
                            <input type="date" name="start_depre_date"
                                value="{{ old('start_depre_date', $asset->start_depre_date?->format('Y-m-d')) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required />
                            @error('start_depre_date')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Acquisition Value <span
                                    class="text-red-900 dark:text-red-400">*</span></label>
                            <div class="relative ">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    @if($activeCompany->currency === 'USD')
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                        </svg>
                                    @elseif($activeCompany->currency === 'IDR')
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                    @endif
                                </div>
                                <input type="text" id="acquisition_value_display"
                                    value="{{ old('acquisition_value', $asset->acquisition_value) }}"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required />
                                <input type="hidden" name="acquisition_value" id="acquisition_value_hidden"
                                    value="{{ old('acquisition_value', $asset->acquisition_value) }}">
                                @error('acquisition_value')
                                    <div class="text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Capitalized Date <span
                                    class="text-red-900 dark:text-red-400">*</span></label>
                            <input type="date" name="capitalized_date"
                                value="{{ old('capitalized_date', $asset->capitalized_date?->format('Y-m-d')) }}"
                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required readonly />
                            @error('capitalized_date')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Depre Date <span
                                    class="text-red-900 dark:text-red-400">*</span></label>
                            <input type="date" name="start_depre_date"
                                value="{{ old('start_depre_date', $asset->start_depre_date?->format('Y-m-d')) }}"
                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required readonly />
                            @error('start_depre_date')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Acquisition Value <span
                                    class="text-red-900 dark:text-red-400">*</span></label>
                            <div class="relative ">
                                <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                    @if($activeCompany->currency === 'USD')
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                        </svg>
                                    @elseif($activeCompany->currency === 'IDR')
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                    @endif
                                </div>
                                <input type="text" id="acquisition_value_display"
                                    value="{{ old('acquisition_value', $asset->acquisition_value) }}"
                                    class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    required readonly />
                                <input type="hidden" name="acquisition_value" id="acquisition_value_hidden"
                                    value="{{ old('acquisition_value', $asset->acquisition_value) }}" />
                                @error('acquisition_value')
                                    <div class="text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Current Cost <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                @if($activeCompany->currency === 'USD')
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                    </svg>
                                @elseif($activeCompany->currency === 'IDR')
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                @endif
                            </div>
                            <input type="text" id="current_cost_display"
                                value="{{ old('current_cost', $asset->current_cost) }}"
                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required readonly />
                            <input type="hidden" id="current_cost_hidden" name="current_cost"
                                value="{{ old('current_cost', $asset->current_cost) }}" />
                            @error('current_cost')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Commercial Useful Life
                            Month <span class="text-red-900 dark:text-red-400">*</span></label>
                        <input type="text" name="commercial_useful_life_month"
                            value="{{ old('commercial_useful_life_month', $asset->commercial_useful_life_month) }}"
                            class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            required readonly />
                        @error('commercial_useful_life_month')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Commercial Accum Depre
                            <span class="text-red-900 dark:text-red-400">*</span></label>
                        <div class="relative ">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                @if($activeCompany->currency === 'USD')
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                    </svg>
                                @elseif($activeCompany->currency === 'IDR')
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                @endif
                            </div>
                            <input type="text" id="commercial_accum_display"
                                value="{{ old('commercial_accum_depre', $asset->commercial_accum_depre) }}"
                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required readonly />
                            <input type="hidden" id="commercial_accum_hidden" name="commercial_accum_depre"
                                value="{{ old('commercial_accum_depre', $asset->commercial_accum_depre) }}" />
                            @error('commercial_accum_depre')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Commercial Net Book
                            Value <span class="text-red-900 dark:text-red-400">*</span></label>
                        <div class="relative ">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                @if($activeCompany->currency === 'USD')
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                    </svg>
                                @elseif($activeCompany->currency === 'IDR')
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                @endif
                            </div>
                            <input type="text" id="commercial_nbv_display"
                                value="{{ old('commercial_nbv', $asset->commercial_nbv) }}"
                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required readonly />
                            <input type="hidden" id="commercial_nbv_hidden" name="commercial_nbv"
                                value="{{ old('commercial_nbv', $asset->commercial_nbv) }}" />
                            @error('commercial_nbv')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fiscal Useful Life Month
                            <span class="text-red-900 dark:text-red-400">*</span></label>
                        <input type="text" name="fiscal_useful_life_month"
                            value="{{ old('fiscal_useful_life_month', $asset->fiscal_useful_life_month) }}"
                            class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            required readonly />
                        @error('fiscal_useful_life_month')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fiscal Accum Depre <span
                                class="text-red-900 dark:text-red-400">*</span></label>
                        <div class="relative ">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                @if($activeCompany->currency === 'USD')
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                    </svg>
                                @elseif($activeCompany->currency === 'IDR')
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                @endif
                            </div>
                            <input type="text" id="fiscal_accum_display"
                                value="{{ old('fiscal_accum_depre', $asset->fiscal_accum_depre) }}"
                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required readonly />
                            <input type="hidden" id="fiscal_accum_hidden" name="fiscal_accum_depre"
                                value="{{ old('fiscal_accum_depre', $asset->fiscal_accum_depre) }}" />
                            @error('fiscal_accum_depre')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fiscal Net Book Value
                            <span class="text-red-900 dark:text-red-400">*</span></label>
                        <div class="relative ">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                @if($activeCompany->currency === 'USD')
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                    </svg>
                                @elseif($activeCompany->currency === 'IDR')
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Rp</span>
                                @endif
                            </div>
                            <input type="text" id="fiscal_nbv_display" value="{{ old('fiscal_nbv', $asset->fiscal_nbv) }}"
                                class="bg-gray-200 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-0 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                required readonly />
                            <input type="hidden" id="fiscal_nbv_hidden" name="fiscal_nbv"
                                value="{{ old('fiscal_nbv', $asset->fiscal_nbv) }}" />
                            @error('fiscal_nbv')
                                <div class="text-red-500">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-1 gap-x-6 gap-y-5 mb-5">
                    <div class="md:col-span-2">
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2">
                            Remark
                        </h2>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remark </label>
                        <input type="text" name="remaks" value="{{ old('remaks', $asset->remaks) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                        @error('remaks')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700">Update</button>
                    <a href="{{ route('asset.index') }}"
                        class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            //Asset Class, Asset Sub Class, Asset Name
            const classSelect = document.getElementById('asset-class-select');
            const subClassSelect = document.getElementById('asset-sub-class-select');
            const nameSelect = document.getElementById('asset-name-select');

            // Simpan nilai awal dari database untuk pemilihan otomatis
            const initialSubClassId = "{{ old('asset_sub_class_id', $asset->assetName->assetSubClass->id ?? '') }}";
            const initialNameId = "{{ old('asset_name_id', $asset->assetName->id ?? '') }}";

            // Fungsi untuk memuat Sub Class berdasarkan Class ID
            async function loadSubClasses(classId, selectedSubClassId = null) {
                if (!classId) {
                    subClassSelect.innerHTML = '<option value="">Choose an Asset Sub Class</option>';
                    nameSelect.innerHTML = '<option value="">Choose an Asset Name</option>';
                    return;
                }

                const response = await fetch(`/api/asset-sub-classes-by-class/${classId}`);
                const data = await response.json();

                subClassSelect.innerHTML = '<option value="">Choose an Asset Sub Class</option>';
                data.forEach(sub => {
                    const option = new Option(sub.name, sub.id);
                    if (selectedSubClassId && selectedSubClassId == sub.id) {
                        option.selected = true;
                    }
                    subClassSelect.add(option);
                });

                // Jika ada sub class yang terpilih, picu pemuatan asset name
                if (selectedSubClassId) {
                    loadAssetNames(selectedSubClassId, initialNameId);
                }
            }

            // Fungsi untuk memuat Asset Name berdasarkan Sub Class ID
            async function loadAssetNames(subClassId, selectedNameId = null) {
                if (!subClassId) {
                    nameSelect.innerHTML = '<option value="">Choose an Asset Name</option>';
                    return;
                }

                const response = await fetch(`/api/asset-names-by-sub-class/${subClassId}`);
                const data = await response.json();

                nameSelect.innerHTML = '<option value="">Choose an Asset Name</option>';
                data.forEach(name => {
                    const option = new Option(name.name, name.id);
                    if (selectedNameId && selectedNameId == name.id) {
                        option.selected = true;
                    }
                    nameSelect.add(option);
                });
            }

            // Event listener untuk dropdown Asset Class
            classSelect.addEventListener('change', function () {
                loadSubClasses(this.value);
            });

            // Event listener untuk dropdown Asset Sub Class
            subClassSelect.addEventListener('change', function () {
                loadAssetNames(this.value);
            });

            // Jika ada nilai awal untuk Asset Class, muat Sub Class yang sesuai
            if (classSelect.value) {
                loadSubClasses(classSelect.value, initialSubClassId);
            }

            //Currency
            const acquisitionDisplay = document.getElementById('acquisition_value_display');
            const acquisitionHidden = document.getElementById('acquisition_value_hidden');
            const currentCostDisplay = document.getElementById('current_cost_display');
            const currentCostHidden = document.getElementById('current_cost_hidden');
            const commercialNbvDisplay = document.getElementById('commercial_nbv_display');
            const commercialNbvHidden = document.getElementById('commercial_nbv_hidden');
            const commercialAccumDisplay = document.getElementById('commercial_accum_display');
            const commercialAccumHidden = document.getElementById('commercial_accum_hidden');
            const fiscalNbvDisplay = document.getElementById('fiscal_nbv_display');
            const fiscalNbvHidden = document.getElementById('fiscal_nbv_hidden');
            const fiscalAccumDisplay = document.getElementById('fiscal_accum_display');
            const fiscalAccumHidden = document.getElementById('fiscal_accum_hidden');

            function formatRupiah(angka) {
                const currencyCode = '{{ $activeCompany->currency ?? 'IDR' }}';
                const locale = (currencyCode === 'USD') ? 'en-US' : 'id-ID';

                if (!angka || isNaN(angka)) return '';
                return new Intl.NumberFormat(locale).format(angka);
            }

            function unformatRupiah(rupiahStr) {
                return rupiahStr.replace(/[^0-9]/g, '');
            }

            function processAndSyncValue(inputValue) {
                let rawValue = unformatRupiah(inputValue);
                let formattedValue = formatRupiah(rawValue);

                // Perbarui semua input display
                acquisitionDisplay.value = formattedValue;
                currentCostDisplay.value = formattedValue;
                commercialNbvDisplay.value = formattedValue;
                commercialAccumDisplay.value = formattedValue;
                fiscalNbvDisplay.value = formattedValue;
                fiscalAccumDisplay.value = formattedValue;

                // Perbarui semua input hidden
                acquisitionHidden.value = rawValue;
                currentCostHidden.value = rawValue;
                commercialNbvHidden.value = rawValue;
                commercialAccumHidden.value = rawValue;
                fiscalNbvHidden.value = rawValue;
                fiscalAccumHidden.value = rawValue;
            }

            acquisitionDisplay.addEventListener('input', function (e) {
                processAndSyncValue(e.target.value);
            });

            processAndSyncValue(acquisitionDisplay.value);

            // Status
            const statusSelect = document.querySelector('.status-select');
            const otherStatusWrapper = document.getElementById('other-status-wrapper');
            const otherStatusInput = document.getElementById('other-status-input');

            function handleStatusChange() {
                if (statusSelect.value === 'Other') {
                    otherStatusWrapper.classList.remove('hidden');
                    otherStatusInput.setAttribute('name', 'status');
                    statusSelect.removeAttribute('name');
                } else {
                    otherStatusWrapper.classList.add('hidden');
                    statusSelect.setAttribute('name', 'status');
                    otherStatusInput.removeAttribute('name');
                }
            }

            statusSelect.addEventListener('change', handleStatusChange);

            handleStatusChange();
        });
    </script>
@endpush