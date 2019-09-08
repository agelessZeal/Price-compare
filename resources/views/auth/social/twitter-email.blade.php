@extends('layouts.auth')

@section('content')

    <div class="form-wrap col-md-6 col-md-offset-3 auth-form">
        <h1>@lang('app.hey') {{ $account->getName() }},</h1>

        <div class="alert alert-warning">
            <strong>@lang('app.one_more_thing')...</strong>
            @lang('app.twitter_does_not_provide_email')
        </div>

        @include('partials.messages')

        <form role="form" action="<?= url('auth/twitter/email') ?>" method="POST" id="email-form" autocomplete="off">
            <input type="hidden" value="<?= csrf_token() ?>" name="_token">

            <div class="form-group password-field input-icon">
                <label for="password" class="sr-only">@lang('app.email')</label>
                <i class="fa fa-at"></i>
                <input type="email" name="email" id="email" class="form-control" placeholder="@lang('app.your_email')">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-custom btn-lg btn-block">@lang('app.log_me_in')</button>
            </div>
        </form>
    </div>

@stop

@section('scripts')
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Auth\Social\SaveEmailRequest', '#email-form') !!}
@stop