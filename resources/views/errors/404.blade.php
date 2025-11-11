<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management - Not Found</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <div class="flex justify-center min-h-screen dark:bg-black">
        <div class="flex flex-col text-center">
            <img src="{{ asset('images/404.svg') }}" class="max-w-xs md:max-w-lg mb-8 dark:invert" alt="Under Maintenance Illustration" />
            <h1 class="font-bold text-3xl md:text-5xl text-gray-800 mb-2 dark:text-gray-100">Page not found</h1>
            <p class="text-gray-500 text-lg dark:text-gray-300">We've looked everywhere but couldn't find the page you were looking for.</p>
            <button onclick="window.history.back()" type="button" class="hover:underline hover:text-black dark:text-gray-300 dark:hover:text-white">
                Go to previous page
            </button>
        </div>
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
</body>
</html>
