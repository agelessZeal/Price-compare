@extends('layouts.app')

@section('page-title', trans('app.dashboard'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            @lang('app.welcome') <?= Auth::user()->username ?: Auth::user()->first_name ?>!
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li class="active">@lang('app.dashboard')</li>
                </ol>
            </div>
        </h1>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <a href="{{ route('profile') }}" class="panel-link">
            <div class="panel panel-default dashboard-panel">
                <div class="panel-body">
                    <div class="icon">
                        <i class="fa fa-user"></i>
                    </div>
                    <p class="lead">@lang('app.update_profile')</p>
                </div>
            </div>
        </a>
    </div>
    @if (config('session.driver') == 'database')
        <div class="col-md-3">
            <a href="{{ route('profile.sessions') }}" class="panel-link">
                <div class="panel panel-default dashboard-panel">
                    <div class="panel-body">
                        <div class="icon">
                            <i class="fa fa-list"></i>
                        </div>
                        <p class="lead">@lang('app.my_sessions')</p>
                    </div>
                </div>
            </a>
        </div>
    @endif
    <div class="col-md-3">
        <a href="{{ route('profile.activity') }}" class="panel-link">
            <div class="panel panel-default dashboard-panel">
                <div class="panel-body">
                    <div class="icon">
                        <i class="fa fa-list-alt"></i>
                    </div>
                    <p class="lead">@lang('app.activity_log')</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('auth.logout') }}" class="panel-link">
            <div class="panel panel-default dashboard-panel">
                <div class="panel-body">
                    <div class="icon">
                        <i class="fa fa-sign-out"></i>
                    </div>
                    <p class="lead">@lang('app.logout')</p>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('app.activity') (@lang('app.last_two_weeks')</div>
            <div class="panel-body">
                <div>
                    <canvas id="myChart" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
    <script>
        var labels = {!! json_encode(array_keys($activities)) !!};
        var activities = {!! json_encode(array_values($activities)) !!};
    </script>
    {!! HTML::script('assets/js/chart.min.js') !!}
    {!! HTML::script('assets/js/as/dashboard-default.js') !!}
@stop