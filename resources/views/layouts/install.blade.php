<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Installation | {{ settings('app_name') }}</title>

    {!! HTML::style('assets/css/bootstrap.min.css') !!}
    {!! HTML::style('assets/css/font-awesome.min.css') !!}
    {!! HTML::style('assets/css/sweetalert.css') !!}
    {!! HTML::style('assets/css/install.css') !!}

    @yield('styles')
</head>
<body>

    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 col-md-offset-3 logo-wrapper">
                    <img src="{{ url('assets/img/vanguard-logo.png') }}" alt="Vanguard" class="logo">
                </div>
            </div>
            <div class="wizard col-md-6 col-md-offset-3">
                @yield('content')
            </div>
        </div>
    </div>

    <script type="text/javascript" src="{{ url("assets/js/jquery-2.1.4.min.js") }}"></script>
    <script type="text/javascript" src="{{ url("assets/js/bootstrap.min.js") }}"></script>
    <script type="text/javascript" src="{{ url("assets/js/sweetalert.min.js") }}"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        var as = {};
    </script>
    <script type="text/javascript" src="{{ url('assets/js/as/btn.js') }}"></script>
    <script type="text/javascript" src="{{ url('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    <script>
        $("a[data-toggle=loader], button[data-toggle=loader]").click(function () {
            if ($(this).parents('form').valid()) {
                as.btn.loading($(this), $(this).data('loading-text'));
                $(this).parents('form').submit();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
