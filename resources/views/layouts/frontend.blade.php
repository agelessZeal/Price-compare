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

    <meta name="robots" content="index,follow"/>
    <meta name="title" content="@yield('product-title')"/>
    <meta name="description" content="@yield('product-description')"/>
    <meta name="keywords" content="@yield('product-keywords')"/>
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
    {!! HTML::style('assets/css/front.css') !!}

    {{--start rating jquery plugin--}}
    {{--{!! HTML::style('assets/plugins/ffrating/styles/ffrating.css') !!}--}}

    {{--highlighter for image--}}
    {!! HTML::style('assets/plugins/featherlight/featherlight.css') !!}

    {{--Javascript cropper for image--}}
    {!! HTML::style('assets/plugins/cropperjs/cropper.css') !!}

    {{--Javascript cropper for image--}}
    {!! HTML::style('assets/plugins/lobibox-notify/css/lobibox.min.css') !!}

    {{--Javascript Pagination Plugin--}}
    {!! HTML::style('assets/plugins/simplePagination/simplePagination.css') !!}

    {!! HTML::style('assets/plugins/choosen/style.css') !!}

    {{--<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">--}}

    @yield('styles')
</head>
<body>
    <div id="pageloader" style="/* display: none; */">
        <span class="loader"><span class="loader-inner"></span></span>
    </div>
    <div class="header">
        <nav class="navbar">
            <div class="container">
                <div class="row">
                    <div class="col-md-2 col-sm-6 col-xs-6">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="{{ route('dashboard') }}">
                                <img src="{{url('assets/img/bastaprice-menu-logo.png')}}" alt="Bastaprice">
                            </a>
                        </div>
                    </div>
                    <div class="navbar-collapse" id="navbar3">
                        <ul class="nav navbar-nav navbar-right">
                            @if (Auth::check())
                                <li><a href="{{ route('auth.logout') }}">@lang('app.logout')</a></li>
                                <li><a href="{{ route('favorite.profile') }}">@lang('app.profile')&nbsp;&nbsp;|</a></li>
                            @else
                                <li><a href="{{ url('/login') }}">@lang('app.login')</a></li>
                            @endif
                            <li><a href="{{ route('favorite') }}">@lang('app.favorite')&nbsp;&nbsp;|</a></li>
                            {{--<li><a href="{{ url('/') }}">@lang('app.home')&nbsp;&nbsp;|</a></li>--}}
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6" style="z-index: 10">
                    <button type="button" class="btn btn-primary input-lg category-part" id="view-all-category">
                         <svg-icon><src href="{{ url('assets/img/svg/si-glyph-dial-number.svg')}}"/></svg-icon>
                        &nbsp;<span>@lang('app.categories')</span>
                    </button>
                </div>
                <div class="col-md-9 col-sm-6">
                    <?php $keyword = Input::get('keyword');?>
                    <?php $cur_ctg = Input::get('category');?>
                    {!! Form::open(['route' => 'product.search', 'files' => false, 'id' => 'product-search-form','method'=>'GET']) !!}
                        <div class="input-group stylish-input-group item-search-box">
                            <input type="hidden" name=category value="{{ ($cur_ctg)? $cur_ctg:""}}">
                            <input type="text" name="keyword" class="form-control input-lg"  placeholder="@lang('app.what_you_would_like_to_find_today')">
                            <span class="input-group-addon">
                                <button type="submit">
                                    <img src="{{ url('assets/img/svg/si-glyph-magnifier.svg')}}">
                                </button>
                            </span>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="category-section" >
            <div class="container">
                <div class="category-section-container category-part" style="display: none">
                    <div class="row">
                        <?php $parent_ctg_order = 0;?>
                        @foreach ($parentCategories as $key=>$categorySet)
                            <?php $parent_ctg_order++;?>
                            @if ($parent_ctg_order<13)
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="category-item">
                                        <div class="row">
                                            <div class="col-md-2 col-sm-3 col-xs-2" style="text-align: right">
                                                <svg-icon><src href="{{ url('assets/img/svg/'.$categorySet[0]['svg'].'.svg')}}"/></svg-icon>
                                            </div>
                                            <div class="col-md-10 col-sm-9 col-xs-10">
                                                <h4 class="category-item-title" data-keyword="{{ trim($categorySet[0]['parent_keyword'])}}">{{ trim($categorySet[0]['parent'])}}</h4>
                                                <p>
                                                    <?php
                                                    foreach ($categorySet as $index=>$item):
                                                        if ($index<4){
                                                            if($index==0){
                                                                echo '<span class="category-sub-item" data-keyword="'.trim($item['sub_keyword']).'">'.trim($item['sub']).'</span>';
                                                            }else{
                                                                echo ', <span class="category-sub-item" data-keyword="'.trim($item['sub_keyword']).'">'.trim($item['sub']).'</span>';
                                                            }
                                                        }else{
                                                            echo '<span class="category-sub-item" style="display: none">'.trim($item['sub']).'</span>';
                                                        }
                                                    endforeach;
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                    </div>
                    <hr>
                    <div class="row" style="text-align: center;">
                        <a href="{{ route('view.more.category') }}" id="view-more-categories-link">@lang('app.view_all_categories')</a>
                    </div>
                </div>
            </div>
        </div>
        @yield('content')
        <div class="featherlight" style="display: none;">
            <div class="featherlight-content">
                <button class="featherlight-close-icon featherlight-close" aria-label="Close">✕</button>
                <img src="" alt="" class="featherlight-image featherlight-inner">
            </div>
        </div>
    </div>
    <div class="category-back-drop"></div>

    <div class="container" style="text-align: center;margin-bottom: 50px;margin-top: 50px;z-index: -10">
        <ul class="pagination" id="pagination"></ul>
    </div>

    {{-- For production, it is recommended to combine following scripts into one. --}}
    {!! HTML::script('assets/js/jquery-2.1.4.min.js') !!}
    {!! HTML::script('assets/js/bootstrap.min.js') !!}

    {!! HTML::script('assets/js/iconwc.js') !!}

    {{--My Javascript Functions--}}
    {!! HTML::script('assets/js/my-lib.js') !!}

    {{--Star Rating Javascript Plugin--}}
    {{--{!! HTML::script('assets/plugins/ffrating/scripts/ffrating.js') !!}--}}

    {{--highlighter for image--}}
    {!! HTML::script('assets/plugins/featherlight/featherlight.js') !!}

    {{--Javascript croper for image--}}
    {!! HTML::script('assets/plugins/cropperjs/cropper.js') !!}

    {{--TWS Pagination Javascript Library--}}
    {{--{!! HTML::script('assets/plugins/paginationjs/jquery.twbsPagination.js') !!}--}}

    {{--TWS Pagination Javascript Library--}}
    {!! HTML::script('assets/plugins/simplePagination/jquery.simplePagination.js') !!}

    {{--Nofify Lobibox Javascript Library--}}
    {!! HTML::script('assets/plugins/lobibox-notify/js/lobibox.min.js') !!}

    {!! HTML::script('assets/plugins/choosen/choosen.js') !!}

    {!! HTML::script('assets/js/metisMenu.min.js') !!}
    {!! HTML::script('assets/js/sweetalert.min.js') !!}
    {!! HTML::script('assets/js/delete.handler.js') !!}
    {!! HTML::script('assets/plugins/js-cookie/js.cookie.js') !!}

    <script type="text/javascript">
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var tokenStr = "{{ csrf_token() }}";
        var siteURL = "{{ url('').'/' }}";
        var ajaxProductCompare = "{{ route('favorite.add.product') }}";
        var productSearchURL  ="{{route('product.search')}}";
        var productViewURL = "{{route('product.view.detail')}}";
        var ajaxFavProductDeleteURL = "{{route('favorite.delete.product')}}";
        var favroitePageURL = "{{route('favorite')}}";
        var ajaxStoreRelatedProduct = "{{route('product.store.related')}}";

        var isLogin = "{{Auth::check()}}";
        var curPageName = "";

        setTimeout(function () {
            $('#pageloader').fadeOut('slow');
        }, 500);

    </script>
    {!! HTML::script('assets/js/local-product-manage.js') !!}
    {!! HTML::script('assets/js/product-manage.js') !!}
    {!! HTML::script('assets/js/page-manage.js') !!}

    {!! HTML::script('vendor/jsvalidation/js/jsvalidation.js') !!}
    {!! HTML::script('assets/js/as/app.js') !!}
    @yield('scripts')
</body>
</html>