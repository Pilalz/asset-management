@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between border-b border-slate-200 dark:border-gray-700 dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('asset-name.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400 transition-colors">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Asset
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ route('asset-name.index') }}"
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ms-2 dark:text-gray-400 dark:hover:text-white transition-colors">Asset
                            Name</a>
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
        <div class="max-w-full mx-auto">
            <div
                class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-slate-200 dark:border-gray-700 overflow-hidden">
                <!-- Header Card -->
                <div class="px-6 py-5 border-b border-slate-200 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Edit Asset Name Details
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Update the information for this asset name.
                    </p>
                </div>

                <!-- Form Section -->
                <form action="{{ route('asset-name.update', $asset_name->id) }}" method="POST" class="px-6 py-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Input Select Sub Class -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Select a Sub Class <span class="text-red-500">*</span>
                            </label>
                            <select name="sub_class_id"
                                class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-400 dark:focus:border-indigo-400 hover:border-slate-400 transition-colors shadow-sm @error('sub_class_id') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @enderror"
                                required>
                                <option value="">Choose a Sub Class</option>
                                @foreach($assetsubclasses as $asset_sub_class)
                                    <option value="{{ $asset_sub_class->id }}" {{ (old('sub_class_id', $asset_name->sub_class_id) == $asset_sub_class->id) ? 'selected' : '' }}>
                                        {{ $asset_sub_class->assetClass->name ?? '' }} - {{ $asset_sub_class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sub_class_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Input Name -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Asset Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $asset_name->name) }}"
                                class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-400 dark:focus:border-indigo-400 hover:border-slate-400 transition-colors shadow-sm @error('name') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @enderror"
                                placeholder="e.g. Buildings" required />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Input Asset Grouping -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Asset Grouping <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="grouping" value="{{ old('grouping', $asset_name->grouping) }}"
                                class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-400 dark:focus:border-indigo-400 hover:border-slate-400 transition-colors shadow-sm @error('grouping') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @enderror"
                                placeholder="e.g. Group A" required />
                            @error('grouping')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Input Commercial Life -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Commercial Life <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="commercial"
                                    value="{{ old('commercial', $asset_name->commercial) }}"
                                    class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-400 dark:focus:border-indigo-400 hover:border-slate-400 transition-colors shadow-sm @error('commercial') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @enderror"
                                    placeholder="4" required />
                                <small class="text-xs text-slate-500 mt-1 block">In years</small>
                                @error('commercial')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Input Fiscal Life -->
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Fiscal Life <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="fiscal" value="{{ old('fiscal', $asset_name->fiscal) }}"
                                    class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-400 dark:focus:border-indigo-400 hover:border-slate-400 transition-colors shadow-sm @error('fiscal') border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500 @enderror"
                                    placeholder="4" required />
                                <small class="text-xs text-slate-500 mt-1 block">In years</small>
                                @error('fiscal')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-3 pt-5 border-t border-slate-100 dark:border-gray-700">
                        <button type="submit"
                            class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800 transition-all shadow-sm focus:outline-none w-full sm:w-auto">
                            Update Name
                        </button>
                        <a href="{{ route('asset-name.index') }}"
                            class="text-gray-700 bg-white border border-slate-300 hover:bg-slate-50 focus:ring-4 focus:ring-slate-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 transition-all shadow-sm focus:outline-none w-full sm:w-auto">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection