<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nông Sản Sạch')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-green-50 text-gray-800">
    <x-header />
    <main>
        @yield('content')
    </main>
    <x-footer />

    <x-toast />
    <x-session-modal />
</body>
</html>
