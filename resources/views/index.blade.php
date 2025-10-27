@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            .apexcharts-canvas .apexcharts-legend-text {
                font-size: 12px !important;
                color: #4B5563 !important;
            }

            .dark .apexcharts-canvas .apexcharts-legend-text {
                color: #E5E7EB !important; /* Warna abu-abu terang */
            }

            .apexcharts-canvas .apexcharts-tooltip .apexcharts-tooltip-text-y-value, .apexcharts-canvas .apexcharts-tooltip .apexcharts-tooltip-text-y-label{
                font-size: 14px !important;
                font-family: inherit !important;
                color: #111827 !important;
            }
            .dark .apexcharts-tooltip.apexcharts-theme-dark {
                background: #1F2937 !important; /* Warna abu-abu sangat gelap (gray-800) */
                border-color: #4B5563 !important; /* Warna border abu-abu (gray-600) */
                color: #E5E7EB !important;
            }
        </style>
    @endpush
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <svg class="w-3 h-3 me-2.5 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Dashboard</span>
                </li>
            </ol>
        </nav>
    </div>
    
    <div class="p-5">
        <div class="w-full mb-5 bg-white p-4 rounded-lg shadow-md dark:bg-gray-800 dark:text-white">
            <h1 class="text-xl font-bold">Hello {{ Auth::user()->name }}</h1>
            <p class="text-md">Welcome To {{ Auth::user()->lastActiveCompany->name }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Card untuk Chart Asset by Location --}}
            <div class="w-full bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 md:p-6">
                <div class="flex justify-between items-start w-full mb-4">
                    <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Asset by Location</h5>
                </div>
                <div class="py-6" id="assetbyloc-chart"></div>
            </div>

            {{-- Card untuk Chart Asset by Class --}}
            <div class="w-full bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 md:p-6">
                <div class="flex justify-between items-start w-full mb-4">
                    <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Asset by Class</h5>
                </div>
                <div class="py-6" id="assetbyclass-chart"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
            <div class="flex items-center w-full bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 md:p-6">
                <div class="me-4">
                    <svg class="w-10 h-10 text-black bg-gray-200 rounded-lg p-1 dark:text-white dark:bg-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v3c0 .5523.44772 1 1 1h10M3 15v-4m0 4h9m-9-4V6c0-.55228.44772-1 1-1h16c.5523 0 1 .44772 1 1v3M3 11h11m-2-.2079V19m3-4h1.9909M21 15c0 1.1046-.8954 2-2 2s-2-.8954-2-2 .8954-2 2-2 2 .8954 2 2Z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-lg dark:text-white">
                        Asset Arrival
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $assetArrival }} assets
                    </div>
                </div>
            </div>

            <div class="flex items-center w-full bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 md:p-6">
                <div class="me-4">
                    <svg class="w-10 h-10 text-black bg-gray-200 rounded-lg p-1 dark:text-white dark:bg-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v3c0 .5523.44772 1 1 1h16c.5523 0 1-.4477 1-1v-3M3 15V6c0-.55228.44772-1 1-1h16c.5523 0 1 .44772 1 1v9M3 15h18M8 15v4m4-4v4m4-4v4m-7-9h1.9909M15 10c0 1.1046-.8954 2-2 2s-2-.8954-2-2c0-1.10457.8954-2 2-2s2 .89543 2 2Z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-lg dark:text-white">
                        Fixed Asset
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $assetFixed }} assets
                    </div>
                </div>
            </div>

            <div class="flex items-center w-full bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 md:p-6">
                <div class="me-4">
                    <svg class="w-10 h-10 text-black bg-gray-200 rounded-lg p-1 dark:text-white dark:bg-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15v3c0 .5523.44772 1 1 1h16c.5523 0 1-.4477 1-1v-3M3 15V6c0-.55228.44772-1 1-1h16c.5523 0 1 .44772 1 1v9M3 15h18M8 15v4m4-4v4m4-4v4m-7-9h1.9909M15 10c0 1.1046-.8954 2-2 2s-2-.8954-2-2c0-1.10457.8954-2 2-2s2 .89543 2 2Z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-bold text-lg dark:text-white">
                        Low Value Asset
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $assetLVA }} assets
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 mt-5">
            <div class="w-full bg-white rounded-lg shadow-md dark:bg-gray-800 p-4 md:p-6">

                <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700 overflow-y-auto h-9/10">
                    <div class="flex items-center justify-between mb-4">
                        <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Asset Remark</h5>
                        <!-- <a href="#" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">
                            View all
                        </a> -->
                        <p class="text-sm font-medium">Total : <span class="text-sm font-medium text-blue-600 dark:text-blue-500">{{ $assetRemaksCount }}</span></p>
                    </div>
                    <div class="flow-root">
                        <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($assetRemaks as $assetRemaks)
                                <li class="py-3 sm:py-4">
                                    <div class="flex items-center">
                                        <!-- <div class="shrink-0">
                                            <img class="w-8 h-8 rounded-full" src="" alt="">
                                        </div> -->
                                        <div class="flex-1 min-w-0 ms-4">
                                            <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                {{ $assetRemaks->asset_number }}
                                            </p>
                                            <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                                                {{ $assetRemaks->remaks }}
                                            </p>
                                        </div>
                                        <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $assetRemaks->asset_type }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const assetLocData = @json($assetLocData);
        const assetClassData = @json($assetClassData);

        // Opsi minimal untuk Chart Lokasi
        const minimalLocationOptions = {
            series: assetLocData.series.map(Number),
            labels: assetLocData.labels,
            chart: {
                type: 'pie',
                height: 420
            },
            legend: {
                position: 'bottom'
            }
        };

        // Opsi minimal untuk Chart Kelas
        const minimalClassOptions = {
            series: assetClassData.series.map(Number),
            labels: assetClassData.labels,
            chart: {
                type: 'pie',
                height: 420
            },
            legend: {
                position: 'bottom'
            }
        };

        // Render Chart Lokasi
        if (document.getElementById("assetbyloc-chart") && typeof ApexCharts !== 'undefined') {
            if (assetLocData.series && assetLocData.series.length > 0) {
                const locChart = new ApexCharts(document.getElementById("assetbyloc-chart"), minimalLocationOptions);
                locChart.render();
            } else {
                document.getElementById("assetbyloc-chart").innerHTML = '<div class="text-center text-gray-500 py-10">No location data to display.</div>';
            }
        }

        // Render Chart Kelas
        if (document.getElementById("assetbyclass-chart") && typeof ApexCharts !== 'undefined') {
            if (assetClassData.series && assetClassData.series.length > 0) {
                const classChart = new ApexCharts(document.getElementById("assetbyclass-chart"), minimalClassOptions);
                classChart.render();
            } else {
                document.getElementById("assetbyclass-chart").innerHTML = '<div class="text-center text-gray-500 py-10">No class data to display.</div>';
            }
        }
    });
</script>
@endpush