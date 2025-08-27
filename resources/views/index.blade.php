@extends('layouts.main')

@section('content')
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
    
    <div class="p-5 w-full">
        <div class="w-full relative overflow-x-auto shadow-md sm:rounded-lg bg-white p-4 dark:bg-gray-800 dark:text-white">
            <h1 class="text-lg font-bold">Hello {{ Auth::user()->name }}</h1>
            <p class="text-md">Welcome To {{ Auth::user()->lastActiveCompany->name }}</p>
            <div class="flex gap-2 justify-around mt-2">
                
                <div class="max-w-sm w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
                    <div class="flex justify-between items-start w-full">
                        <div class="flex-col items-center">
                            <div class="flex items-center mb-1">
                                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white me-1">Asset by Location</h5>
                            </div>
                        </div>
                    </div>
                    <div class="py-6" id="assetbyloc-chart"></div>
                    <div class=" grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                    </div>
                </div>

                <div class="max-w-sm w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
                    <div class="flex justify-between items-start w-full">
                        <div class="flex-col items-center">
                            <div class="flex items-center mb-1">
                                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white me-1">Asset by Location</h5>
                            </div>
                        </div>
                    </div>
                    <div class="py-6" id="assetbyclass-chart"></div>
                    <div class=" grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
                    </div>
                </div>                

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const assetLocData = @json($assetLocData);
    const assetClassData = @json($assetClassData);

    const getLocationChartOptions  = () => {
        return {
            series: assetLocData.series.map(Number),
            labels: assetLocData.labels,
            
            colors: ["#1C64F2", "#16BDCA", "#9061F9", "#FDBA8C", "#E74694"],
            chart: {
                height: 420,
                width: "100%",
                type: "pie",
            },
            stroke: {
                colors: ["white"],
                lineCap: "",
            },
            plotOptions: {
                pie: {
                    labels: {
                        show: true,
                    },
                    size: "100%",
                    dataLabels: {
                        offset: -25
                    }
                },
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontFamily: "Inter, sans-serif",
                },
                // Formatter untuk menampilkan angka absolut, bukan persen
                formatter: function (value, { seriesIndex, w }) {
                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    const count = w.globals.series[seriesIndex];
                    const percentage = (count / total * 100).toFixed(1);
                    return `${count} (${percentage}%)`; // Contoh output: "15 (45.5%)"
                },
            },
            legend: {
                position: "bottom",
                fontFamily: "Inter, sans-serif",
            },
        }
    }

    if (document.getElementById("assetbyloc-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("assetbyloc-chart"), getLocationChartOptions ());
        chart.render();
    }

    

    const getClassChartOptions  = () => {
        return {
            series: assetClassData.series.map(Number),
            labels: assetClassData.labels,
            
            colors: ["#1C64F2", "#16BDCA", "#9061F9", "#FDBA8C", "#E74694"],
            chart: {
                height: 420,
                width: "100%",
                type: "pie",
            },
            stroke: {
                colors: ["white"],
                lineCap: "",
            },
            plotOptions: {
                pie: {
                    labels: {
                        show: true,
                    },
                    size: "100%",
                    dataLabels: {
                        offset: -25
                    }
                },
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontFamily: "Inter, sans-serif",
                },
                // Formatter untuk menampilkan angka absolut, bukan persen
                formatter: function (value, { seriesIndex, w }) {
                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                    const count = w.globals.series[seriesIndex];
                    const percentage = (count / total * 100).toFixed(1);
                    return `${count} (${percentage}%)`; // Contoh output: "15 (45.5%)"
                },
            },
            legend: {
                position: "bottom",
                fontFamily: "Inter, sans-serif",
            },
        }
    }

    if (document.getElementById("assetbyclass-chart") && typeof ApexCharts !== 'undefined') {
        const chart = new ApexCharts(document.getElementById("assetbyclass-chart"), getClassChartOptions ());
        chart.render();
    }
</script>
@endpush