<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- other metas -->
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-signin-client_id" content="531698814418-uecn9t2d3653ikp0bj7jukb4chk8brei.apps.googleusercontent.com">
    <title>@yield('page_title') Party People</title>


    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{asset('css/font-awesome.min.css')}}">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ mix('css/all.css')}}" rel="stylesheet">
    <link href="{{asset('css/styles.css')}}" rel="stylesheet">
    <link href="{{asset('css/mobile.css')}}" rel="stylesheet" media="screen and (max-width:750px)">
    @stack('page_css')
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body class="@yield('body_class')" id="body" style="@yield('body_style')">
<div class="flex-center position-ref full-height">


    <div @if (isset($vue)) id="app" @endif class="content">
        @yield('body')

        @stack('modals')
    </div>
</div>

@stack('o_modals')
@include('partials.global_footer')
<script>
function openNav() {
    document.getElementById("sidebar").style.width = "75%";
    document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
}

function closeNav() {
    document.getElementById("sidebar").style.width = "0";
    document.body.style.backgroundColor = "white";
}
</script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script type="text/javascript" src="{{ mix('js/app.js') }}"></script>
<script type="text/javascript" src="{{ mix('assets/js/app.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/scripts.js') }}"></script>
@if (isset($maps))
    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{config('maps.google_key')}}&sensor=false&libraries=places"></script>
@endif

@if(isset($charts))
    @if(!isset($highmaps))
        <script src="{{asset('plugins/highcharts/highcharts.js')}}"></script>
        <script src="{{asset('plugins/highcharts/highcharts-more.js')}}"></script>
        <script src="{{asset('plugins/highcharts/modules/exporting.js')}}"></script>
        <script src="{{asset('plugins/highcharts/modules/offline-exporting.js')}}"></script>
    @endif

    @if(isset($highmaps))
        <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.3.6/proj4.js"></script>
        <script src="https://code.highcharts.com/maps/highmaps.js"></script>
        <script src="https://code.highcharts.com/maps/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/mapdata/countries/ke/ke-all.js"></script>
    @endif
@endif

@stack('page_js')
@stack('page_scripts')
</body>
</html>
