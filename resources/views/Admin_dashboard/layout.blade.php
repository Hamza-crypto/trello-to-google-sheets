<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Admin Dashboard </title>

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Bootstrap Docs -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap_docs.css') }}" type="text/css">

    <!-- Slick -->
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>

    <!-- Main style file -->
    <link rel="stylesheet" href="{{ asset('assets/css/app_min.css') }}" type="text/css">
    <!-- Add this style section in the head of your HTML file -->

</head>

<body>

    <!-- preloader -->
    <div class="preloader">
        <div class="preloader-icon"></div>
    </div>
    <!-- ./ preloader -->

    @include('Admin_dashboard.includes.main.Sidebar')

    <div class="layout-wrapper">

        @include('Admin_dashboard.includes.main.Header')

        @yield('contents')

        @include('Admin_dashboard.includes.main.Footer')

    </div>

    {{-- Bundle scripts --}}
    <script src="{{ asset('assets/js/bundle.js') }}"></script>

    {{-- Apex chart --}}
    <script src="{{ asset('assets/js/apexcharts_min.js') }}"></script>

    {{-- Slick --}}
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    {{-- Examples --}}
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>

    {{-- Main Javascript file --}}
    <script src="{{ asset('assets/js/app_min.js') }}"></script>
</body>

</html>
