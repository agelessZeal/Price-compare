<div class="panel panel-default">
    <div class="panel-heading">@lang('app.two_factor_authentication')</div>
    <div class="panel-body">
        @if (! env('AUTHY_KEY'))
            <div class="alert alert-info">
                @lang('app.in_order_to_enable_2fa')
                @lang('app.new_application_on') <a href="https://www.authy.com/" target="_blank"><strong>@lang('app.authy_website')</strong></a>,
                @lang('app.and_update_your') <code>AUTHY_KEY</code> @lang('app.environment_variable_inside') <code>.env</code> @lang('app.file').
            </div>
        @else
            @if (settings('2fa.enabled'))
                {!! Form::open(['route' => 'settings.auth.2fa.disable', 'id' => 'auth-2fa-settings-form']) !!}
                <button type="submit" class="btn btn-danger" data-toggle="loader" data-loading-text="@lang('app.disabling')">
                    <i class="fa fa-times"></i>
                    @lang('app.disable')
                </button>
                {!! Form::close() !!}
            @else
                {!! Form::open(['route' => 'settings.auth.2fa.enable', 'id' => 'auth-2fa-settings-form']) !!}
                <button type="submit" class="btn btn-primary" data-toggle="loader" data-loading-text="@lang('app.enabling')">
                    <i class="fa fa-phone"></i>
                    @lang('app.enable')
                </button>
                {!! Form::close() !!}
            @endif
        @endif
    </div>
</div>