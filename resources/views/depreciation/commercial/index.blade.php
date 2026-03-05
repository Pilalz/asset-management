@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
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
    <div class="bg-white flex p-5 text-lg justify-between items-center border-b border-slate-200 dark:border-gray-700 dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center text-sm font-medium text-gray-700 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Depreciation
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <a href="{{ route('depreciation.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Commercial</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">List</span>
                    </div>
                </li>
            </ol>
        </nav>

    @can('is-admin')
        <div class="flex">
            <a href="{{ route('commercial.export', ['start' => $selectedStartYear, 'end' => $selectedEndYear]) }}" type="button" 
                class="inline-flex items-center justify-center text-indigo-700 bg-indigo-50 border border-indigo-200 focus:outline-none hover:bg-indigo-100 hover:text-indigo-800 focus:ring-4 focus:ring-indigo-100 font-medium rounded-lg text-sm px-4 py-2 text-center my-4 md:my-0 shadow-sm transition-colors dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800 dark:hover:bg-indigo-800 dark:hover:text-white dark:focus:ring-indigo-900">
                <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2"/>
                </svg>
                Export Excel
            </a>
        </div>
    @endcan
    </div>

    <x-alerts />

    <div class="p-5">
        {{-- Form untuk filter tahun --}}
        <div class="mb-5 flex flex-col sm:flex-row flex-wrap gap-4">
            <form method="GET" action="{{ route('depreciation.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 w-full md:w-auto">
                <div class="flex items-center gap-3">
                    <label for="start" class="text-xs font-semibold uppercase text-slate-500 tracking-wider dark:text-gray-400">Periode:</label>
                    <div class="inline-flex items-center border border-slate-300 dark:border-gray-600 rounded-lg px-2 bg-slate-50 dark:bg-gray-700 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-shadow">
                        <select name="start" id="start" class="bg-transparent border-none text-sm focus:ring-0 py-2 dark:text-white font-medium cursor-pointer">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $selectedStartYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <span class="text-slate-400 px-2 text-sm dark:text-gray-500">-</span>
                        <select name="end" id="end" class="bg-transparent border-none text-sm focus:ring-0 py-2 dark:text-white font-medium cursor-pointer">
                            @for ($y = now()->year; $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ $selectedEndYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 w-full sm:w-auto justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Terapkan Filter
                </button>
            </form>
            <form method="GET" action="{{ route('depreciation.index', ['start' => now()->year, 'end' => now()->year]) }}" class="flex items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200 dark:bg-gray-800 dark:border-gray-700 w-full md:w-auto">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-sm font-medium rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-offset-gray-800 w-full justify-center">
                    <svg class="w-4 h-4 mr-2 text-slate-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Tahun Ini
                </button>
            </form>
        </div>

        <div class="relative shadow-sm rounded-xl bg-white border border-slate-200 dark:border-gray-700 dark:bg-gray-800 mb-6 overflow-hidden">
            <div class="overflow-x-auto relative">
                <table id="depreciationTable" class="w-full text-sm text-left rtl:text-right text-slate-600 dark:text-gray-300">
                    <thead class="text-xs text-slate-500 dark:text-gray-400 bg-slate-50 dark:bg-gray-700/50 uppercase font-semibold tracking-wider">
                        <tr>
                            <th rowspan="2" class="px-3 py-4 border-b border-r border-slate-200 bg-slate-50 dark:bg-gray-700/80 dark:border-gray-600 sticky left-0 z-10 w-12 text-center shadow-[1px_0_4px_rgba(0,0,0,0.05)]">No</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 dark:border-gray-600 border-r-2 bg-slate-50 dark:bg-gray-700/80 sticky left-[43px] z-10 whitespace-nowrap shadow-[2px_0_5px_rgba(0,0,0,0.05)]">Asset Number</th>
                            <th rowspan="2" class="px-5 py-4 border-b border-slate-200 dark:border-gray-600 whitespace-nowrap">Asset Name</th>

                            @foreach($months as $monthName)
                                @php
                                    $bgColorClass = $loop->iteration % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-slate-50/50 dark:bg-gray-700/30';
                                @endphp
                                <th colspan="3" class="text-center px-4 py-3 border-b border-slate-200 dark:border-gray-600 {{ $bgColorClass }}">{{ $monthName }}</th>
                            @endforeach
                        </tr>                    
                        <tr>
                            @foreach($months as $monthName)
                                @php
                                    $bgColorClass = $loop->iteration % 2 === 0 ? 'bg-white dark:bg-gray-800' : 'bg-slate-50/50 dark:bg-gray-700/30';
                                @endphp
                                <th class="px-3 py-3 border-b border-slate-200 dark:border-gray-600 font-medium whitespace-nowrap {{ $bgColorClass }}">Monthly Depre</th>
                                <th class="px-3 py-3 border-b border-slate-200 dark:border-gray-600 font-medium whitespace-nowrap {{ $bgColorClass }}">Accum Depre</th>
                                <th class="px-3 py-3 border-b border-slate-200 dark:border-gray-600 font-medium whitespace-nowrap {{ $bgColorClass }}">Book Value</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if ($pivotedData == null)
                            <tr>
                                <td colspan="12" class="text-center py-8 text-slate-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <span class="text-sm">No data available</span>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($pivotedData as $assetId => $data)
                                <tr class="group hover:bg-indigo-50/50 dark:hover:bg-gray-700/50 transition-colors bg-white dark:bg-gray-800 border-b border-slate-100 dark:border-gray-700 last:border-0">
                                    <td class="sticky left-0 bg-white dark:bg-gray-800 group-hover:bg-indigo-50 dark:group-hover:bg-gray-700 text-center px-3 py-3 border-r border-slate-100 dark:border-gray-700 z-10 shadow-[1px_0_4px_rgba(0,0,0,0.02)]">{{ $loop->iteration + (($paginator->currentPage() - 1) * $paginator->perPage()) }}</td>
                                    <td class="sticky left-[43px] px-5 py-3 bg-white dark:bg-gray-800 group-hover:bg-indigo-50 dark:group-hover:bg-gray-700 border-r-2 border-slate-100 dark:border-gray-700 z-10 whitespace-nowrap font-medium text-slate-900 dark:text-gray-100 shadow-[2px_0_5px_rgba(0,0,0,0.02)]">{{ $data['master_data']->asset_number ?? 'N/A' }}</td>
                                    <td class="px-5 py-3 whitespace-nowrap border-r border-slate-50/50 dark:border-gray-700/50">{{ $data['master_data']->assetName->name ?? 'N/A' }}</td>

                                    @foreach ($months as $monthKey => $monthName)
                                        @php
                                            $bgColorClass = $loop->iteration % 2 === 0 ? 'bg-transparent' : 'bg-slate-50/30 dark:bg-gray-800/50';
                                        @endphp

                                        @if (isset($data['schedule'][$monthKey]))
                                            @php $schedule = $data['schedule'][$monthKey]; @endphp
                                            <td class="px-3 py-3 text-right whitespace-nowrap border-x border-slate-50/50 dark:border-gray-700/50 {{ $bgColorClass }}">{{ format_currency($schedule->monthly_depre) }}</td>
                                            <td class="px-3 py-3 text-right whitespace-nowrap border-x border-slate-50/50 dark:border-gray-700/50 {{ $bgColorClass }}">{{ format_currency($schedule->accumulated_depre) }}</td>
                                            <td class="px-3 py-3 text-right whitespace-nowrap border-x border-slate-50/50 dark:border-gray-700/50 font-medium text-slate-700 dark:text-gray-300 {{ $bgColorClass }}">{{ format_currency($schedule->book_value) }}</td>
                                        @else
                                            <td class="px-3 py-3 text-center border-x border-slate-50/50 dark:border-gray-700/50 text-slate-400 {{ $bgColorClass }}">-</td>
                                            <td class="px-3 py-3 text-center border-x border-slate-50/50 dark:border-gray-700/50 text-slate-400 {{ $bgColorClass }}">-</td>
                                            <td class="px-3 py-3 text-center border-x border-slate-50/50 dark:border-gray-700/50 text-slate-400 {{ $bgColorClass }}">-</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($pivotedData != null && $paginator->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800/80">
                    {{ $paginator->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection