<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Data Analytics Agent')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>

<body class="h-screen flex text-gray-900 overflow-hidden bg-[#f0f2f5]">
    @include('components.sidebar')

    <div class="flex-1 flex flex-col h-full relative overflow-hidden" id="main-content-area">
        @include('components.header-mobile')
        @yield('content')
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-20 hidden md:hidden"></div>
    <div id="toast-container" class="fixed top-6 right-6 z-50 flex flex-col gap-3"></div>

    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>
