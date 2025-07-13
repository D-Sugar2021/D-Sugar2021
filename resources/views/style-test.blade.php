<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Style Test</title>

    <!-- The same Vite directive from app.blade.php -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-200 p-8">

    <h1 class="text-4xl font-bold text-blue-600 mb-4">Style Test Page</h1>

    <p class="text-gray-800">If this text is styled, then your Vite asset compilation is working.</p>

    <div class="mt-8 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-semibold">This is a styled card.</h2>
        <p class="mt-2 text-gray-600">It should have a white background, padding, a shadow, and rounded corners.</p>
        <button class="mt-4 px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-700">
            Styled Button
        </button>
    </div>

</body>
</html>
