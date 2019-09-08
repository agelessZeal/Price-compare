@extends('layouts.app')

@section('page-title', trans('app.authentication_settings'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            @lang('app.authentication')
            <small>@lang('app.system_auth_registration_settings')</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li><a href="javascript:;">@lang('app.settings')</a></li>
                    <li class="active">@lang('app.authentication')</li>
                </ol>
            </div>
        </h1>
    </div>
</div>

@include('partials.messages')

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="#auth" aria-controls="auth" role="tab" data-toggle="tab">
            <i class="fa fa-lock"></i>
            @lang('app.authentication')
        </a>
    </li>
    <li role="presentation">
        <a href="#registration" aria-controls="registration" role="tab" data-toggle="tab">
            <i class="fa fa-user-plus"></i>
            @lang('app.registration')
        </a>
    </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="auth">
        <div class="row">
            <div class="col-md-6">
                @include('settings.partials.auth')
            </div>
            <div class="col-md-6">
                @include('settings.partials.throttling')
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                @include('settings.partials.two-factor')
            </div>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="registration">
        <div class="row">
            <div class="col-md-6">
                @include('settings.partials.registration')
            </div>
            <div class="col-md-6">
                @include('settings.partials.recaptcha')
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
    {!! HTML::script('assets/plugins/bootstrap-switch/bootstrap-switch.min.js') !!}
    <script>
        $(".switch").bootstrapSwitch({ size: 'small' });
    </script>
@stop

@section('styles')
    {!! HTML::style('assets/plugins/bootstrap-switch/bootstrap-switch.css') !!}
@stop