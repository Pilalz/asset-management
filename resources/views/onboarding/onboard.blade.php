<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col h-screen bg-gray-100 dark:bg-gray-900">
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start rtl:justify-end">
                    <a href="{{ route('onboard.index') }}" class="flex ms-2 md:me-24 dark:hidden">
                        <img src="{{ asset('images/logo.svg') }}" class="h-8 me-3" alt="Asset Management Logo" />
                        <span class="hidden sm:block self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Asset Management</span>
                    </a>
                    <a href="{{ route('onboard.index') }}" class="hidden ms-2 md:me-24 dark:flex">
                        <img src="{{ asset('images/logo-dark.svg') }}" class="h-8 me-3" alt="Asset Management Logo" />
                        <span class="hidden sm:block self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">Asset Management</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3 gap-4">

                        <!-- Tombol utama dropdown -->
                        <button type="button" aria-expanded="false" data-dropdown-toggle="dropdown-company" data-dropdown-placement="bottom-end"
                            class="rounded-md text-black hover:bg-gray-100 font-medium text-sm px-2.5 py-2.5 text-center dark:text-white dark:hover:bg-gray-700 focus:bg-gray-100 dark:focus:bg-gray-700">
                            <div class="inline-flex items-center">
                                {{ $activeCompany?->name ?? 'Choose Company' }}
                                <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2 text-black dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
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
                                @if($activeCompany == null)
                                    <li>
                                        <p class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors">Anda belum tergabung dalam company apapun</p>
                                    </li>
                                @endif
                                @if($activeCompany)
                                    <li>
                                        <a href="{{ route('company.edit', ['company' => $activeCompany->id]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors" role="menuitem">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-white transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z"/>
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                                </svg>
                                                Setting Company
                                            </div>
                                        </a>
                                    </li>
                                @endif
                                @if(isset($userCompanies) && $userCompanies->count() >= 1)
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
                        <button id="theme-toggle" type="button" class="hidden sm:block text-gray-800 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                            <svg id="theme-toggle-dark-icon" class="hidden w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M11.675 2.015a.998.998 0 0 0-.403.011C6.09 2.4 2 6.722 2 12c0 5.523 4.477 10 10 10 4.356 0 8.058-2.784 9.43-6.667a1 1 0 0 0-1.02-1.33c-.08.006-.105.005-.127.005h-.001l-.028-.002A5.227 5.227 0 0 0 20 14a8 8 0 0 1-8-8c0-.952.121-1.752.404-2.558a.996.996 0 0 0 .096-.428V3a1 1 0 0 0-.825-.985Z" clip-rule="evenodd"/>
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                              <path fill-rule="evenodd" d="M13 3a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V3ZM6.343 4.929A1 1 0 0 0 4.93 6.343l1.414 1.414a1 1 0 0 0 1.414-1.414L6.343 4.929Zm12.728 1.414a1 1 0 0 0-1.414-1.414l-1.414 1.414a1 1 0 0 0 1.414 1.414l1.414-1.414ZM12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10Zm-9 4a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H3Zm16 0a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2h-2ZM7.757 17.657a1 1 0 1 0-1.414-1.414l-1.414 1.414a1 1 0 1 0 1.414 1.414l1.414-1.414Zm9.9-1.414a1 1 0 0 0-1.414 1.414l1.414 1.414a1 1 0 0 0 1.414-1.414l-1.414-1.414ZM13 19a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        @can('is-dev')
                            <div>
                                <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:bg-gray-500" aria-expanded="false" data-dropdown-toggle="dropdown-action" data-dropdown-placement="bottom-end">
                                    <div class="flex justify-center items-center w-8 h-8 rounded-full">
                                        <svg class="w-[24px] h-[24px] text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5"/>
                                        </svg>
                                    </div>
                                </button>
                            </div>
                            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-56" id="dropdown-action">
                                <ul class="py-1" role="none">
                                    <li>
                                        <a href="{{ route('onboard.create') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors" role="menuitem">
                                            <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-green-600 dark:text-gray-500 dark:group-hover:text-green-400 transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                            </svg>
                                            Create Company
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endcan

                        <div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user" data-dropdown-placement="bottom-end">
                                <span class="sr-only">Open user menu</span>
                                
                                <img class="w-8 h-8 rounded-full" src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random' }}" alt="user photo">
                            </button>
                        </div>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-xl shadow-lg border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:divide-gray-700 w-64" id="dropdown-user">
                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-t-xl" role="none">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white" role="none">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs font-medium text-gray-500 truncate dark:text-gray-400" role="none">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                            <ul class="py-1" role="none">
                                <li>
                                    <a href="{{ route('onboard.edit') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white transition-colors" role="menuitem">
                                        <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-indigo-600 dark:text-gray-500 dark:group-hover:text-white transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"/>
                                        </svg>
                                        Settings
                                    </a>
                                </li>
                                <li>
                                    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                                        @csrf
                                    </form>
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="group flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 dark:hover:text-red-300 transition-colors" role="menuitem">
                                        <svg class="w-4 h-4 mr-3 text-red-400 group-hover:text-red-600 dark:text-red-500 dark:group-hover:text-red-400 transition-colors" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
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
        <main class="size-full p-5 text-center dark:text-white">
            <h1>Anda belum memiliki atau tergabung dengan suatu perusahaan</h1>
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
        });
    </script>
    @stack('scripts')
</body>
</html>