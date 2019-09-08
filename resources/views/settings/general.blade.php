@extends('layouts.app')

@section('page-title', trans('app.general_settings'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            @lang('app.general_settings')
            <small>@lang('app.manage_general_system_settings')</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li><a href="javascript:;">@lang('app.settings')</a></li>
                    <li class="active">@lang('app.general')</li>
                </ol>
            </div>
        </h1>
    </div>
</div>

@include('partials.messages')

{!! Form::open(['route' => 'settings.general.update', 'id' => 'general-settings-form']) !!}

<div class="row">
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('app.general_app_settings')</div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="name">@lang('app.name')</label>
                    <input type="text" class="form-control" id="app_name"
                           name="app_name" value="{{ settings('app_name') }}">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-refresh"></i>
                    @lang('app.update_settings')
                </button>
            </div>
        </div>
    </div>
</div>

@stop