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
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
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
                        <a href="{{ route('depreciationFiscal.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Fiscal</a>
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
            <a href="{{ route('fiscal.export', ['start' => $selectedStartYear, 'end' => $selectedEndYear]) }}" type="button" class="inline-flex items-center text-blue-500 bg-white border border-blue-300 focus:outline-none hover:bg-blue-200 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 text-center me-2 dark:bg-blue-600 dark:text-blue-200 dark:border-blue-400 dark:hover:bg-blue-500 dark:hover:border-blue-400">
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
        <div class="relative shadow-md rounded-lg bg-white p-4 dark:bg-gray-800">
            {{-- Form untuk filter tahun jika perlu --}}
            <div class="mb-4 flex flex-row content-center gap-4">
                <form method="GET" action="{{ route('depreciationFiscal.index') }}" class="flex items-center gap-4 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center gap-2">
                        <label for="start" class="text-xs font-semibold uppercase text-gray-400 track-wider">Periode:</label>
                        <div class="inline-flex items-center border rounded-md px-2 bg-gray-50">
                            <select name="start" id="start" class="bg-transparent border-none text-sm focus:ring-0 py-2">
                                @for ($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $selectedStartYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <span class="text-gray-400 px-2">s/d</span>
                            <select name="end" id="end" class="bg-transparent border-none text-sm focus:ring-0 py-2">
                                @for ($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $selectedEndYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Terapkan Filter
                    </button>
                </form>
                <form method="GET" action="{{ route('depreciationFiscal.index', ['start' => now()->year, 'end' => now()->year]) }}" class="flex items-center gap-4 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        Tahun Ini
                    </button>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table id="depreciationTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-50">
                    <thead class="text-xs text-gray-700 dark:text-gray-50 uppercase">
                        <tr>
                            <th rowspan="2" class="px-2 py-3 border bg-gray-50 dark:bg-gray-700 sticky left-0">No</th>
                            <th rowspan="2" class="px-6 py-3 border bg-gray-50 dark:bg-gray-700 sticky left-9">Asset Name</th>
                            <th rowspan="2" class="px-6 py-3 border bg-gray-50 dark:bg-gray-700">Asset Number</th>
                            
                            @foreach($months as $monthName)
                                @php
                                    $bgColorClass = $loop->iteration % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800';
                                @endphp
                                <th colspan="3" class="text-center px-6 py-3 border {{ $bgColorClass }}">{{ $monthName }}</th>
                            @endforeach
                        </tr>
                        
                        <tr>
                            @foreach($months as $monthName)
                                @php
                                    $bgColorClass = $loop->iteration % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800';
                                @endphp
                                <th class="px-2 py-2 border {{ $bgColorClass }}">Monthly Depre</th>
                                <th class="px-2 py-2 border {{ $bgColorClass }}">Accum Depre</th>
                                <th class="px-2 py-2 border {{ $bgColorClass }}">Book Value</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if ($pivotedData == null)
                            <tr>
                                <td colspan="12" class="text-center py-4">No data available</td>
                            </tr>
                        @else
                            @foreach ($pivotedData as $assetId => $data)
                                if (isset)
                                <tr class="group">
                                    <td class="sticky left-0 bg-gray-50 dark:bg-gray-700 text-center border border-gray-100 group-hover:bg-gray-200 dark:group-hover:bg-gray-500">{{ $loop->iteration + (($paginator->currentPage() - 1) * $paginator->perPage()) }}</td>
                                    <td class="sticky left-9 p-4 bg-gray-50 dark:bg-gray-700 border border-gray-100 group-hover:bg-gray-200 dark:group-hover:bg-gray-500">{{ $data['master_data']->asset_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-100 group-hover:bg-gray-200 dark:group-hover:bg-gray-500">{{ $data['master_data']->assetName->name ?? 'N/A' }}</td>

                                    @foreach ($months as $monthKey => $monthName)
                                        @php
                                            $bgColorClass = $loop->iteration % 2 === 0 ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800';
                                        @endphp

                                        @if (isset($data['schedule'][$monthKey]))
                                            @php $schedule = $data['schedule'][$monthKey]; @endphp
                                            <td class="border border-gray-100 px-2 text-center group-hover:bg-gray-200 dark:group-hover:bg-gray-500 {{ $bgColorClass }}">{{ format_currency($schedule->monthly_depre) }}</td>
                                            <td class="border border-gray-100 px-2 text-center group-hover:bg-gray-200 dark:group-hover:bg-gray-500 {{ $bgColorClass }}">{{ format_currency($schedule->accumulated_depre) }}</td>
                                            <td class="border border-gray-100 px-2 text-center group-hover:bg-gray-200 dark:group-hover:bg-gray-500 {{ $bgColorClass }}">{{ format_currency($schedule->book_value) }}</td>
                                        @else
                                            <td class="border border-gray-100 px-2 text-center group-hover:bg-gray-200 dark:group-hover:bg-gray-500 {{ $bgColorClass }}">-</td>
                                            <td class="border border-gray-100 px-2 text-center group-hover:bg-gray-200 dark:group-hover:bg-gray-500 {{ $bgColorClass }}">-</td>
                                            <td class="border border-gray-100 px-2 text-center group-hover:bg-gray-200 dark:group-hover:bg-gray-500 {{ $bgColorClass }}">-</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-4 py-4">
                {{ $paginator->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection