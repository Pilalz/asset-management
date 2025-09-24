<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management - Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <div class="flex items-center justify-center min-h-screen">
        <div class="flex flex-col items-center text-center p-8">
            <img src="{{ asset('images/403-gif.gif') }}" class="max-w-xs md:max-w-lg mb-8" alt="Under Maintenance Illustration" />
            <h1 class="font-bold text-3xl md:text-5xl text-gray-800 mb-2">Access Denied</h1>
            <p class="text-gray-500 text-lg">Sorry, but you don't have permission to access this page.</p>
            <span class="text-gray-500">You can go back to 
                <button onclick="window.history.back()" type="button" class="hover:underline hover:text-black">
                    previous page
                </button>
            </span>
        </div>
    </div>
</body>
</html>
