<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Get in Style</title>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('limitless/assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('limitless/assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('limitless/assets/css/core.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('limitless/assets/css/components.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('limitless/assets/css/colors.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ URL::asset('limitless/assets/css/customcss.css') }}" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    @stack('css')

</head>

<body class="navbar-bottom">

@include('shared.header')

<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">

    @include('shared.sidebar')

    <!-- Main content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
<!-- /page container -->

@include('shared.footer')

<!-- Core JS files -->
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/plugins/loaders/pace.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/bootstrap.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/plugins/loaders/blockui.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/plugins/ui/nicescroll.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/plugins/ui/drilldown.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/plugins/ui/fab.min.js') }}"></script>
<!-- /core JS files -->

<!-- Theme JS files -->
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/plugins/ui/prism.min.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/app.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/pages/components_popups.js') }}"></script>
<!-- /theme JS files -->

<script type="text/javascript" src="{{ URL::asset('limitless/assets/js/customjs.js') }}"></script>

@stack('js')

</body>
</html>
