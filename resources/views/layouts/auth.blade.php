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
Electronics Accessories Parts,Electronic Cigarettes,Video Games,Earphones Headphones,Wearable Devices,"/>
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
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ url('assets/img/icons/apple-touch-icon-144x144.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ url('assets/img/icons/apple-touch-icon-152x152.png') }}" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ url('assets/img/icons/favicon-16x16.png') }}" sizes="16x16" />

    {!! HTML::style("assets/css/bootstrap.min.css") !!}
    {!! HTML::style("assets/css/font-awesome.min.css") !!}
    {!! HTML::style("assets/css/app.css") !!}
    {!! HTML::style("assets/css/bootstrap-social.css") !!}

    @yield('header-scripts')
</head>
<body class="auth">

    <div class="container">

        @yield('content')

        <footer id="footer">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <p>@lang('app.copyright') © - {{ settings('app_name') }} {{ date('Y') }}</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {!! HTML::script('assets/js/jquery-2.1.4.min.js') !!}
    {!! HTML::script('assets/js/bootstrap.min.js') !!}
    {!! HTML::script('vendor/jsvalidation/js/jsvalidation.js') !!}
    {!! HTML::script('assets/js/as/app.js') !!}
    {!! HTML::script('assets/js/as/btn.js') !!}

    @yield('scripts')
</body>
</html>
