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

    </div>
@endsection

<!-- @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (typeof ApexCharts === 'undefined') {
            console.error("Kesalahan Kritis: ApexCharts tidak ditemukan. Pastikan sudah diimpor dan didaftarkan di app.js, dan npm run dev berjalan.");
            return;
        }

        const assetLocData = @json($assetLocData);
        const assetClassData = @json($assetClassData);

        const getLocationChartOptions = () => {
            return {
                series: assetLocData.series.map(Number),
                labels: assetLocData.labels,
                colors: ["#1C64F2", "#16BDCA", "#9061F9", "#FDBA8C", "#E74694"],
                chart: { 
                    type: "pie", 
                    height: 420, 
                    width: "100%" 
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (value, { seriesIndex, w }) {
                        const count = w.globals.series[seriesIndex];
                        return `${count} (${value.toFixed(1)}%)`;
                    },
                },
                legend: {
                    position: "bottom",
                    fontFamily: "Inter, sans-serif",
                },
            }
        };
        
        const getClassChartOptions = () => {
            return {
                series: assetClassData.series.map(Number),
                labels: assetClassData.labels,
                colors: ["#1C64F2", "#16BDCA", "#9061F9", "#FDBA8C", "#E74694"],
                chart: { 
                    type: "pie", 
                    height: 420, 
                    width: "100%" 
                },
                legend: {
                    position: "bottom",
                    fontFamily: "Inter, sans-serif",
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (value, { seriesIndex, w }) {
                        const count = w.globals.series[seriesIndex];
                        return `${count} (${value.toFixed(1)}%)`;
                    },
                },
            }
        };

        const locChartDiv = document.getElementById("assetbyloc-chart");
        if (locChartDiv) {
            if (assetLocData && assetLocData.series.length > 0) {
                const locChart = new ApexCharts(locChartDiv, getLocationChartOptions());
                locChart.render();
            } else {
                locChartDiv.innerHTML = '<div class="text-center text-gray-500 py-10">Tidak ada data lokasi untuk ditampilkan.</div>';
            }
        }

        // Render Chart Kelas
        const classChartDiv = document.getElementById("assetbyclass-chart");
        if (classChartDiv) {
            if (assetClassData && assetClassData.series.length > 0) {
                const classChart = new ApexCharts(classChartDiv, getClassChartOptions());
                classChart.render();
            } else {
                classChartDiv.innerHTML = '<div class="text-center text-gray-500 py-10">Tidak ada data kelas aset untuk ditampilkan.</div>';
            }
        }
    });
</script>
@endpush -->