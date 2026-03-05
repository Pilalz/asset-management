@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Matiin Outline */
            #companyTable thead tr th:hover {
                outline: none !important;
            }

            /* Menghapus background bawaan dari kolom yang diurutkan */
            table.dataTable tbody tr>.sorting_1,
            table.dataTable tbody tr>.sorting_2,
            table.dataTable tbody tr>.sorting_3 {
                background-color: inherit !important;
            }

            .dark .dt-search,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:hover,
            html.dark .dt-container .dt-paging .dt-paging-button.disabled:active,
            .dark div.dt-container .dt-paging .dt-paging-button,
            .dark div.dt-container .dt-paging .ellipsis {
                color: #e4e6eb !important;
            }

            html.dark .dt-container .dt-paging .dt-paging-button.current:hover {
                color: white !important;
            }

            div.dt-container select.dt-input {
                padding: 4px 25px 4px 4px;
            }

            select.dt-input option {
                text-align: center !important;
            }
        </style>
    @endpush
    <div class="bg-white flex p-5 text-lg justify-between border-b border-slate-200 dark:border-gray-700 dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('company.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:hover:text-indigo-400 dark:text-gray-400">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        Company
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">List</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex gap-2">
            @can('is-dev')
                <div class="hidden sm:block">
                    <a href="{{ route('company.create') }}" type="button"
                        class="inline-flex items-center text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800 transition-colors shadow-sm">
                        <span class="sr-only">New Data</span>
                        New Data
                        <svg class="w-4 h-4 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 12h14m-7 7V5" />
                        </svg>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="shadow-sm rounded-xl bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 overflow-hidden">
            <table id="companyTable" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-100">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-white">
                    <tr>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">No</span></th>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">Name</span></th>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">Role</span></th>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">Kode</span></th>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">Currency</span></th>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">Address</span></th>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">Phone</span></th>
                        <th scope="col" class="px-6 py-3"><span class="flex items-center">Fax</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->iteration }}</th>
                            <td scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                                <img class="w-10 h-10 rounded-full" src="{{ Storage::url($company->logo) }}"
                                    alt="{{ $company->name }}">
                                <div class="ps-3">
                                    <div class="text-base font-semibold">{{ $company->name }}</div>
                                    <div class="font-normal text-gray-500">{{ $company->alias }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $company->role }}</td>
                            <td class="px-6 py-4">{{ $company->code }}</td>
                            <td class="px-6 py-4">{{ $company->currency }}</td>
                            <td class="px-6 py-4">{{ $company->address ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $company->phone ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $company->fax ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-3">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/company.js')
@endpush