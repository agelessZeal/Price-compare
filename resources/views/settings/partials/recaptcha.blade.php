<div class="panel panel-default">
    <div class="panel-heading">reCAPTCHA</div>
    <div class="panel-body">

        @if (! (env('RECAPTCHA_SITEKEY') && env('RECAPTCHA_SECRETKEY')))
            <div class="alert alert-info">
                @lang('app.to_utilize_recaptcha_please_get') <code>@lang('app.site_key')</code> and <code>@lang('app.secret_key')</code>
                @lang('app.from') <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank"><strong>@lang('app.recaptcha_website')</strong></a>,
                @lang('app.and_update_your') <code>RECAPTCHA_SITEKEY</code> @lang('app.and') <code>RECAPTCHA_SECRETKEY</code> @lang('app.environment_variables_inside') <code>.env</code> @lang('app.file').
            </div>
        @else
            @if (settings('registration.captcha.enabled'))
                {!! Form::open(['route' => 'settings.registration.captcha.disable', 'id' => 'captcha-settings-form']) !!}
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-times"></i>
                    @lang('app.disable')
                </button>
                {!! Form::close() !!}
            @else
                {!! Form::open(['route' => 'settings.registration.captcha.enable', 'id' => 'captcha-settings-form']) !!}
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-refresh"></i>
                    @lang('app.enable')
                </button>
                {!! Form::close() !!}
            @endif
        @endif
    </div>
</div>