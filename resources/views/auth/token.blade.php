@extends('layouts.auth')

@section('page-title', trans('app.two_factor_authentication'))

@section('content')

<div class="form-wrap col-md-5 auth-form">
    <h1>@lang('app.two_factor_authentication')</h1>

    @include('partials.messages')

    <form role="form" action="<?= route('auth.token.validate') ?>" method="POST" autocomplete="off">
        <input type="hidden" value="<?= csrf_token() ?>" name="_token">

        <div class="form-group password-field input-icon">
            <label for="password" class="sr-only">@lang('app.token')</label>
            <i class="fa fa-lock"></i>
            <input type="text" name="token" id="token" class="form-control" placeholder="@lang('app.authy_2fa_token')">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-custom btn-lg btn-block" id="btn-reset-password">
                @lang('app.validate')
            </button>
        </div>
    </form>
</div>

@stop