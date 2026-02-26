<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Styling khusus video agar fill container reader */
        #reader video {
            object-fit: cover !important;
            width: 100% !important;
            height: 100% !important;
        }

        /* Kotak scan visual */
        #reader__scan_region {
            background: rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-gray-900 font-sans antialiased">
    <nav
        class="fixed top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-slate-200 dark:bg-gray-800/90 dark:border-gray-700 transition-colors duration-300">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
                        aria-controls="logo-sidebar" type="button"
                        class="inline-flex items-center p-2 text-sm text-slate-500 rounded-lg sm:hidden hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:text-slate-400 dark:hover:bg-slate-700">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>

                    <div class="hidden sm:flex">
                        <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24 dark:hidden">
                            <img src="{{ asset('images/logo.svg') }}" class="h-8 me-3" alt="Asset Management Logo" />
                            <span
                                class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Asset
                                Management</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="hidden ms-2 md:me-24 dark:flex">
                            <img src="{{ asset('images/logo-dark.svg') }}" class="h-8 me-3"
                                alt="Asset Management Logo" />
                            <span
                                class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Asset
                                Management</span>
                        </a>
                    </div>

                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-2.5">

                        <!-- Tombol utama dropdown -->
                        <button type="button" aria-expanded="false" data-dropdown-toggle="dropdown-company"
                            data-dropdown-placement="bottom-end"
                            class="rounded-lg text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium text-sm px-3 py-2.5 text-center dark:text-gray-200 dark:hover:bg-slate-700 dark:hover:text-indigo-400 focus:bg-indigo-50 dark:focus:bg-slate-700 transition-colors">
                            <div class="inline-flex items-center">
                                {{ $activeCompany?->name ?? 'Choose Company' }}
                                <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2 text-black dark:text-white"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 9-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <!-- Konten Dropdown -->
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-64"
                            id="dropdown-company">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl">
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Active
                                    Company</span>
                            </div>
                            <ul class="py-1" role="none">
                                <!-- Menu "Setting Company" -->
                                @if($activeCompany)
                                    @can('is-admin')
                                        <li>
                                            <a href="{{ route('company.edit', ['company' => $activeCompany->id]) }}"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                                role="menuitem">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-white transition-colors"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z" />
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                                    </svg>
                                                    Setting Company
                                                </div>
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                                @if(isset($userCompanies) && $userCompanies->count() > 1)
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <li
                                        class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider dark:text-gray-500">
                                        Switch Company</li>
                                    @foreach ($userCompanies as $company)
                                        @if($activeCompany?->id !== $company->id)
                                            <li>
                                                <form action="{{ route('company.switch') }}" method="POST" class="w-full">
                                                    @csrf
                                                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                                                    <button type="submit"
                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-200 dark:hover:bg-gray-700/50 dark:hover:text-indigo-400 transition-colors"
                                                        role="menuitem">
                                                        {{ $company->name }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <!-- Theme Setting -->
                        <button id="theme-toggle" type="button"
                            class="text-slate-500 hover:bg-indigo-50 dark:text-slate-400 dark:hover:bg-slate-700 focus:outline-none focus:ring-0 rounded-lg text-sm p-2.5 transition-colors">
                            <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M11.675 2.015a.998.998 0 0 0-.403.011C6.09 2.4 2 6.722 2 12c0 5.523 4.477 10 10 10 4.356 0 8.058-2.784 9.43-6.667a1 1 0 0 0-1.02-1.33c-.08.006-.105.005-.127.005h-.001l-.028-.002A5.227 5.227 0 0 0 20 14a8 8 0 0 1-8-8c0-.952.121-1.752.404-2.558a.996.996 0 0 0 .096-.428V3a1 1 0 0 0-.825-.985Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <!-- Notification -->
                        <button type="button"
                            class="relative inline-flex items-center text-sm text-slate-500 hover:bg-indigo-50 dark:text-slate-400 dark:hover:bg-slate-700 rounded-lg focus:ring-0 focus:bg-indigo-50 dark:focus:bg-slate-700 p-2.5 transition-colors"
                            aria-expanded="false" data-dropdown-toggle="dropdown-notif"
                            data-dropdown-placement="bottom-end">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M17.133 12.632v-1.8a5.406 5.406 0 0 0-4.154-5.262.955.955 0 0 0 .021-.106V3.1a1 1 0 0 0-2 0v2.364a.955.955 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C6.867 15.018 5 15.614 5 16.807 5 17.4 5 18 5.538 18h12.924C19 18 19 17.4 19 16.807c0-1.193-1.867-1.789-1.867-4.175ZM8.823 19a3.453 3.453 0 0 0 6.354 0H8.823Z" />
                            </svg>
                            <span class="sr-only">Notifications</span>
                            @if($pendingApprovals->isNotEmpty())
                                <div
                                    class="absolute inline-flex items-center justify-center w-2.5 h-2.5 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full top-2.5 end-2.5 dark:border-gray-900">
                                </div>
                            @endif
                        </button>
                        <!-- Dropdown Notif -->
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-80 sm:w-96"
                            id="dropdown-notif">
                            <div
                                class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900 dark:text-white">Form Notifications</h3>
                                @if($pendingApprovals->isNotEmpty())
                                    <span
                                        class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-indigo-900 dark:text-indigo-300">
                                        {{ $pendingApprovals->count() }} new
                                    </span>
                                @endif
                            </div>
                            <ul class="py-0" role="none">
                                @forelse($pendingApprovals as $notification)
                                    <li>
                                        <a href="{{ $notification->show_url }}"
                                            class="flex items-start gap-4 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0"
                                            role="menuitem">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                                    <svg class="w-4 h-4" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                                    {{ $notification->form_type }}
                                                </p>
                                                <p class="text-xs text-gray-500 truncate dark:text-gray-400">
                                                    {{ $notification->form_no }}
                                                </p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                    {{ $notification->form_date }}
                                                </p>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="m15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            <span>No pending tasks.</span>
                                        </div>
                                    </li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Shortcut -->
                        <div>
                            <button type="button"
                                class="flex text-sm hover:bg-indigo-50 dark:hover:bg-slate-700 rounded-lg focus:ring-0 focus:bg-indigo-50 dark:focus:bg-slate-700 p-2.5"
                                data-dropdown-toggle="dropdown-shortcut" data-dropdown-placement="bottom-end">
                                <div class="flex justify-center items-center">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                            d="M20.337 3.664c.213.212.354.486.404.782.294 1.711.657 5.195-.906 6.76-1.77 1.768-8.485 5.517-10.611 6.683a.987.987 0 0 1-1.176-.173l-.882-.88-.877-.884a.988.988 0 0 1-.173-1.177c1.165-2.126 4.913-8.841 6.682-10.611 1.562-1.563 5.046-1.198 6.757-.904.296.05.57.191.782.404ZM5.407 7.576l4-.341-2.69 4.48-2.857-.334a.996.996 0 0 1-.565-1.694l2.112-2.111Zm11.357 7.02-.34 4-2.111 2.113a.996.996 0 0 1-1.69-.565l-.422-2.807 4.563-2.74Zm.84-6.21a1.99 1.99 0 1 1-3.98 0 1.99 1.99 0 0 1 3.98 0Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-56"
                            id="dropdown-shortcut">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl">
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Shortcuts</span>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ route('cache-clear') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                        role="menuitem">
                                        <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-amber-500 dark:text-gray-500 dark:group-hover:text-amber-400 transition-colors"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4" />
                                        </svg>
                                        Clear Cache
                                    </a>
                                </li>
                                <!-- HANYA MUNCUL SAAT SO -->
                                @if ($so_exist)
                                    <li>
                                        <a href="{{ route('scan.index') }}"
                                            class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                            role="menuitem">
                                            <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-indigo-400 transition-colors"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M11 6.5h2M11 18h2m-7-5v-2m12 2v-2M5 8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm0 12h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm12 0h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm0-12h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Z" />
                                            </svg>
                                            SO Scanner
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a href="{{ route('scan.index') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                        role="menuitem">
                                        <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-indigo-400 transition-colors"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 6.5h2M11 18h2m-7-5v-2m12 2v-2M5 8h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm0 12h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm12 0h2a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Zm0-12h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1Z" />
                                        </svg>
                                        Scan Asset
                                    </a>
                                </li>
                                @can('is-dev')
                                    <li>
                                        <a href="{{ route('company.create') }}"
                                            class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                            role="menuitem">
                                            <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-green-600 dark:text-gray-500 dark:group-hover:text-green-400 transition-colors"
                                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            Create Company
                                        </a>
                                    </li>
                                @endcan
                                @canany(['is-dev', 'is-admin'])
                                <li>
                                    <a href="{{ route('company-user.create') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors"
                                        role="menuitem">
                                        <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-blue-600 dark:text-gray-500 dark:group-hover:text-blue-400 transition-colors"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 12h4m-2 2v-4M4 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Add Company User
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>

                        <!-- Dropdown User -->
                        <div>
                            <button type="button"
                                class="flex text-sm bg-indigo-100 rounded-full focus:ring-4 focus:ring-indigo-100 dark:bg-indigo-900/50 dark:focus:ring-indigo-900"
                                data-dropdown-toggle="dropdown-user" data-dropdown-placement="bottom-end">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full p-0.5 bg-white dark:bg-gray-800 object-cover"
                                    src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}"
                                    alt="user photo">
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-64"
                            id="dropdown-user">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" role="none">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mb-1">
                                    {{ Auth::user()->role }}
                                </p>
                                <p class="text-xs font-medium text-gray-500 truncate dark:text-gray-400" role="none">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ route('profile.edit') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white transition-colors"
                                        role="menuitem">
                                        <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-white transition-colors"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                                        </svg>
                                        My Profile
                                    </a>
                                </li>
                                <li>
                                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                                        @csrf
                                    </form>
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="group flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300 transition-colors"
                                        role="menuitem">
                                        <svg class="w-4 h-4 mr-3 text-red-400 group-hover:text-red-600 dark:text-red-500 dark:group-hover:text-red-400 transition-colors"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                                        </svg>
                                        Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center py-20 px-4">

        <!-- ===[ PANEL SCANNER ]=== -->
        <div id="panel-scanner" class="w-full max-w-lg">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b dark:border-gray-700 text-center">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Stock Opname Scanner</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Arahkan QR Code ke kamera atau upload
                        gambar</p>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900/50">
                    <div id="reader"
                        class="overflow-hidden rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-black aspect-video flex items-center justify-center relative">
                        <div id="camera-placeholder" class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Kamera nonaktif</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-6 grid grid-cols-2 gap-3">
                    <button id="start-btn"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold shadow-lg shadow-indigo-500/30 transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Buka Kamera
                    </button>
                    <button id="stop-btn"
                        class="hidden flex items-center justify-center gap-2 w-full px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-semibold transition-all active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 10a1 1 0 00-1 1v2a1 1 0 001 1h6a1 1 0 001-1v-2a1 1 0 00-1-1H9z" />
                        </svg>
                        Stop Kamera
                    </button>
                    <label
                        class="col-span-2 flex items-center justify-center gap-2 w-full px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl font-semibold cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload Gambar
                        <input type="file" id="qr-input-file" accept="image/*" class="hidden">
                    </label>
                </div>
            </div>
        </div>

        <!-- ===[ PANEL DETAIL ASET ]=== -->
        <div id="panel-detail" class="hidden w-full max-w-lg">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">

                <!-- Status Badge Header -->
                <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Asset Details</h2>
                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-0.5">Scan QR Code Result</p>
                    </div>
                    <span id="detail-status-badge"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300"></span>
                </div>

                <!-- Asset Info -->
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                Asset Number</p>
                            <p id="detail-number" class="mt-1 font-semibold text-gray-800 dark:text-white">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                Asset Name</p>
                            <p id="detail-name" class="mt-1 font-semibold text-gray-800 dark:text-white">-</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                Description</p>
                            <p id="detail-description" class="mt-1 text-gray-600 dark:text-gray-300">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                Location</p>
                            <p id="detail-location" class="mt-1 text-gray-700 dark:text-gray-200">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                Department</p>
                            <p id="detail-department" class="mt-1 text-gray-700 dark:text-gray-200">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                Condition</p>
                            <p id="detail-condition" class="mt-1 text-gray-700 dark:text-gray-200">-</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                User</p>
                            <p id="detail-user" class="mt-1 text-gray-700 dark:text-gray-200">-</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                Mark</p>
                            <p id="detail-description" class="mt-1 text-gray-600 dark:text-gray-300">-</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-5 pb-5 flex gap-3">
                    <!-- Toggle Favorite / Bookmark -->
                    <button id="mark-favorite-btn"
                        class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-gray-50 hover:bg-amber-50 dark:bg-gray-700 dark:hover:bg-amber-900/40 text-gray-400 hover:text-amber-500 border border-gray-200 dark:border-gray-600 rounded-xl transition-all duration-300 active:scale-95 group"
                        title="Tandai Asset (Favorite)">
                        <!-- Outline Icon (Unmarked) -->
                        <svg id="mark-icon-outline" class="w-5 h-5 transition-transform group-hover:scale-110"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        <!-- Solid Icon (Marked) - Hidden by default -->
                        <svg id="mark-icon-solid"
                            class="hidden w-5 h-5 text-amber-500 transition-transform group-hover:scale-110"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                        </svg>
                    </button>

                    <!-- Main Actions -->
                    <div class="flex-1 grid grid-cols-2 gap-3">
                        <button id="back-btn"
                            class="flex items-center justify-center gap-2 px-2 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-all active:scale-95 text-sm sm:text-base">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Scan
                        </button>
                        <button id="mark-found-btn"
                            class="flex items-center justify-center gap-2 px-2 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold shadow-lg shadow-green-500/30 transition-all active:scale-95 text-sm sm:text-base">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Mark as found
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===[ WRONG COMPANY MODAL ]=== -->
        <div id="wrong-company-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex justify-center items-center w-full h-full bg-gray-900/50 backdrop-blur-sm">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-2xl shadow dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Assets Owned by Other Companies
                        </h3>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5 space-y-4">
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                            This asset (<span id="modal-asset-name" class="font-bold text-gray-900 dark:text-gray-100"></span> - <span id="modal-asset-number" class="font-bold text-gray-900 dark:text-gray-100"></span>) belongs to <span id="modal-company-name" class="font-bold text-gray-900 dark:text-gray-100"></span>. You have management access in that company.
                        </p>
                    </div>
                    <!-- Modal footer -->
                    <div class="flex flex-col gap-3 p-4 md:p-5 border-t border-gray-100 dark:border-gray-700 rounded-b bg-gray-50 dark:bg-gray-800/50">
                        <form id="switch-company-form" action="{{ route('company.switch') }}" method="POST" class="w-full m-0">
                            @csrf
                            <input type="hidden" name="company_id" id="modal-company-id" value="">
                            <button type="submit" class="w-full text-white bg-amber-500 hover:bg-amber-600 focus:ring-4 focus:outline-none focus:ring-amber-300 font-semibold rounded-xl text-sm px-5 py-3 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800 transition-all shadow-lg shadow-amber-500/30">
                                Switch to <span id="modal-btn-company-name"></span> & Process Asset
                            </button>
                        </form>
                        <button type="button" id="modal-cancel-btn" class="py-3 px-5 w-full text-sm font-semibold text-gray-700 focus:outline-none bg-white rounded-xl border border-gray-300 hover:bg-gray-100 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        // Inject active company ID dari session Blade (server-side)
        const activeCompanyId = {{ session('active_company_id') ?? 'null' }};
        const html5QrCode = new Html5Qrcode("reader");
        const startBtn = document.getElementById('start-btn');
        const stopBtn = document.getElementById('stop-btn');
        const placeholder = document.getElementById('camera-placeholder');
        const fileInput = document.getElementById('qr-input-file');
        const panelScanner = document.getElementById('panel-scanner');
        const panelDetail = document.getElementById('panel-detail');
        const backBtn = document.getElementById('back-btn');

        // Elemen tombol favorite
        const markFavoriteBtn = document.getElementById('mark-favorite-btn');
        const markIconOutline = document.getElementById('mark-icon-outline');
        const markIconSolid = document.getElementById('mark-icon-solid');
        let isMarked = false;

        // --- TAMPILKAN PANEL DETAIL ---
        function showDetail(asset, wrongCompany = false, assetCompanyName = null, assetCompanyId = null) {
            // Isi data
            document.getElementById('detail-number').textContent = asset.asset_number ?? '-';
            document.getElementById('detail-name').textContent = asset.asset_name.name ?? '-';
            document.getElementById('detail-description').textContent = asset.description ?? '-';
            document.getElementById('detail-location').textContent = asset.location.name ?? '-';
            document.getElementById('detail-department').textContent = asset.department.name ?? '-';
            document.getElementById('detail-condition').textContent = asset.condition ?? '-';
            document.getElementById('detail-user').textContent = asset.user ?? '-';

            // ⚠️ Tampilkan/sembunyikan modal wrong company
            const markFoundBtn = document.getElementById('mark-found-btn');
            const modal = document.getElementById('wrong-company-modal');
            if (wrongCompany && assetCompanyName) {
                document.getElementById('modal-asset-name').textContent = asset.asset_name.name ?? '-';
                document.getElementById('modal-asset-number').textContent = asset.asset_number ?? '-';
                document.getElementById('modal-company-name').textContent = assetCompanyName;
                document.getElementById('modal-btn-company-name').textContent = assetCompanyName;
                if (assetCompanyId) document.getElementById('modal-company-id').value = assetCompanyId;
                
                modal.classList.remove('hidden');
                
                // Nonaktifkan tombol "Mark as Found" karena beda company
                markFoundBtn.disabled = true;
                markFoundBtn.classList.add('opacity-50', 'cursor-not-allowed');
                markFoundBtn.title = 'Cannot mark as found: asset belongs to a different company';
            } else {
                modal.classList.add('hidden');
                markFoundBtn.disabled = false;
                markFoundBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                markFoundBtn.title = '';
            }

            // Status badge
            const badge = document.getElementById('detail-status-badge');
            badge.textContent = asset.status ?? '-';
            badge.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ';
            if (asset.status === 'Active') {
                badge.className += 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300';
            } else if (asset.status === 'Inactive') {
                badge.className += 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300';
            } else {
                badge.className += 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
            }

            // Ganti panel
            panelScanner.classList.add('hidden');
            panelDetail.classList.remove('hidden');
        }

        // --- RESET KE PANEL SCANNER ---
        function showScanner() {
            panelDetail.classList.add('hidden');
            panelScanner.classList.remove('hidden');

            // Reset reader border
            document.getElementById('reader').classList.remove('border-green-500', 'border-red-500');

            // Sembunyikan modal
            document.getElementById('wrong-company-modal').classList.add('hidden');
            
            const markFoundBtn = document.getElementById('mark-found-btn');
            markFoundBtn.disabled = false;
            markFoundBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            markFoundBtn.title = '';

            // Reset status Mark (Favorite) jika sebelumnya tertandai
            if (isMarked) {
                isMarked = false;
                markIconOutline.classList.remove('hidden');
                markIconSolid.classList.add('hidden');
                markFavoriteBtn.classList.remove('bg-amber-50', 'border-amber-200', 'text-amber-500', 'dark:bg-amber-900/40', 'dark:border-amber-800');
                markFavoriteBtn.classList.add('bg-gray-50', 'border-gray-200', 'text-gray-400', 'dark:bg-gray-700', 'dark:border-gray-600');
            }

            // Reset tombol kamera ke keadaan awal
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            placeholder.classList.remove('hidden');

            // Auto-start kamera
            startCamera();
        }

        // --- LOGIKA KAMERA ---
        function startCamera() {
            placeholder.classList.add('hidden');
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess
            ).then(() => {
                startBtn.classList.add('hidden');
                stopBtn.classList.remove('hidden');
            }).catch(err => {
                placeholder.classList.remove('hidden');
                console.error("Gagal akses kamera:", err);
            });
        }

        async function onScanSuccess(decodedText) {
            // Hentikan kamerage
            if (html5QrCode.isScanning) {
                await html5QrCode.stop();
            }
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            placeholder.classList.remove('hidden');

            // Feedback visual sementara
            document.getElementById('reader').classList.add('border-indigo-500');

            // Ambil kode dari URL QR (ambil segment terakhir setelah '/')
            const segments = decodedText.split('/');
            const assetCode = segments[segments.length - 1];

            try {
                // Kirim company_id aktif ke API untuk deteksi company mismatch
                const url = `/api/asset-by-code/${encodeURIComponent(assetCode)}` +
                    (activeCompanyId ? `?company_id=${activeCompanyId}` : '');
                const res = await fetch(url);
                const data = await res.json();

                if (res.ok && data.success) {
                    document.getElementById('reader').classList.add('border-green-500');
                    showDetail(data.asset, data.wrong_company, data.asset_company, data.asset_company_id);
                } else {
                    document.getElementById('reader').classList.add('border-red-500');
                    alert(data.message ?? 'Asset not found in the system!');
                    // Biarkan scanner panel tapi reset border setelah 1.5s
                    setTimeout(() => {
                        document.getElementById('reader').classList.remove('border-indigo-500', 'border-red-500');
                    }, 1500);
                }
            } catch (e) {
                alert('Terjadi kesalahan koneksi saat mengambil data asset.');
                document.getElementById('reader').classList.remove('border-indigo-500');
            }
        }

        startBtn.addEventListener('click', startCamera);

        stopBtn.addEventListener('click', () => {
            html5QrCode.stop().then(() => {
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                placeholder.classList.remove('hidden');
            });
        });

        backBtn.addEventListener('click', showScanner);

        document.getElementById('modal-cancel-btn').addEventListener('click', () => {
            document.getElementById('wrong-company-modal').classList.add('hidden');
            showScanner();
        });

        // --- LOGIKA UPLOAD FILE ---
        fileInput.addEventListener('change', e => {
            if (e.target.files.length === 0) return;
            const imageFile = e.target.files[0];
            html5QrCode.scanFile(imageFile, true)
                .then(onScanSuccess)
                .catch(err => alert("QR not found in this image: " + err));
        });

        // --- LOGIKA TANDAI ASSET (FAVORITE) ---
        if (markFavoriteBtn) {
            markFavoriteBtn.addEventListener('click', () => {
                isMarked = !isMarked;
                if (isMarked) {
                    markIconOutline.classList.add('hidden');
                    markIconSolid.classList.remove('hidden');
                    markFavoriteBtn.classList.add('bg-amber-50', 'border-amber-200', 'text-amber-500', 'dark:bg-amber-900/40', 'dark:border-amber-800');
                    markFavoriteBtn.classList.remove('bg-gray-50', 'border-gray-200', 'text-gray-400', 'dark:bg-gray-700', 'dark:border-gray-600');
                } else {
                    markIconOutline.classList.remove('hidden');
                    markIconSolid.classList.add('hidden');
                    markFavoriteBtn.classList.remove('bg-amber-50', 'border-amber-200', 'text-amber-500', 'dark:bg-amber-900/40', 'dark:border-amber-800');
                    markFavoriteBtn.classList.add('bg-gray-50', 'border-gray-200', 'text-gray-400', 'dark:bg-gray-700', 'dark:border-gray-600');
                }
            });
        }
    </script>
    @stack('scripts')
</body>

</html>