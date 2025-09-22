<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management - Under Maintenance</title>
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
            <img src="{{ asset('images/maintenance.webp') }}" class="max-w-xs md:max-w-lg mb-8" alt="Under Maintenance Illustration" />
            <h1 class="font-bold text-3xl md:text-5xl text-gray-800 mb-2">Oops! Site is under maintenance</h1>
            <p class="text-gray-500 text-lg">We are working hard to improve your experience. Please check back later.</p>
        </div>
    </div>
</body>
</html>
