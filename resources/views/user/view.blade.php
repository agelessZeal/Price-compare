@extends('layouts.app')

@section('page-title', $user->present()->nameOrEmail)

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            {{ $user->present()->nameOrEmail }}
            <small>@lang('app.user_details')</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li><a href="{{ route('user.list') }}">@lang('app.users')</a></li>
                    <li class="active">{{ $user->present()->nameOrEmail }}</li>
                </ol>
            </div>

        </h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-md-5">
        <div id="edit-user-panel" class="panel panel-default">
            <div class="panel-heading">
                @lang('app.details')
                <div class="pull-right">
                    <a href="{{ route('user.edit', $user->id) }}" class="edit"
                       data-toggle="tooltip" data-placement="top" title="@lang('app.edit_user')">
                        @lang('app.edit')
                    </a>
                </div>
            </div>
            <div class="panel-body panel-profile">
                <div class="image">
                    <img alt="image" class="img-circle avatar" src="{{ $user->present()->avatar }}">
                </div>
                <div class="name"><strong>{{ $user->present()->name }}</strong></div>

                <br>

                <table class="table table-hover table-details">
                    <thead>
                        <tr>
                            <th colspan="3">@lang('app.contact_informations')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@lang('app.email')</td>
                            <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                        </tr>
                        @if ($user->phone)
                            <tr>
                                <td>@lang('app.phone')</td>
                                <td><a href="telto:{{ $user->phone }}">{{ $user->phone }}</a></td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th colspan="3">@lang('app.additional_informations')</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>@lang('app.birth')</td>
                        <td>{{ $user->present()->birthday }}</td>
                    </tr>
                    <tr>
                        <td>@lang('app.address')</td>
                        <td>{{ $user->present()->fullAddress }}</td>
                    </tr>
                    <tr>
                        <td>@lang('app.last_logged_in')</td>
                        <td>{{ $user->present()->lastLogin }}</td>
                    </tr>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8 col-md-7">
        <div class="panel panel-default">
            <div class="panel-heading">
                @lang('app.latest_activity')
                <div class="pull-right">
                    <a href="{{ route('activity.user', $user->id) }}" class="edit"
                       data-toggle="tooltip" data-placement="top" title="@lang('app.complete_activity_log')">
                        @lang('app.view_all')
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <table class="table user-activity">
                    <thead>
                        <tr>
                            <th>@lang('app.action')</th>
                            <th>@lang('app.date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userActivities as $activity)
                            <tr>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->created_at->format(config('app.date_time_format')) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop