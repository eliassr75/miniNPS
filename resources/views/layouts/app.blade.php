<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>@yield('title', 'Título Padrão')</title>
    <meta name="description" content="miniNPS">
    <meta name="keywords" content="bootstrap, NPS, miniNPS, Mobile Survey, survey, questions nps, mobile, html, responsive" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="manifest" href="{{ asset('__manifest.json') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://unpkg.com/tabulator-tables@6.2.5/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.2.5/dist/js/tabulator.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.bootstrap5.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.dataTables.css" />
    <script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.dataTables.js"></script>
    <script src="{{ asset('assets/js/functions.js') }}"></script>

    @stack('styles')
</head>

<body>

<!-- loader -->
<div id="loader">
    <img src="{{ asset('assets/img/loading-icon.png') }}" alt="icon" class="loading-icon">
</div>
<!-- * loader -->

@auth
    @include('components.app_header')
@endauth

<div id="dialog-container"></div>

<!-- App Capsule -->
<div id="appCapsule">


    @yield('content')
    {{--@include('components.app_footer')--}}

</div>
<!-- * App Capsule -->

@auth
    @include('components.app_bottom_menu')
@endauth

@include('components.app_sidebar')
@include('components.app_add_home')
@include('components.app_cookies')

<!-- ========= JS Files =========  -->
<!-- Bootstrap -->
<script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<!-- Splide -->
<script src="{{ asset('assets/js/plugins/splide/splide.min.js') }}"></script>
<!-- Base Js File -->
<script src="{{ asset('assets/js/base.js') }}"></script>

@stack('scripts')

<script>
    // Add to Home with 2 seconds delay.
    AddtoHome("2000", "once");
</script>

</body>

</html>
