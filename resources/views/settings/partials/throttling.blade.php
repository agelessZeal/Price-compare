<div class="panel panel-default">
    <div class="panel-heading">@lang('app.authentication_throttling')</div>
    <div class="panel-body">
        {!! Form::open(['route' => 'settings.auth.update', 'id' => 'auth-throttle-settings-form']) !!}

        <div class="form-group">
            <label for="name">@lang('app.throttle_authentication')</label>
            <br>
            <input type="hidden" name="throttle_enabled" value="0">
            {!! Form::checkbox('throttle_enabled', 1, settings('throttle_enabled'), ['class' => 'switch']) !!}
        </div>

        <div class="form-group">
            <label for="throttle_attempts">
                @lang('app.maximum_number_of_attempts')
                <span class="fa fa-question-circle"
                      data-toggle="tooltip"
                      data-placement="top"
                      title="@lang('app.max_number_of_incorrect_login_attempts')"></span>
            </label>
            <input type="text" name="throttle_attempts" class="form-control"
                   value="{{ settings('throttle_attempts', 10) }}">
        </div>

        <div class="form-group">
            <label for="throttle_lockout_time">
                @lang('app.lockout_time')
                <span class="fa fa-question-circle"
                      data-toggle="tooltip"
                      data-placement="top"
                      title="@lang('app.num_of_minutes_to_lock_the_user')"></span>
            </label>
            <input type="text" name="throttle_lockout_time" class="form-control"
                   value="{{ settings('throttle_lockout_time', 1) }}">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-refresh"></i>
            @lang('app.update_settings')
        </button>

        {!! Form::close() !!}
    </div>
</div>