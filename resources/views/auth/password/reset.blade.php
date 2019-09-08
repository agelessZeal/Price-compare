@extends('layouts.auth')

@section('page-title', trans('app.reset_password'))

@section('content')

<div class="form-wrap col-md-5 auth-form">
    <h1>@lang('app.reset_your_password')</h1>

    @include('partials.messages')

    <form role="form" action="{{ url('password/reset') }}" method="POST" id="reset-password-form" autocomplete="off">

         {{ csrf_field() }}
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group password-field input-icon">
            <label for="password" class="sr-only">@lang('app.your_email')</label>
            <i class="fa fa-lock"></i>
            <input type="email" name="email" id="email" class="form-control" placeholder="@lang('app.your_email')">
        </div>

        <div class="form-group password-field input-icon">
            <label for="password" class="sr-only">@lang('app.new_password')</label>
            <i class="fa fa-lock"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="@lang('app.new_password')">
        </div>

        <div class="form-group password-field input-icon">
            <label for="password" class="sr-only">@lang('app.confirm_new_password')</label>
            <i class="fa fa-lock"></i>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="@lang('app.confirm_new_password')">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-custom btn-lg btn-block" id="btn-reset-password">
                @lang('app.update_password')
            </button>
        </div>

    </form>
</div>

@stop