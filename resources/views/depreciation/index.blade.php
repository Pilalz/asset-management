@extends('layouts.main')

@section('content')
    <div class="bg-white flex p-5 text-lg justify-between">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('depreciation.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                        Depreciation
                    </a>
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

        <div class="flex">
            <a href="{{ route('depreciation.create') }}" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-sm text-sm px-5 py-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg class="w-4 h-4 me-2 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                </svg>
                New Data
            </a>
        </div>
    </div>
    
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg m-5 bg-white p-4">
        {{-- Form untuk filter tahun jika perlu --}}
        <div class="mb-4 flex flex-row content-center">
            <form method="GET" action="{{ route('depreciation.index') }}">
                <label for="year" class="">Tampilkan Tahun:</label>
                <select name="year" id="year" onchange="this.form.submit()" class="py-2 px-0 w-24 text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                    @for ($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
        

        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                {{-- Baris Header Pertama --}}
                <tr>
                    <th rowspan="2" class="px-2 py-3 border">No</th>
                    <th rowspan="2" class="px-6 py-3 border">Asset Name</th>
                    <th rowspan="2" class="px-6 py-3 border">Asset Number</th>
                    
                    {{-- Loop untuk membuat header bulan --}}
                    @foreach($months as $monthName)
                        <th colspan="3" class="text-center px-6 py-3 border">{{ $monthName }}</th>
                    @endforeach
                </tr>
                {{-- Baris Header Kedua --}}
                <tr>
                    @foreach($months as $monthName)
                        <th class="px-2 py-2 border">Monthly Depre</th>
                        <th class="px-2 py-2 border">Accum Depre</th>
                        <th class="px-2 py-2 border">Book Value</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($pivotedData as $assetId => $data)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-2 py-4 border">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 border">
                            {{ $data['master_data']->assetName->name }}
                        </td>
                        <td class="px-6 py-4 border">{{ $data['master_data']->asset_number }}</td>

                        {{-- Loop untuk mengisi data per bulan --}}
                        @foreach ($months as $monthKey => $monthName)
                            {{-- Cek apakah ada data untuk aset ini di bulan ini --}}
                            @if (isset($data['schedule'][$monthKey]))
                                <td class="px-2 py-4 border text-right">{{ number_format($data['schedule'][$monthKey]->monthly_depre, 0, ',', '.') }}</td>
                                <td class="px-2 py-4 border text-right">{{ number_format($data['schedule'][$monthKey]->accumulated_depre, 0, ',', '.') }}</td>
                                <td class="px-2 py-4 border text-right">{{ number_format($data['schedule'][$monthKey]->book_value, 0, ',', '.') }}</td>
                            @else
                                {{-- Jika tidak ada data, buat sel kosong --}}
                                <td class="px-2 py-4 border"></td>
                                <td class="px-2 py-4 border"></td>
                                <td class="px-2 py-4 border"></td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 3 + (count($months) * 3) }}" class="text-center p-3">Tidak ada data untuk ditampilkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    if (document.getElementById("assetSubClassTable") && typeof simpleDatatables.DataTable !== 'undefined') {
        const dataTable = new simpleDatatables.DataTable("#assetSubClassTable", {
            searchable: true,
            sortable: true,
            tableRender: (_data, table, type) => {
                if (type === "print") {
                    return table
                }

                const tHead = table.childNodes[0];
                const columnHeaders = tHead.childNodes[0].childNodes; // Ini adalah TH dari baris header pertama
                const filterHeaders = {
                    nodeName: "TR",
                    attributes: {
                        class: "search-filtering-row"
                    },
                    childNodes: Array.from(columnHeaders).map(
                        (_th, index) => {
                            // Cek jika ini adalah kolom "Asset Class" (indeks 1)
                            if (index === 1) { // Indeks 1 adalah kolom "Asset Class"
                                return {
                                    nodeName: "TH",
                                    childNodes: [
                                        {
                                            nodeName: "INPUT",
                                            attributes: {
                                                class: "datatable-input",
                                                type: "search",
                                                "data-columns": "[" + index + "]",
                                                placeholder: "Cari Asset Class..." // Tambahkan placeholder
                                            }
                                        }
                                    ]
                                };
                            } else {
                                // Untuk kolom lain, kembalikan TH kosong
                                return { nodeName: "TH", childNodes: [] };
                            }
                        }
                    )
                }
                tHead.childNodes.push(filterHeaders); // Menambahkan baris filter ke thead
                return table;
            }
        });
    }
</script>
@endpush