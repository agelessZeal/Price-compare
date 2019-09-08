@extends('layouts.app')

@section('page-title', trans('app.notification_settings'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            @lang('app.notification_settings')
            <small>@lang('app.manage_system_notification_settings')</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li><a href="javascript:;">@lang('app.settings')</a></li>
                    <li class="active">@lang('app.notifications')</li>
                </ol>
            </div>
        </h1>
    </div>
</div>

@include('partials.messages')

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('app.email_notifications')</div>
            <div class="panel-body">
                {!! Form::open(['route' => 'settings.notifications.update', 'id' => 'notification-settings-form']) !!}
                    <div class="form-group">
                        <label for="notifications_signup_email">@lang('app.notify_admin_when_user_signs_up')</label>
                        <br>
                        <input type="hidden" name="notifications_signup_email" value="0">
                        <input type="checkbox" name="notifications_signup_email" class="switch" value="1"
                               data-on-text="@lang('app.yes')" data-off-text="@lang('app.no')" {{ settings('notifications_signup_email') ? 'checked' : '' }}>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-refresh"></i>
                        @lang('app.update_settings')
                    </button>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
    {!! HTML::script('assets/plugins/bootstrap-switch/bootstrap-switch.min.js') !!}
    <script>
        $(".switch").bootstrapSwitch({size: 'small'});
    </script>
@stop

@section('styles')
    {!! HTML::style('assets/plugins/bootstrap-switch/bootstrap-switch.css') !!}
@stop