<!DOCTYPE html>
<html lang="en" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management</title>

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
                    <a href="#" class="flex ms-2 md:me-24">
                        <img src="{{ asset('images/logo.svg') }}" class="h-8 me-3" alt="Asset Management Logo" />
                        <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Asset Management</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-4">

                        <!-- Tombol utama dropdown -->
                        <button type="button" aria-expanded="false" data-dropdown-toggle="dropdown-company">
                            <div class="text-black hover:bg-gray-200 hover:rounded-md font-medium text-sm px-4 py-2.5 text-center inline-flex items-center dark:text-white dark:hover:bg-gray-600">
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
                                    <li>
                                        <a href="{{ route('company.edit', ['company' => $activeCompany->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">Setting Company</a>
                                    </li>
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
                        <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M11.675 2.015a.998.998 0 0 0-.403.011C6.09 2.4 2 6.722 2 12c0 5.523 4.477 10 10 10 4.356 0 8.058-2.784 9.43-6.667a1 1 0 0 0-1.02-1.33c-.08.006-.105.005-.127.005h-.001l-.028-.002A5.227 5.227 0 0 0 20 14a8 8 0 0 1-8-8c0-.952.121-1.752.404-2.558a.996.996 0 0 0 .096-.428V3a1 1 0 0 0-.825-.985Z" clip-rule="evenodd"/>
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                              <path fill-rule="evenodd" d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Plus Icon -->
                        <div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300" data-dropdown-toggle="dropdown-shortcut">
                                <div class="flex justify-center items-center w-8 h-8 rounded-full">
                                    <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm border border-gray-200 dark:bg-gray-700 dark:border-gray-600" id="dropdown-shortcut">
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ route('company.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600" role="menuitem">Create Company</a>
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
                                    {{ Auth::user()->name }}
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

                    <li>
                        <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('dashboard') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/>
                            </svg>
                            <span class="ms-3 menu-text">Dashboard</span>
                        </a>
                    </li>

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
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-asset" data-collapse-toggle="dropdown-asset" aria-expanded="{{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-width="2" d="M3 11h18m-9 0v8m-8 0h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text">Asset</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-asset" class="{{ request()->routeIs(['asset.*', 'assetLVA.*', 'assetArrival.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('assetArrival.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('assetArrival.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Arrival</a>
                            </li>
                            <li>
                                <a href="{{ route('asset.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('asset.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Fixed Asset</a>
                            </li>
                            <li>
                                <a href="{{ route('assetLVA.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('assetLVA.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Low Value Asset</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-grouping" data-collapse-toggle="dropdown-grouping" aria-expanded="{{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['asset-class.*', 'asset-sub-class.*', 'asset-name.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
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

                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-action" data-collapse-toggle="dropdown-action" aria-expanded="{{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['register-asset.*', 'transfer-asset.*', 'disposal-asset.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="m20.9532 11.7634-2.0523-2.05225-2.0523 2.05225 2.0523 2.0523 2.0523-2.0523Zm-1.3681-2.73651-4.1046-4.10457L12.06 8.3428l4.1046 4.1046 3.4205-3.42051Zm-4.1047 2.73651-2.7363-2.73638-8.20919 8.20918 2.73639 2.7364 8.2091-8.2092Z"/>
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="m12.9306 3.74083 1.8658 1.86571-2.0523 2.05229-1.5548-1.55476c-.995-.99505-3.23389-.49753-3.91799.18657l2.73639-2.73639c.6841-.68409 1.9901-.74628 2.9229.18658Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text">Action</span>
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
                        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 {{ request()->routeIs(['depreciation.*']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}" aria-controls="dropdown-depre" data-collapse-toggle="dropdown-depre" aria-expanded="{{ request()->routeIs(['depreciation.*']) ? 'true' : 'false' }}">
                            <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs(['depreciation.*']) ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-white group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v5m0 0 2-2m-2 2-2-2M3 6v1a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1Zm2 2v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8H5Z"/>
                            </svg>
                            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap menu-text">Depreciation</span>
                            <svg class="w-3 h-3 arrow-icon transition-transform duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                            </svg>
                        </button>
                        <ul id="dropdown-depre" class="{{ request()->routeIs(['depreciation.*']) ? '' : 'hidden' }} py-2 space-y-2">
                            <li>
                                <a href="{{ route('depreciation.index') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text {{ request()->routeIs('depreciation.*') ? 'bg-gray-100 dark:bg-gray-700' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">Commercial</a>
                            </li>
                            <li>
                                <a href="#" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 menu-text">Fiscal</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('company-user.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('company-user.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('company-user.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-width="2" d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text">Users</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('person-in-charge.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('person-in-charge.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white {{ request()->routeIs('person-in-charge.*') ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7141 15h4.268c.4043 0 .732-.3838.732-.8571V3.85714c0-.47338-.3277-.85714-.732-.85714H6.71411c-.55228 0-1 .44772-1 1v4m10.99999 7v-3h3v3h-3Zm-3 6H6.71411c-.55228 0-1-.4477-1-1 0-1.6569 1.34315-3 3-3h2.99999c1.6569 0 3 1.3431 3 3 0 .5523-.4477 1-1 1Zm-1-9.5c0 1.3807-1.1193 2.5-2.5 2.5s-2.49999-1.1193-2.49999-2.5S8.8334 9 10.2141 9s2.5 1.1193 2.5 2.5Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text">Person In Charge</span>
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

                    <!-- <li>
                        <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                            <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2h1a1 1 0 1 0 0-2h-2a1 1 0 0 0-1 1v2H9.89A6.977 6.977 0 0 1 12 8v5h-2V8A5 5 0 1 0 0 8v6a1 1 0 0 0 1 1h8v4a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-4h6a1 1 0 0 0 1-1V8a5 5 0 0 0-2.582-4.377ZM6 12H4a1 1 0 0 1 0-2h2a1 1 0 0 1 0 2Z"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap menu-text">Inbox</span>
                            <span class="inline-flex items-center justify-center w-3 h-3 p-3 ms-3 text-sm font-medium text-blue-800 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300 menu-text">3</span>
                        </a>
                    </li> -->

                </ul>
                <div class="mt-auto">
                    <button id="sidebar-toggle" class="w-full flex items-center justify-center p-2 text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 group">
                        <svg id="sidebar-toggle-icon" class="w-6 h-6 transition-transform" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/></svg>
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
            
            if (sidebar && mainContent && sidebarToggleBtn && sidebarToggleIcon) {
                function applySidebarState(isCollapsed) {
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                        sidebar.style.width = '4.5rem'; // 72px

                        if (window.innerWidth >= 640) {
                            mainContent.style.marginLeft = '4.5rem';
                        }
                        sidebarToggleIcon.style.transform = 'rotate(180deg)';
                    } else {
                        sidebar.classList.remove('collapsed');
                        sidebar.style.width = '16rem'; // 256px
                        if (window.innerWidth >= 640) {
                            mainContent.style.marginLeft = '16rem';
                        }
                        sidebarToggleIcon.style.transform = 'rotate(0deg)';
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
            }
        });
    </script>
    @stack('scripts')
</body>
</html>