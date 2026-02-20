@extends('layouts.main')

@section('content')

    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('stock-opname.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Stock Opname
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <a href="{{ route('stock-opname.index') }}"
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">List</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Detail</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex gap-2 content-center">
            {{-- Export button placeholder --}}
            <button id="history-btn" data-modal-target="history-modal" data-modal-toggle="history-modal"
                class="inline-flex items-center text-gray-500 bg-gray-50 border border-gray-300 focus:outline-none hover:bg-gray-200 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-400 dark:hover:bg-gray-500 dark:hover:border-gray-400">
                <svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 6.5h2M11 18h2m-7-5v-2m12 2v-2M5 8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm0 12h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm12 0h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm0-12h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Z"/>
                </svg>
                SO Scanner
            </button>
        </div>
    </div>

    <x-alerts />

    <div class="p-5 space-y-5">

        {{-- ── Basic Information ─────────────────────────────────────────── --}}
        <div class="relative overflow-x-auto shadow-md py-5 px-6 rounded-lg bg-white dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2 mb-4">
                Basic Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

                <table class="text-gray-900 dark:text-white w-full">
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4 w-40">Title</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">{{ $stockOpnameSession->title }}</td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4">Status</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">
                            @if ($stockOpnameSession->status === 'Open')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    Open
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $stockOpnameSession->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4">Deskripsi</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">{{ $stockOpnameSession->description ?? '-' }}</td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4">Perusahaan</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">{{ $stockOpnameSession->company?->name ?? '-' }}</td>
                    </tr>
                </table>

                <table class="text-gray-900 dark:text-white w-full">
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4 w-40">Tanggal Mulai</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">{{ $stockOpnameSession->start_date?->format('d M Y') ?? '-' }}</td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4">Tanggal Selesai</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">{{ $stockOpnameSession->end_date?->format('d M Y') ?? 'Sedang Berjalan' }}</td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4">Dibuat Oleh</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">{{ $stockOpnameSession->createdBy?->name ?? '-' }}</td>
                    </tr>
                    <tr class="border-b dark:border-gray-700">
                        <td class="font-medium py-2 pr-4">Tanggal Dibuat</td>
                        <td class="px-2 py-2">:</td>
                        <td class="py-2">{{ $stockOpnameSession->created_at?->format('d M Y, H:i') }}</td>
                    </tr>
                </table>

            </div>
        </div>

        {{-- ── Stats Cards ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            {{-- Total --}}
            <div class="flex items-center gap-4 rounded-xl shadow-md px-5 py-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Aset</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
            </div>

            {{-- Found --}}
            <div class="flex items-center gap-4 rounded-xl shadow-md px-5 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4.7 4.5 9.3-9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wide">Found</p>
                    <p class="text-3xl font-bold text-green-700 dark:text-green-300">{{ number_format($stats['found']) }}</p>
                    @if ($stats['total'] > 0)
                        <p class="text-xs text-green-500 dark:text-green-500 mt-0.5">
                            {{ number_format($stats['found'] / $stats['total'] * 100, 1) }}% dari total
                        </p>
                    @endif
                </div>
            </div>

            {{-- Missing --}}
            <div class="flex items-center gap-4 rounded-xl shadow-md px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/60 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6m0 12L6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wide">Missing</p>
                    <p class="text-3xl font-bold text-red-700 dark:text-red-300">{{ number_format($stats['missing']) }}</p>
                    @if ($stats['total'] > 0)
                        <p class="text-xs text-red-500 dark:text-red-500 mt-0.5">
                            {{ number_format($stats['missing'] / $stats['total'] * 100, 1) }}% dari total
                        </p>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── Asset Detail List ─────────────────────────────────────────── --}}
        <div class="relative shadow-md py-5 px-6 rounded-lg bg-white dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b border-gray-300 dark:border-gray-700 pb-2 mb-4">
                Daftar Aset
            </h2>

            <div class="overflow-x-auto">
                <table id="soDetailTable"
                    class="w-full text-sm text-left text-gray-500 dark:text-gray-200"
                    data-url="{{ route('api.stock-opname.details', $stockOpnameSession->id) }}">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-white">
                        <tr>
                            <th scope="col" class="px-4 py-3">No</th>
                            <th scope="col" class="px-4 py-3">Asset Number</th>
                            <th scope="col" class="px-4 py-3">Asset Name</th>
                            <th scope="col" class="px-4 py-3">Description</th>
                            <th scope="col" class="px-4 py-3">Lokasi Sistem</th>
                            <th scope="col" class="px-4 py-3">Lokasi Aktual</th>
                            <th scope="col" class="px-4 py-3">Kondisi Sistem</th>
                            <th scope="col" class="px-4 py-3">Kondisi Aktual</th>
                            <th scope="col" class="px-4 py-3">User Sistem</th>
                            <th scope="col" class="px-4 py-3">User Aktual</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3">Waktu Scan</th>
                        </tr>
                        <tr id="filter-row">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        {{-- ── Back Button ───────────────────────────────────────────────── --}}
        <div class="flex gap-2">
            <a href="{{ route('stock-opname.index') }}"
                class="text-gray-900 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                &larr; Back
            </a>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        window.assetNamesData = @json($assetNames);
    </script>
    @vite('resources/js/pages/stockOpnameShow.js')
@endpush