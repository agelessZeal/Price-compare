@extends('layouts.auth')

@section('page-title', trans('app.reset_password'))

@section('content')

<div class="form-wrap col-md-5 auth-form">
    <h1>@lang('app.forgot_your_password')</h1>

    @include('partials.messages')

    <form role="form" action="<?= url('password/remind') ?>" method="POST" id="remind-password-form" autocomplete="off">
        <input type="hidden" value="<?= csrf_token() ?>" name="_token">

        <div class="form-group password-field input-icon">
            <label for="password" class="sr-only">@lang('app.email')</label>
            <i class="fa fa-at"></i>
            <input type="email" name="email" id="email" class="form-control" placeholder="@lang('app.your_email')">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-custom btn-lg btn-block" id="btn-reset-password">
                @lang('app.reset_password')
            </button>
        </div>
    </form>
</div>

@stop

@section('scripts')
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Auth\PasswordRemindRequest', '#remind-password-form') !!}
@stop