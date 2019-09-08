@extends('layouts.app')

@section('page-title', trans('app.activity_log'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            {{ isset($user) ? $user->present()->nameOrEmail : trans('app.activity_log') }}
            <small>{{ isset($user) ? trans('app.activity_log_sm') : trans('app.activity_log_all_users') }}</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    @if (isset($user) && isset($adminView))
                        <li><a href="{{ route('activity.index') }}">@lang('app.activity_log')</a></li>
                        <li class="active">{{ $user->present()->nameOrEmail }}</li>
                    @else
                        <li class="active">@lang('app.activity_log')</li>
                    @endif
                </ol>
            </div>

        </h1>
    </div>
</div>

<div class="row tab-search">
    <div class="col-md-8"></div>
    <form method="GET" action="" accept-charset="UTF-8" id="users-form">
        <div class="col-md-4">
            <div class="input-group custom-search-form">
                <input type="text" class="form-control" name="search"
                       value="{{ Input::get('search') }}" placeholder="@lang('app.search_for_action')">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit" id="search-activities-btn">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                    @if (Input::has('search') && Input::get('search') != '')
                        <a href="{{ isset($adminView) ? route('activity.index') : route('profile.activity') }}"
                           class="btn btn-danger" type="button">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    @endif
                </span>
            </div>
        </div>
    </form>
</div>

<div class="table-responsive top-border-table">
    <table class="table">
        <thead>
            @if (isset($adminView))
                <th>@lang('app.user')</th>
            @endif
            <th>@lang('app.ip_address')</th>
            <th>@lang('app.message')</th>
            <th>@lang('app.log_time')</th>
            <th class="text-center">@lang('app.more_info')</th>
        </thead>
        <tbody>
            @foreach ($activities as $activity)
                <tr>
                    @if (isset($adminView))
                        <td>
                            @if (isset($user))
                                {{ $activity->user->present()->nameOrEmail }}
                            @else
                                <a href="{{ route('activity.user', $activity->user_id) }}"
                                    data-toggle="tooltip" title="@lang('app.view_activity_log')">
                                    {{ $activity->user->present()->nameOrEmail }}
                                </a>
                            @endif
                        </td>
                    @endif
                    <td>{{ $activity->ip_address }}</td>
                    <td>{{ $activity->description }}</td>
                    <td>{{ $activity->created_at->format(config('app.date_time_format')) }}</td>
                    <td class="text-center">
                        <a tabindex="0" role="button" class="btn btn-primary btn-circle"
                           data-trigger="focus"
                           data-placement="left"
                           data-toggle="popover"
                           title="@lang('app.user_agent')"
                           data-content="{{ $activity->user_agent }}">
                            <i class="fa fa-info"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {!! $activities->render() !!}
</div>

@stop