<div class="panel panel-default">
    <div class="panel-heading">@lang('app.general')</div>
    <div class="panel-body">
        {!! Form::open(['route' => 'settings.auth.update', 'id' => 'auth-general-settings-form']) !!}

        <div class="form-group">
            <label for="remember_me">
                @lang('app.allow_remember_me')
                <span class="fa fa-question-circle"
                      data-toggle="tooltip"
                      data-placement="top"
                      title="@lang('app.should_remember_me_be_displayed')"></span>
            </label>
            <br>
            <input type="hidden" name="remember_me" value="0">
            {!! Form::checkbox('remember_me', 1, settings('remember_me'), ['class' => 'switch']) !!}
        </div>

        <div class="form-group">
            <label for="forgot_password">
                @lang('app.forgot_password')
                <span class="fa fa-question-circle"
                      data-toggle="tooltip"
                      data-placement="top"
                      title="@lang('app.enable_disable_forgot_password')"></span>
            </label>
            <br>
            <input type="hidden" name="forgot_password" value="0">
            {!! Form::checkbox('forgot_password', 1, settings('forgot_password'), ['class' => 'switch']) !!}
        </div>

        <div class="form-group">
            <label for="login_reset_token_lifetime">
                @lang('app.reset_token_lifetime')
                <span class="fa fa-question-circle"
                      data-toggle="tooltip"
                      data-placement="top"
                      title="@lang('app.number_of_minutes')"></span>
            </label>
            <input type="text" name="login_reset_token_lifetime"
                   class="form-control" value="{{ settings('login_reset_token_lifetime', 30) }}">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-refresh"></i>
            @lang('app.update_settings')
        </button>

        {!! Form::close() !!}
    </div>
</div>