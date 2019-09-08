<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="application-name" content="{{ settings('app_name') }}"/>
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta name="msapplication-TileImage" content="{{ url('assets/img/icons/mstile-144x144.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{--///Title , Description, Title meta tags--}}
    <meta name="p:domain_verify" content="00f58534223ca7b047e93281b0344936"/>
    <meta name="description" content="best price finder"/>
    <meta name="keywords" content="Women Clothing,male Clothing,Cell phone mobile,Computer,Consumer Electronics,Jewelry,Home,Luggage Bag,Shoes,kids,sport,
Health,Watches,Toys,Wedding,Novelty,Automobile,Lighting,Furniture,Electronic Supplies,School Supplies,
Home Appliances,Home Improvement,Security protection,Tools,Hair Extensions,dress,blouse,woman Sweatshirt,woman Sweater,
woman coat,woman accessories,Bottom,romper,top tee,Intimates,Jumpsuits,Bodysuit,clothing set,female socks,
female sleep lounge,Hoodies & Sweatshirts,male top,male coat,pants male,male shirts,jeans male,male shorts,
male wallet,man Sweater,Suits & Blazers, ,male casual,male Shorts,men socks,Board Shorts,Mobile cell Phone,
Phone Bag,Power Bank,Mobile Phone Accessories,Mobile Phone Parts,Communication Equipment,Office Electronics,Tablet,
Computer components,Tablet accessories,Computer peripherals,External Storage,Networking,Memory Card,computer cable,
computer accessories,computer desktop,Industrial Computer,DIY Gaming computer,Computer Cleaner,Notebook,
Laptop Accessories,Gaming Laptop,digital photo camera,Portable Audio video,Home Audio Video,Smart Electronics,
Electronics Accessories Parts,Electronic Cigarettes,Video Games,Earphones Headphones,Wearable Devices,
VR AR Devices,Sports Action Video Cameras,360 degree Video Accessories,Home Electronic,Speakers,"/>
    <meta name="robots" content="index,follow"/>
    <meta name="title" content="best price finder"/>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-80119832-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-80119832-2');
    </script>
    {{--///Title , Description, Title meta tags--}}

    <title>@yield('page-title') | {{ settings('app_name') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ url('assets/img/icons/apple-touch-icon-144x144.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ url('assets/img/icons/apple-touch-icon-152x152.png') }}" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-16x16.png') }}" sizes="16x16" />

    {{-- For production, it is recommended to combine following styles into one. --}}
    {!! HTML::style('assets/css/bootstrap.min.css') !!}
    {!! HTML::style('assets/css/font-awesome.min.css') !!}
    {!! HTML::style('assets/css/metisMenu.css') !!}
    {!! HTML::style('assets/css/sweetalert.css') !!}
    {!! HTML::style('assets/css/bootstrap-social.css') !!}
    {!! HTML::style('assets/css/app.css') !!}
    {!! HTML::style('assets/css/backend.css') !!}

    {{--highlighter for image--}}
    {!! HTML::style('assets/plugins/featherlight/featherlight.css') !!}
    {{--Javascript cropper for image--}}
    {!! HTML::style('assets/plugins/cropperjs/cropper.css') !!}

    @yield('styles')
</head>
<body>

<div id="pageloader">
    <span class="loader"><span class="loader-inner"></span></span>
</div>

    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('dashboard') }}" style="padding: 7px 0 0 0;">
                    <img src="{{ url('assets/img/bastaprice-logo.png') }}" height="33" alt="{{ settings('app_name') }}">
                </a>
            </div>
            <div id="navbar" class="navbar-collapse">
                <a href="#" id="sidebar-toggle" class="btn">
                    <i class="navbar-icon fa fa-bars icon"></i>
                </a>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle avatar" data-toggle="dropdown">
                            <img alt="image" class="img-circle avatar" src="{{ Auth::user()->present()->avatar }}">
                            {{ Auth::user()->present()->name }}
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('profile') }}">
                                    <i class="fa fa-user"></i>
                                    @lang('app.my_profile')
                                </a>
                            </li>
                            @if (config('session.driver') == 'database')
                                <li>
                                    <a href="{{ route('profile.sessions') }}">
                                        <i class="fa fa-list"></i>
                                        @lang('app.active_sessions')
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('auth.logout') }}">
                                    <i class="fa fa-sign-out"></i>
                                    @lang('app.logout')
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    @include('partials.sidebar')

    <div id="page-wrapper">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    {{-- For production, it is recommended to combine following scripts into one. --}}
    {!! HTML::script('assets/js/jquery-2.1.4.min.js') !!}
    {!! HTML::script('assets/js/bootstrap.min.js') !!}
    {!! HTML::script('assets/js/metisMenu.min.js') !!}
    {!! HTML::script('assets/js/sweetalert.min.js') !!}
    {!! HTML::script('assets/js/delete.handler.js') !!}
    {!! HTML::script('assets/plugins/js-cookie/js.cookie.js') !!}

    {{--highlighter for image--}}
    {!! HTML::script('assets/plugins/featherlight/featherlight.js') !!}
    {{--Javascript croper for image--}}
    {!! HTML::script('assets/plugins/cropperjs/cropper.js') !!}

    <script type="text/javascript">
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var tokenStr = "{{ csrf_token() }}";
        var siteURL = "{{ url('') }}";

        setTimeout(function () {
            $('#pageloader').fadeOut('slow');
        }, 1000);

    </script>
    {!! HTML::script('vendor/jsvalidation/js/jsvalidation.js') !!}
    {!! HTML::script('assets/js/as/app.js') !!}
    @yield('scripts')
</body>
</html>