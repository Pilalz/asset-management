<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asset Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/pages/layoutMain.js'])

    @stack('styles')
    <style>
        #logo-sidebar,
        #main-content {
            transition: all 0.3s ease-in-out;
        }

        #logo-sidebar.collapsed .menu-text,
        #logo-sidebar.collapsed .arrow-icon {
            display: none;
        }

        #logo-sidebar.collapsed .group {
            justify-content: center;
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-gray-900 font-sans antialiased">
    <nav class="fixed top-0 z-40 w-full bg-white/80 backdrop-blur-md border-b border-slate-200 dark:bg-gray-800/90 dark:border-gray-700 shadow-sm transition-colors duration-300">
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
                            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Asset Management</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="hidden ms-2 md:me-24 dark:flex">
                            <img src="{{ asset('images/logo-dark.svg') }}" class="h-8 me-3" alt="Asset Management Logo" />
                            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Asset Management</span>
                        </a>
                    </div>

                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-2.5">

                        <!-- Tombol utama dropdown -->
                        <button type="button" aria-expanded="false" data-dropdown-toggle="dropdown-company" data-dropdown-placement="bottom-end"
                            class="rounded-lg text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 font-medium text-sm px-3 py-2.5 text-center dark:text-gray-200 dark:hover:bg-slate-700 dark:hover:text-indigo-400 focus:bg-indigo-50 dark:focus:bg-slate-700 transition-colors">
                            <div class="inline-flex items-center">
                                {{ $activeCompany?->name ?? 'Choose Company' }}
                                <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2 text-black dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                                </svg>
                            </div>
                        </button>
                        <!-- Konten Dropdown -->
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-64" id="dropdown-company">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl">
                                <span class="block text-sm font-semibold text-gray-900 dark:text-white">Active Company</span>
                            </div>
                            <ul class="py-1" role="none">
                                <!-- Menu "Setting Company" -->
                                @if($activeCompany)
                                    @can('is-admin')
                                        <li>
                                            <a href="{{ route('company.edit', ['company' => $activeCompany->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors" role="menuitem">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-white transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                            d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z" />
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                                    </svg>
                                                    Setting Company
                                                </div>
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                                @if(isset($userCompanies) && $userCompanies->count() > 1)
                                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                                    <li class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider dark:text-gray-500">Switch Company</li>
                                    @foreach ($userCompanies as $company)
                                        @if($activeCompany?->id !== $company->id)
                                            <li>
                                                <form action="{{ route('company.switch') }}" method="POST" class="w-full">
                                                    @csrf
                                                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 dark:text-gray-200 dark:hover:bg-gray-700/50 dark:hover:text-indigo-400 transition-colors" role="menuitem">
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
                        <button id="theme-toggle" type="button" class="text-slate-500 hover:bg-indigo-50 dark:text-slate-400 dark:hover:bg-slate-700 focus:outline-none focus:ring-0 rounded-lg text-sm p-2.5 transition-colors">
                            <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M11.675 2.015a.998.998 0 0 0-.403.011C6.09 2.4 2 6.722 2 12c0 5.523 4.477 10 10 10 4.356 0 8.058-2.784 9.43-6.667a1 1 0 0 0-1.02-1.33c-.08.006-.105.005-.127.005h-.001l-.028-.002A5.227 5.227 0 0 0 20 14a8 8 0 0 1-8-8c0-.952.121-1.752.404-2.558a.996.996 0 0 0 .096-.428V3a1 1 0 0 0-.825-.985Z" clip-rule="evenodd" />
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z" clip-rule="evenodd" />
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
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-80 sm:w-96" id="dropdown-notif">
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

    <div class="flex pt-16">
        <aside id="logo-sidebar" class="fixed top-0 left-0 z-[35] h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
            <div class="justify-between h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800 flex flex-col">
                <ul class="space-y-1 font-medium flex-grow">

                    <li class="sm:hidden mb-4 border-b-2 p-4">
                        <a href="{{ route('dashboard') }}" class="flex ms-2 dark:hidden">
                            <img src="{{ asset('images/logo.svg') }}" class="h-6 me-3" alt="Asset Management Logo" />
                            <span class="self-center text-md font-semibold whitespace-nowrap dark:text-white">Asset
                                Management</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="hidden ms-2 dark:flex">
                            <img src="{{ asset('images/logo-dark.svg') }}" class="h-6 me-3"
                                alt="Asset Management Logo" />
                            <span class="self-center text-md font-semibold whitespace-nowrap dark:text-white">Asset
                                Management</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dashboard') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z" />
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z" />
                            </svg>
                            <span class="ms-3 menu-text font-medium">Dashboard</span>
                        </a>
                    </li>

                    <li class="px-2 mt-5 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider textSidebar">Data Master</li>

                    <li>
                        <a href="{{ route('location.index') }}"
                            class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('location.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('location.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z" />
                            </svg>
                            <span class="ms-3 menu-text font-medium">Location</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('department.index') }}"
                            class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('department.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('department.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z" />
                            </svg>
                            <span class="ms-3 menu-text font-medium">Department</span>
                        </a>
                    </li>

                    <li>
                        <button type="button"
                            class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group {{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}"
                            aria-controls="dropdown-grouping" data-collapse-toggle="dropdown-grouping"
                            aria-expanded="{{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5.005 11.19V12l6.998 4.042L19 12v-.81M5 16.15v.81L11.997 21l6.998-4.042v-.81M12.003 3 5.005 7.042l6.998 4.042L19 7.042 12.003 3Z" />
                            </svg>
                            <span
                                class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text font-medium">Grouping</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <ul id="dropdown-grouping"
                            class="{{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('asset-class.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('asset-class.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Asset
                                    Class</a>
                            </li>
                            <li>
                                <a href="{{ route('asset-sub-class.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('asset-sub-class.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Asset
                                    Sub Class</a>
                            </li>
                            <li>
                                <a href="{{ route('asset-name.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('asset-name.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Asset
                                    Name</a>
                            </li>
                        </ul>
                    </li>

                    <li class="px-2 mt-5 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider textSidebar">
                        Asset</li>

                    <li>
                        <button type="button"
                            class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group {{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}"
                            aria-controls="dropdown-asset" data-collapse-toggle="dropdown-asset"
                            aria-expanded="{{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-width="2"
                                    d="M3 11h18m-9 0v8m-8 0h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                            </svg>
                            <span
                                class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text font-medium">Asset</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <ul id="dropdown-asset"
                            class="{{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            @can('is-admin')
                                <li>
                                    <a href="{{ route('assetArrival.index') }}"
                                        class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('assetArrival.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Arrival</a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ route('asset.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('asset.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Fixed
                                    Asset</a>
                            </li>
                            <li>
                                <a href="{{ route('assetLVA.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('assetLVA.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Low
                                    Value Asset</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <button type="button"
                            class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group {{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}"
                            aria-controls="dropdown-action" data-collapse-toggle="dropdown-action"
                            aria-expanded="{{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 5h6m-6 4h6M10 3v4h4V3h-4Z" />
                            </svg>
                            <span
                                class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text font-medium">Form</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <ul id="dropdown-action"
                            class="{{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('register-asset.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('register-asset.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Register
                                    Asset</a>
                            </li>
                            <li>
                                <a href="{{ route('transfer-asset.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('transfer-asset.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Transfer
                                    Asset</a>
                            </li>
                            <li>
                                <a href="{{ route('disposal-asset.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('disposal-asset.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Disposal
                                    Asset</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <button type="button"
                            class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group {{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}"
                            aria-controls="dropdown-depre" data-collapse-toggle="dropdown-depre"
                            aria-expanded="{{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 11v5m0 0 2-2m-2 2-2-2M3 6v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Zm2 2v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8H5Z" />
                            </svg>
                            <span
                                class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text font-medium">Depreciation</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        <ul id="dropdown-depre"
                            class="{{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('depreciation.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('depreciation.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Commercial</a>
                            </li>
                            <li>
                                <a href="{{ route('depreciationFiscal.index') }}"
                                    class="flex items-center w-full p-2 transition duration-75 rounded-lg pl-11 group menu-text {{ request()->routeIs('depreciationFiscal.*') ? 'text-indigo-600 font-medium bg-indigo-50 dark:text-indigo-300 dark:bg-indigo-900/50' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">Fiscal</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('stock-opname.index') }}"
                            class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('stock-opname.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 {{ request()->routeIs('stock-opname.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M10 18v2H4V4h6v2m4 12v2h6V4h-6v2m-6.49543 8.4954L10 12m0 0L7.50457 9.50457M10 12H4.05191m12.50199 2.5539L14 12m0 0 2.5539-2.55392M14 12h5.8319" />
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text font-medium">Stock Opname</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('insurance.index') }}"
                            class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('insurance.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('insurance.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 10h18M6 14h2m3 0h5M3 7v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Z" />
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text font-medium">Insurance</span>
                        </a>
                    </li>

                    <li class="px-2 mt-5 mb-2 text-xs font-bold text-slate-400 uppercase tracking-wider textSidebar">
                        Company</li>

                    <li>
                        <a href="{{ route('company-user.index') }}"
                            class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('company-user.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('company-user.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text font-medium">Users</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('company.index') }}"
                            class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('company.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('company.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z" />
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text font-medium">Company</span>
                        </a>
                    </li>

                    @canany(['is-dev', 'is-admin'])
                        <li>
                            <a href="{{ route('history.index') }}"
                                class="flex items-center p-2 rounded-lg group transition-colors duration-200 {{ request()->routeIs('history.*') ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-400' }}">
                                <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('history.*') ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-600 dark:text-slate-400 dark:group-hover:text-indigo-400' }}"
                                    aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linejoin="round" stroke-width="2"
                                        d="M10 12v1h4v-1m4 7H6a1 1 0 0 1-1-1V9h14v9a1 1 0 0 1-1 1ZM4 5h16a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                                </svg>
                                <span class="flex-1 ms-3 whitespace-nowrap menu-text font-medium">History</span>
                            </a>
                        </li>
                    @endcanany

                </ul>
                <div class="mt-auto hidden sm:block">
                    <button id="sidebar-toggle"
                        class="w-full flex items-center justify-center p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg id="sidebar-toggle-icon" class="w-6 h-6 transition-transform text-gray-800 dark:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                            viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m17 16-4-4 4-4m-6 8-4-4 4-4" />
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <main id="main-content"
            class="flex-1 sm:ml-64 overflow-x-hidden transition-all duration-300 ease-in-out flex flex-col min-h-[calc(100vh-4rem)]">
            <div class="flex-grow">
                @yield('content')
            </div>
            <footer class="p-6 text-center text-xs text-slate-400 dark:text-slate-500">
                &copy; {{ date('Y') }} Asset Management. All rights reserved.
            </footer>
        </main>

        {{-- Global Delete Confirmation Modal --}}
        <div id="delete-modal" tabindex="-1"
            class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <button type="button"
                        class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="delete-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                    <div class="p-4 md:p-5 text-center">
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to
                            delete this?</h3>
                        <form id="delete-form" action="" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                Yes, I'm sure
                            </button>
                        </form>
                        <button data-modal-hide="delete-modal" type="button"
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">No,
                            cancel</button>
                    </div>
                </div>
            </div>
        </div>


    </div>

    @stack('scripts')
</body>

</html>