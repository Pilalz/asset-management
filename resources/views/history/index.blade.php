@extends('layouts.main')

@section('content')
    @push('styles')
        <style>
            /* Gaya untuk Light Mode */
            #historyTable tbody tr:hover {
                background-color: #F9FAFB !important;
                /* Tailwind's hover:bg-gray-50 */
            }

            /* Gaya untuk Dark Mode */
            .dark #historyTable tbody tr:hover {
                background-color: #374151 !important;
                /* Tailwind's dark:hover:bg-gray-700 (contoh) */
            }

            /* Matiin Outline */
            #historyTable thead tr th:hover {
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
    <div class="bg-white flex p-5 text-lg justify-between dark:bg-gray-800">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('location.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                        </svg>
                        History
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
    </div>

    <x-alerts />

    <div class="p-5">
        <div class="relative overflow-x-auto shadow-md rounded-lg bg-white dark:bg-gray-800 p-4">
            <div id="date-range-picker" date-rangepicker class="flex items-center mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input id="start_date" name="start" type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date start">
                </div>
                <span class="mx-4 text-gray-500">to</span>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input id="end_date" name="end" type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Select date end">
                </div>
                <span class="mx-2"></span>
                <button id="clear_date" class="px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    Clear
                </button>
            </div>


            <table id="historyTable" class="w-full text-sm text-left text-gray-500 dark:text-gray-100"
                data-url="{{ route('api.history') }}">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3">Event</th>
                        <th scope="col" class="px-6 py-3">User</th>
                        <th scope="col" class="px-6 py-3">Changes</th>
                        <th scope="col" class="px-6 py-3">Date</th>
                    </tr>
                    <tr id="filter-row">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.usersForFilterData = @json($usersForFilter ?? []);
    </script>
    @vite('resources/js/pages/history.js')
@endpush