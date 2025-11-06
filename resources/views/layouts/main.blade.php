<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asset Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
    <style>
        #logo-sidebar, #main-content {
            transition: all 0.3s ease-in-out;
        }
        #logo-sidebar.collapsed .menu-text, #logo-sidebar.collapsed .arrow-icon {
            display: none;
        }
        #logo-sidebar.collapsed .group {
            justify-content: center;
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
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
                    <div class="flex items-center ms-3 gap-2">

                        <!-- Tombol utama dropdown -->
                        <button type="button" aria-expanded="false" data-dropdown-toggle="dropdown-company">
                            <div class="text-black hover:bg-gray-200 hover:rounded-md font-medium text-sm px-2.5 py-2.5 text-center inline-flex items-center dark:text-white dark:hover:bg-gray-600">
                                {{ $activeCompany?->name ?? 'Choose Company' }}
                                <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2 text-black dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                                </svg>
                            </div>
                        </button>
                        <!-- Konten Dropdown -->
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm border border-gray-200 dark:bg-gray-700 dark:border-gray-600" id="dropdown-company">
                            <ul class="py-1" role="none">
                                <!-- Menu "Setting Company" -->
                                @if($activeCompany)
                                    @can('is-admin')
                                        <li>
                                            <a href="{{ route('company.edit', ['company' => $activeCompany->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">Setting Company</a>
                                        </li>
                                    @endcan
                                @endif
                                @if(isset($userCompanies) && $userCompanies->count() > 1)
                                    <hr class="my-1 border-gray-200 dark:border-gray-600">
                                    <li class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400">Switch Company</li>
                                    @foreach ($userCompanies as $company)
                                        @if($activeCompany?->id !== $company->id)
                                            <li>
                                                <form action="{{ route('company.switch') }}" method="POST" class="w-full">
                                                    @csrf
                                                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
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
                        <button id="theme-toggle" type="button" class="text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-0 rounded-lg text-sm p-2.5">
                            <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M11.675 2.015a.998.998 0 0 0-.403.011C6.09 2.4 2 6.722 2 12c0 5.523 4.477 10 10 10 4.356 0 8.058-2.784 9.43-6.667a1 1 0 0 0-1.02-1.33c-.08.006-.105.005-.127.005h-.001l-.028-.002A5.227 5.227 0 0 0 20 14a8 8 0 0 1-8-8c0-.952.121-1.752.404-2.558a.996.996 0 0 0 .096-.428V3a1 1 0 0 0-.825-.985Z" clip-rule="evenodd"/>
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                              <path fill-rule="evenodd" d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Notification -->
                        <button type="button" class="relative inline-flex items-center text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg focus:ring-0 focus:outline-none p-2.5" aria-expanded="false" data-dropdown-toggle="dropdown-notif">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.133 12.632v-1.8a5.406 5.406 0 0 0-4.154-5.262.955.955 0 0 0 .021-.106V3.1a1 1 0 0 0-2 0v2.364a.955.955 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C6.867 15.018 5 15.614 5 16.807 5 17.4 5 18 5.538 18h12.924C19 18 19 17.4 19 16.807c0-1.193-1.867-1.789-1.867-4.175ZM8.823 19a3.453 3.453 0 0 0 6.354 0H8.823Z"/>
                            </svg>
                            <span class="sr-only">Notifications</span>
                            @if($pendingApprovals->isNotEmpty())
                                <div class="absolute inline-flex items-center justify-center w-3 h-3 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-1 -end-1 dark:border-gray-900"></div>
                            @endif
                        </button>
                        <!-- Dropdown Notif -->
                         <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm border border-gray-200 dark:bg-gray-700 dark:border-gray-600" id="dropdown-notif">
                            <ul class="py-1" role="none">
                                @forelse($pendingApprovals as $notification)
                                    <li>
                                        <a href="{{ $notification->show_url }}" class="flex flex-row gap-10 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">
                                            <div class="flex flex-col">
                                                <p class="font-semibold">{{ $notification->form_type }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $notification->form_no }}</p>
                                            </div>    
                                            <div class="flex">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $notification->form_date }}</p>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">Tidak ada tugas persetujuan saat ini.</li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Shortcut -->
                        <div>
                            <button type="button" class="flex text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg focus:ring-0 p-2.5" data-dropdown-toggle="dropdown-shortcut">
                                <div class="flex justify-center items-center">
                                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M20.337 3.664c.213.212.354.486.404.782.294 1.711.657 5.195-.906 6.76-1.77 1.768-8.485 5.517-10.611 6.683a.987.987 0 0 1-1.176-.173l-.882-.88-.877-.884a.988.988 0 0 1-.173-1.177c1.165-2.126 4.913-8.841 6.682-10.611 1.562-1.563 5.046-1.198 6.757-.904.296.05.57.191.782.404ZM5.407 7.576l4-.341-2.69 4.48-2.857-.334a.996.996 0 0 1-.565-1.694l2.112-2.111Zm11.357 7.02-.34 4-2.111 2.113a.996.996 0 0 1-1.69-.565l-.422-2.807 4.563-2.74Zm.84-6.21a1.99 1.99 0 1 1-3.98 0 1.99 1.99 0 0 1 3.98 0Z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm border border-gray-200 dark:bg-gray-700 dark:border-gray-600" id="dropdown-shortcut">
                            <ul class="py-1" role="none">
                                <li>
                                    @can('is-dev')
                                        <a href="{{ route('company.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">Create Company</a>
                                    @endcan
                                    <a href="{{ route('register-asset.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">Add Register Form</a>
                                    <a href="{{ route('company-user.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">Add Company User</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Dropdown User -->
                        <div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                <img class="w-8 h-8 rounded-full" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}" alt="user photo">
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{ Auth::user()->name }} | {{ Auth::user()->role }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">Settings</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white" role="menuitem">
                                        @csrf
                                        <button type="submit">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex pt-16">
        <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
            <div class="justify-between h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800 flex flex-col">
                <ul class="space-y-2 font-medium flex-grow">

                    <li class="sm:hidden mb-4 border-b-2 p-4">
                        <a href="{{ route('dashboard') }}" class="flex ms-2 dark:hidden">
                            <img src="{{ asset('images/logo.svg') }}" class="h-6 me-3" alt="Asset Management Logo" />
                            <span class="self-center text-md font-semibold whitespace-nowrap dark:text-white">Asset Management</span>
                        </a>
                        <a href="{{ route('dashboard') }}" class="hidden ms-2 dark:flex">
                            <img src="{{ asset('images/logo-dark.svg') }}" class="h-6 me-3" alt="Asset Management Logo" />
                            <span class="self-center text-md font-semibold whitespace-nowrap dark:text-white">Asset Management</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('dashboard') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/>
                            </svg>
                            <span class="ms-3 menu-text">Dashboard</span>
                        </a>
                    </li>

                    <li class="text-md text-gray-400 pt-2 textSidebar">Data Master</li>

                    <li>
                        <a href="{{ route('location.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('location.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('location.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z"/>
                            </svg>
                            <span class="ms-3 menu-text">Location</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('department.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('department.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('department.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z"/>
                            </svg>
                            <span class="ms-3 menu-text">Department</span>
                        </a>
                    </li>

                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-grouping" data-collapse-toggle="dropdown-grouping" aria-expanded="{{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.005 11.19V12l6.998 4.042L19 12v-.81M5 16.15v.81L11.997 21l6.998-4.042v-.81M12.003 3 5.005 7.042l6.998 4.042L19 7.042 12.003 3Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text">Grouping</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-grouping" class="{{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('asset-class.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('asset-class.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Asset Class</a>
                            </li>
                            <li>
                                <a href="{{ route('asset-sub-class.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('asset-sub-class.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Asset Sub Class</a>
                            </li>
                            <li>
                                <a href="{{ route('asset-name.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('asset-name.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Asset Name</a>
                            </li>
                        </ul>
                    </li>

                    <li class="text-md text-gray-400 pt-2 textSidebar">Asset</li>

                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-asset" data-collapse-toggle="dropdown-asset" aria-expanded="{{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-width="2" d="M3 11h18m-9 0v8m-8 0h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text">Asset</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-asset" class="{{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            @can('is-admin')
                                <li>
                                    <a href="{{ route('assetArrival.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('assetArrival.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Arrival</a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ route('asset.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('asset.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Fixed Asset</a>
                            </li>
                            <li>
                                <a href="{{ route('assetLVA.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('assetLVA.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Low Value Asset</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-action" data-collapse-toggle="dropdown-action" aria-expanded="{{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 4h3a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h3m0 3h6m-6 5h6m-6 4h6M10 3v4h4V3h-4Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text">Form</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-action" class="{{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('register-asset.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('register-asset.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Register Asset</a>
                            </li>
                            <li>
                                <a href="{{ route('transfer-asset.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('transfer-asset.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Transfer Asset</a>
                            </li>
                            <li>
                                <a href="{{ route('disposal-asset.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('disposal-asset.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Disposal Asset</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-depre" data-collapse-toggle="dropdown-depre" aria-expanded="{{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 0 2-2m-2 2-2-2M3 6v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Zm2 2v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8H5Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text">Depreciation</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-depre" class="{{ request()->routeIs(['depreciation.*', 'depreciationFiscal.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('depreciation.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('depreciation.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Commercial</a>
                            </li>
                            <li>
                                <a href="{{ route('depreciationFiscal.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('depreciationFiscal.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Fiscal</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('insurance.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('insurance.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('insurance.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M6 14h2m3 0h5M3 7v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text">Insurance</span>
                        </a>
                    </li>

                    <li class="text-md text-gray-400 pt-2 textSidebar">Company</li>

                    <li>
                        <a href="{{ route('company-user.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('company-user.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('company-user.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-width="2" d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text">Users</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('company.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('company.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('company.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text">Company</span>
                        </a>
                    </li>

                @canany(['is-dev','is-admin'])
                    <li>
                        <a href="{{ route('history.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('history.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('history.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M10 12v1h4v-1m4 7H6a1 1 0 0 1-1-1V9h14v9a1 1 0 0 1-1 1ZM4 5h16a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text">History</span>
                        </a>
                    </li>
                @endcanany

                </ul>
                <div class="mt-auto hidden sm:block">
                    <button id="sidebar-toggle" class="w-full flex items-center justify-center p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg id="sidebar-toggle-icon" class="w-6 h-6 transition-transform text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17 16-4-4 4-4m-6 8-4-4 4-4"/>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <main id="main-content" class="flex-1 sm:ml-64 overflow-x-hidden transition-all duration-300 ease-in-out">
            @yield('content')
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            function applyTheme(theme) {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    if(themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
                    if(themeToggleLightIcon) themeToggleLightIcon.classList.add('hidden');
                } else {
                    document.documentElement.classList.remove('dark');
                    if(themeToggleDarkIcon) themeToggleDarkIcon.classList.add('hidden');
                    if(themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
                }
            }

            const savedTheme = localStorage.getItem('color-theme');
            if (savedTheme) {
                applyTheme(savedTheme);
            } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                applyTheme('dark');
            } else {
                applyTheme('light');
            }

            if(themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    const newTheme = isDark ? 'dark' : 'light';
                    localStorage.setItem('color-theme', newTheme);
                    applyTheme(newTheme);
                });
            }

            const sidebar = document.getElementById('logo-sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarToggleBtn = document.getElementById('sidebar-toggle');
            const sidebarToggleIcon = document.getElementById('sidebar-toggle-icon');
            const sidebarText = document.getElementsByClassName('textSidebar');
            
            if (sidebar && mainContent && sidebarToggleBtn && sidebarToggleIcon) {
                function applySidebarState(isCollapsed) {
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        sidebar.style.width = '4.5rem'; // 72px

                        if (window.innerWidth >= 640) {
                            mainContent.style.marginLeft = '4.5rem';
                        }
                        sidebarToggleIcon.style.transform = 'rotate(180deg)';
                        for (let text of sidebarText) {
                            text.classList.add('hidden');
                        }
                    } else {
                        sidebar.classList.remove('collapsed');
                        sidebar.style.width = '16rem'; // 256px
                        if (window.innerWidth >= 640) {
                            mainContent.style.marginLeft = '16rem';
                        }
                        sidebarToggleIcon.style.transform = 'rotate(0deg)';
                        for (let text of sidebarText) {
                            text.classList.remove('hidden');
                        }
                    }
                }

                const dropdownToggles = document.querySelectorAll('aside button[data-collapse-toggle]');

                dropdownToggles.forEach(toggle => {
                    const arrowIcon = toggle.querySelector('.arrow-icon');
                    if (arrowIcon && toggle.getAttribute('aria-expanded') === 'true') {
                        arrowIcon.classList.add('rotate-180');
                    }
                });

                dropdownToggles.forEach(toggle => {
                    toggle.addEventListener('click', function(event) {
                        // Cek apakah sidebar sedang dalam keadaan kecil (collapsed)
                        if (sidebar.classList.contains('collapsed')) {
                            // Mencegah dropdown terbuka di saat yang bersamaan
                            event.preventDefault();
                            event.stopPropagation();
                            
                            // Buka sidebar
                            applySidebarState(false);
                            localStorage.setItem('sidebar-collapsed', 'false');
                        } else {
                            // PENAMBAHAN BARU: Logika untuk memutar arrow icon
                            const arrowIcon = this.querySelector('.arrow-icon');
                            if (arrowIcon) {
                                arrowIcon.classList.toggle('rotate-180');
                            }
                        }
                    });
                });

                const isSidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                applySidebarState(isSidebarCollapsed);

                sidebarToggleBtn.addEventListener('click', () => {
                    const currentlyCollapsed = sidebar.classList.contains('collapsed');
                    const newState = !currentlyCollapsed;
                    localStorage.setItem('sidebar-collapsed', newState);
                    applySidebarState(newState);
                });
                
                window.addEventListener('resize', () => {
                    const currentState = localStorage.getItem('sidebar-collapsed') === 'true';
                    if (window.innerWidth < 640) {
                        mainContent.style.marginLeft = '0';
                    } else {
                        applySidebarState(currentState);
                    }
                });

                sidebar.addEventListener('mouseenter', function() {            
                    const isStoredAsCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                    if (isStoredAsCollapsed) {
                        applySidebarState(false); // Buka sidebar
                    }
                });

                sidebar.addEventListener('mouseleave', function() {
                    const isStoredAsCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                    if (isStoredAsCollapsed) {
                        applySidebarState(true);
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>