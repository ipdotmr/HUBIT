<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sidenav-size="compact">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Osen CSS -->
        <link href="{{ asset('osen-vendor/simplebar/simplebar.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('osen-css/vendor.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('osen-css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('osen-css/app.min.css') }}" rel="stylesheet" type="text/css" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <!-- Osen JS -->
        <script src="{{ asset('osen-vendor/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('osen-js/vendor.min.js') }}"></script>
        <script src="{{ asset('osen-js/app.js') }}"></script>
    </body>
</html>
