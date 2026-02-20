@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            .apexcharts-canvas .apexcharts-legend-text {
                font-family: 'Inter', sans-serif !important;
                font-size: 12px !important;
                color: #64748b !important;
                /* slate-500 */
            }

            .dark .apexcharts-canvas .apexcharts-legend-text {
                color: #cbd5e1 !important;
                /* slate-300 */
            }

            .apexcharts-canvas .apexcharts-tooltip {
                background: #fff;
                border: 1px solid #e2e8f0;
                box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
                border-radius: 0.5rem;
            }

            .apexcharts-canvas .apexcharts-tooltip .apexcharts-tooltip-title {
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                font-family: 'Inter', sans-serif !important;
            }

            .apexcharts-canvas .apexcharts-tooltip .apexcharts-tooltip-text-y-value,
            .apexcharts-canvas .apexcharts-tooltip .apexcharts-tooltip-text-y-label {
                font-size: 14px !important;
                font-family: 'Inter', sans-serif !important;
                color: #1e293b !important;
                /* slate-800 */
            }

            .dark .apexcharts-tooltip.apexcharts-theme-dark {
                background: #1e293b !important;
                border-color: #334155 !important;
                color: #f1f5f9 !important;
            }

            .dark .apexcharts-canvas .apexcharts-tooltip .apexcharts-tooltip-title {
                background: #0f172a;
                border-bottom: 1px solid #334155;
            }
        </style>
    @endpush

    <x-alerts />

    <div class="px-5 pb-5 space-y-5 pt-5">
        {{-- Welcome Banner --}}
        <div
            class="w-full bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl shadow-sm relative overflow-hidden">
            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="z-10">
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mb-2">
                            Welcome back, <span
                                class="text-indigo-600 dark:text-indigo-400">{{ Auth::user()->name }}</span>!
                        </h1>
                        <p class="text-slate-600 dark:text-slate-300">
                            Here's what's happening with your assets.
                        </p>
                    </div>
                    <div class="hidden md:block z-10">
                        <span class="inline-flex items-center justify-center p-3 bg-indigo-50 text-indigo-600 rounded-full dark:bg-indigo-900/30 dark:text-indigo-300">
                            <img src="{{ Storage::url($activeCompany->logo) }}" alt="{{ $activeCompany->name }}" class="h-14 w-auto">
                        </span>
                    </div>
                </div>
            </div>

            {{-- Decorative circles --}}
            <div
                class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 rounded-full bg-indigo-50 dark:bg-indigo-900/20 blur-xl">
            </div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 rounded-full bg-blue-50 dark:bg-blue-900/20 blur-xl">
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Assets --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5 transition-transform hover:-translate-y-1 duration-300">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 p-3 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Assets</p>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-1">{{ $totalAsset }}</h3>
                    </div>
                </div>
            </div>

            {{-- Asset Arrival --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5 transition-transform hover:-translate-y-1 duration-300">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 p-3 rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Asset Arrival</p>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-1">{{ $assetArrival }}</h3>
                    </div>
                </div>
            </div>

            {{-- Fixed Asset (FA) --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5 transition-transform hover:-translate-y-1 duration-300">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 p-3 rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Fixed Asset (FA)</p>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-1">{{ $assetFixed }}</h3>
                    </div>
                </div>
            </div>

            {{-- Low Value Asset (LVA) --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5 transition-transform hover:-translate-y-1 duration-300">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 p-3 rounded-lg bg-teal-50 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Low Value Asset (LVA)</p>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-1">{{ $assetLVA }}</h3>
                    </div>
                </div>
            </div>

            {{-- Total Value --}}
            <!-- <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5 transition-transform hover:-translate-y-1 duration-300">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 p-3 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Value</p>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white mt-1">{{ $totalAssetPrice > 0 ? format_currency($totalAssetPrice) : 'Rp 0' }}</h3>
                    </div>
                </div>
            </div> -->
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Category Chart --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5 lg:col-span-2">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Assets by Category
                </h2>
                <div id="chart"></div>
            </div>

            {{-- Department Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Assets by Department
                </h2>
                <div id="chart3"></div>
            </div>

            {{-- Location Chart --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5">
                <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Assets by Location
                </h2>
                <div id="chart2"></div>
            </div>
        </div>

        {{-- Asset Remark --}}
        <div class="grid grid-cols-1 gap-5">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5">
                {{-- Header --}}
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Asset Remark
                    </h5>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                        {{ $assetRemaksCount }} items
                    </span>
                </div>

                {{-- List --}}
                <div class="overflow-y-auto max-h-72 pr-1">
                    @forelse($assetRemaks as $remark)
                        <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-slate-100 dark:border-gray-700' : '' }}">
                                    {{-- Type Badge --}}
                                    <span class="shrink-0 mt-0.5 inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                                        {{ $remark->asset_type === 'FA'
                        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
                        : 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300' }}">
                                        {{ $remark->asset_type }}
                                    </span>
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">
                                            {{ $remark->asset_number }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                            {{ $remark->remaks }}
                                        </p>
                                    </div>
                                </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-slate-500">
                            <svg class="w-10 h-10 mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm">No remarks found</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- Depreciation Chart --}}
        <div class="grid grid-cols-1 gap-5 mt-5">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-5">

                {{-- Header --}}
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h5 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            Depreciation Trend
                        </h5>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Commercial vs Fiscal monthly
                            depreciation</p>
                    </div>
                </div>

                {{-- Mini Summary Totals --}}
                <div class="grid grid-cols-3 gap-4 mb-5">
                    <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                        <p class="text-xs font-medium text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">Total Commercial</p>
                        <p class="text-lg font-bold text-indigo-700 dark:text-indigo-300 mt-1" id="totalCommercial">—</p>
                    </div>
                    <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-4">
                        <p class="text-xs font-medium text-teal-600 dark:text-teal-400 uppercase tracking-wide">Total Fiscal</p>
                        <p class="text-lg font-bold text-teal-700 dark:text-teal-300 mt-1" id="totalFiscal">—</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4">
                        <p class="text-xs font-medium text-amber-600 dark:text-amber-400 uppercase tracking-wide">Assets This Month</p>
                        <p class="text-lg font-bold text-amber-700 dark:text-amber-300 mt-1">{{ $currentMonthDepreCount }} assets</p>
                    </div>
                </div>

                <div id="line-chart"></div>
            </div>
        </div>

    </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            const isDark = document.documentElement.classList.contains('dark');
            const chartFont = 'Inter, sans-serif';

            // Chart 1: Assets by Category
            new ApexCharts(document.querySelector("#chart"), {
                chart: { type: 'donut', height: 380, fontFamily: chartFont, toolbar: { show: false }, background: 'transparent' },
                theme: { mode: isDark ? 'dark' : 'light' },
                series: @json($assetClassData['series']),
                labels: @json($assetClassData['labels']),
                colors: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4'],
                legend: { position: 'left', fontFamily: chartFont, fontSize: '13px', offsetY: 0, itemMargin: { vertical: 4 } },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: { show: true, showAlways: true, label: 'Total', fontSize: '16px', fontFamily: chartFont, fontWeight: 600, color: '#64748b' }
                            }
                        }
                    }
                },
                tooltip: { style: { fontFamily: chartFont } }
            }).render();

            // Chart 2: Assets by Location
            new ApexCharts(document.querySelector("#chart2"), {
                chart: { type: 'pie', height: 350, fontFamily: chartFont, toolbar: { show: false }, background: 'transparent' },
                theme: { mode: isDark ? 'dark' : 'light' },
                series: @json($assetLocData['series']),
                labels: @json($assetLocData['labels']),
                colors: ['#3b82f6', '#0ea5e9', '#06b6d4', '#14b8a6', '#10b981'],
                legend: { position: 'bottom', fontFamily: chartFont, fontSize: '12px' },
                tooltip: { style: { fontFamily: chartFont } }
            }).render();

            // Chart 3: Assets by Department (Bar)
            new ApexCharts(document.querySelector("#chart3"), {
                chart: { type: 'pie', height: 350, fontFamily: chartFont, toolbar: { show: false }, background: 'transparent' },
                theme: { mode: isDark ? 'dark' : 'light' },
                series: @json($assetDeptData['series']),
                labels: @json($assetDeptData['labels']),
                colors: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6'],
                legend: { position: 'bottom', fontFamily: chartFont, fontSize: '12px' },
                tooltip: { style: { fontFamily: chartFont } }
            }).render();

            //Depre Chart Data
            const chartLabels = @json($chartLabels);
            const commercialSumData = @json($commercialSumData);
            const fiscalSumData = @json($fiscalSumData);
            const commercialCountData = @json($commercialCountData);
            const fiscalCountData = @json($fiscalCountData);

            // Depreciation Area Chart
            const commercialTotal = commercialSumData.reduce((a, b) => a + Number(b), 0);
            const fiscalTotal = fiscalSumData.reduce((a, b) => a + Number(b), 0);
            const formatCurrency = (v) => {
                if (v >= 1000000000) return 'Rp ' + (v / 1000000000).toFixed(1) + 'B';
                if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(1) + 'M';
                if (v >= 1000) return 'Rp ' + (v / 1000).toFixed(1) + 'K';
                return 'Rp ' + v;
            };
            document.getElementById('totalCommercial').textContent = formatCurrency(commercialTotal);
            document.getElementById('totalFiscal').textContent = formatCurrency(fiscalTotal);

            if (document.getElementById('line-chart') && typeof ApexCharts !== 'undefined') {
                const depreChart = new ApexCharts(document.getElementById('line-chart'), {
                    chart: {
                        type: 'line',
                        height: 340,
                        fontFamily: chartFont,
                        toolbar: { show: false },
                        background: 'transparent',
                        zoom: { enabled: false }
                    },
                    theme: { mode: isDark ? 'dark' : 'light' },
                    series: [
                        {
                            name: 'Commercial Depre',
                            type: 'area',
                            data: commercialSumData.map(Number),
                            yAxisIndex: 0
                        },
                        {
                            name: 'Fiscal Depre',
                            type: 'area',
                            data: fiscalSumData.map(Number),
                            yAxisIndex: 0
                        },
                        {
                            name: 'Asset Count',
                            type: 'line',
                            data: commercialCountData.map(Number),
                            yAxisIndex: 1
                        }
                    ],
                    colors: ['#6366f1', '#14b8a6', '#f59e0b'],
                    fill: {
                        type: ['gradient', 'gradient', 'solid'],
                        gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02, stops: [0, 95, 100] }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [2.5, 2.5, 2.5],
                        dashArray: [0, 0, 5]
                    },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: chartLabels,
                        labels: { style: { fontSize: '11px', fontFamily: chartFont }, rotate: -30 },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: [
                        {
                            seriesName: 'Commercial Depre',
                            title: { text: 'Depreciation (Rp)', style: { fontSize: '11px', fontFamily: chartFont, color: '#6366f1' } },
                            labels: { style: { fontSize: '11px', fontFamily: chartFont }, formatter: formatCurrency }
                        },
                        {
                            seriesName: 'Fiscal Depre',
                            show: false
                        },
                        {
                            seriesName: 'Asset Count',
                            opposite: true,
                            title: { text: 'Asset Count', style: { fontSize: '11px', fontFamily: chartFont, color: '#f59e0b' } },
                            labels: { style: { fontSize: '11px', fontFamily: chartFont }, formatter: (v) => Math.round(v) }
                        }
                    ],
                    grid: {
                        strokeDashArray: 4,
                        borderColor: isDark ? '#374151' : '#f1f5f9',
                        padding: { left: 8, right: 8, top: -10 }
                    },
                    legend: { position: 'top', horizontalAlign: 'right', fontFamily: chartFont, fontSize: '12px' },
                    tooltip: {
                        shared: true,
                        followCursor: true,
                        style: { fontFamily: chartFont },
                        y: [
                            { formatter: formatCurrency },
                            { formatter: formatCurrency },
                            { formatter: (v) => Math.round(v) + ' assets' }
                        ]
                    },
                    markers: { size: [0, 0, 4], strokeWidth: 0, hover: { size: 6 } }
                });
                depreChart.render();
            }
        </script>

    @endpush
@endsection