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
            <a href="{{ route('fiscal.export', ['year' => $selectedYear]) }}" type="button" class="inline-flex items-center text-blue-500 bg-white border border-blue-300 focus:outline-none hover:bg-blue-200 focus:ring-0 font-medium rounded-md text-sm px-3 py-1.5 text-center me-2 dark:bg-blue-600 dark:text-blue-200 dark:border-blue-400 dark:hover:bg-blue-500 dark:hover:border-blue-400">
                <svg class="w-4 h-4 me-2 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10V4a1 1 0 0 0-1-1H9.914a1 1 0 0 0-.707.293L5.293 7.207A1 1 0 0 0 5 7.914V20a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2M10 3v4a1 1 0 0 1-1 1H5m5 6h9m0 0-2-2m2 2-2 2"/>
                </svg>
                Export Excel
            </a>
        </div>
    @endcan
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
        <div class="relative shadow-md rounded-lg bg-white p-4 dark:bg-gray-800">
            {{-- Form untuk filter tahun jika perlu --}}
            <div class="mb-4 flex flex-row content-center">
                <form method="GET" action="{{ route('depreciationFiscal.index') }}">
                    <label for="year" class="dark:text-gray-50">Tampilkan Tahun:</label>
                    <select name="year" id="year" onchange="this.form.submit()" class="py-2 px-0 w-24 text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                        @for ($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
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
                        @foreach ($pivotedData as $assetId => $data)
                            <tr class="group">
                                <td class="sticky left-0 bg-gray-50 dark:bg-gray-700 text-center border border-gray-100 group-hover:bg-gray-200 dark:group-hover:bg-gray-500">{{ $loop->iteration + (($pivotedData->currentPage() - 1) * $pivotedData->perPage()) }}</td>
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
                    </tbody>
                </table>
            </div>

            <div class="mt-4 py-4">
                {{ $pivotedData->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- @vite('resources/js/pages/commercialDepre.js') -->
    @vite('resources/js/pages/alert.js')
@endpush