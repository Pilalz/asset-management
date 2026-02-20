<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asset Details - {{ $asset->asset_number }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-300 h-screen overflow-hidden flex flex-col">
    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center py-4 sm:py-6 md:py-8 px-4 sm:px-6 md:px-8 transition-colors duration-300 overflow-y-auto">
        <div class="w-full max-w-4xl bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden ring-1 ring-gray-900/5 dark:ring-white/10 transition-colors duration-300 my-auto">
            <!-- Header Section -->
            <div class="relative bg-gradient-to-br from-indigo-600 via-blue-600 to-blue-700 py-4 px-6 md:py-8 sm:px-12 dark:from-indigo-900 dark:via-blue-900 dark:to-blue-950">
                <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-soft-light"></div>
                <div class="relative flex flex-col sm:flex-row justify-between items-center gap-2 md:gap-6">
                    <div class="text-center sm:text-left space-y-2">
                        <span class="hidden md:inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-blue-50 tracking-wider uppercase backdrop-blur-sm border border-white/10">Asset Detail</span>
                        <h1 class="text-xl md:text-3xl font-extrabold text-white tracking-tight drop-shadow-sm">Asset Information</h1>
                        <p class="hidden md:block text-blue-100 font-medium max-w-lg leading-relaxed text-base">Complete details of asset specifications and location.</p>
                    </div>

                    <!-- Company Logo/Name -->
                    <div class="flex flex-col items-center bg-white/10 backdrop-blur-md rounded-2xl p-2 md:p-4 border border-white/20 shadow-lg min-w-[180px] transform hover:scale-105 transition-transform duration-300">
                        @if($asset->company->logo)
                            <div class="bg-white p-2 rounded-xl shadow-sm mb-2 w-full flex justify-center">
                                <img class="h-12 w-auto object-contain" src="{{ Storage::url($asset->company->logo) }}" alt="{{ $asset->company->name }}">
                            </div>
                        @else
                            <div class="h-16 w-16 bg-indigo-500 rounded-full flex items-center justify-center mb-2 shadow-inner ring-4 ring-white/20">
                                <span class="text-white text-2xl font-bold">{{ substr($asset->company->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span class="font-bold text-white text-xs tracking-widest uppercase text-center border-t border-white/20 pt-2 w-full block mt-1">{{ $asset->company->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Content Body -->
            <div class="px-6 py-6 md:py-8 sm:px-12 bg-white dark:bg-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-12">
                    <!-- Left Column: Core Info -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="hidden md:flex items-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                                <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                                Asset Identification
                            </h3>
                            <dl class="space-y-4">
                                <div class="group">
                                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Asset Number</dt>
                                    <dd class="text-sm md:text-base font-bold text-gray-900 dark:text-white font-mono bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded-lg border border-gray-100 dark:border-gray-700 group-hover:border-indigo-200 dark:group-hover:border-indigo-800 transition-colors">
                                        {{ $asset->asset_number }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Asset Name</dt>
                                    <dd class="text-sm md:text-base font-medium text-gray-900 dark:text-white">{{ $asset->assetName->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Model / Description</dt>
                                    <dd class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg">
                                        {{ $asset->description }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Right Column: Location & Purchase -->
                    <div class="space-y-6 lg:border-l lg:border-gray-100 lg:dark:border-gray-700 lg:pl-8">
                        <div>
                            <h3 class="hidden md:flex items-center text-lg font-bold text-gray-900 dark:text-white mb-4">
                                <span
                                    class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 p-2 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </span>
                                Location & Department
                            </h3>
                            <div class="space-y-4">
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
                                    <dt class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Current Location</dt>
                                    <dd class="flex items-center text-sm md:text-base font-semibold text-gray-900 dark:text-white">
                                        <span class="w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                                        {{ $asset->location->name }}
                                    </dd>
                                </div>

                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700 p-4 shadow-sm hover:shadow-md transition-shadow">
                                    <dt class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Owning Department</dt>
                                    <dd class="flex items-center text-sm md:text-base font-semibold text-gray-900 dark:text-white">
                                        <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $asset->department->name }}
                                    </dd>
                                </div>

                                <div class="mt-0 md:mt-3 pt-0 md:pt-4 border-t border-transparent dark:border-transparent md:border-gray-100 dark:border-gray-700">
                                    <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Purchase Order (PO)</dt>
                                    <dd class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        <svg class="w-3.5 h-3.5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $asset->po_no }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action -->
            <div class="px-6 py-5 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 hidden items-center md:flex justify-center sm:justify-end">
                <a href="{{ route('asset.show', $asset->id) }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2 -ml-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M10 7v6m-3-3h6m4 0a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                    </svg>
                    Go to Detail
                </a>
            </div>
        </div>

        <!-- Theme Setting -->
        <div class="fixed bottom-2 right-2 flex items-center">
            <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5 transition-colors duration-200">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <!-- Copyright / Footer -->
        <div
            class="fixed bottom-2 text-center w-full pointer-events-none opacity-50 hover:opacity-100 transition-opacity">
            <p class="text-[10px] text-gray-400 dark:text-gray-600">&copy; {{ date('Y') }} Asset Management System</p>
        </div>
    </div>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            function applyTheme(theme) {
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
                    if (themeToggleLightIcon) themeToggleLightIcon.classList.add('hidden');
                } else {
                    document.documentElement.classList.remove('dark');
                    if (themeToggleDarkIcon) themeToggleDarkIcon.classList.add('hidden');
                    if (themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
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

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    const isDark = document.documentElement.classList.toggle('dark');
                    const newTheme = isDark ? 'dark' : 'light';
                    localStorage.setItem('color-theme', newTheme);
                    applyTheme(newTheme);
                });
            }
        });
    </script>
</body>

</html>