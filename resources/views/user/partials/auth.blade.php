<div class="panel panel-default">
    <div class="panel-heading">@lang('app.login_details')</div>
    <div class="panel-body">
        <div class="form-group">
            <label for="email">@lang('app.email')</label>
            <input type="email" class="form-control" id="email"
                   name="email" placeholder="@lang('app.email')" value="{{ $edit ? $user->email : '' }}">
        </div>
        <div class="form-group">
            <label for="username">@lang('app.username')</label>
            <input type="text" class="form-control" id="username" placeholder="(@lang('app.optional'))"
                   name="username" value="{{ $edit ? $user->username : '' }}">
        </div>
        <div class="form-group">
            <label for="password">{{ $edit ? trans("app.new_password") : trans('app.password') }}</label>
            <input type="password" class="form-control" id="password"
                   name="password" @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @endif>
        </div>
        <div class="form-group">
            <label for="password_confirmation">{{ $edit ? trans("app.confirm_new_password") : trans('app.confirm_password') }}</label>
            <input type="password" class="form-control" id="password_confirmation"
                   name="password_confirmation" @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @endif>
        </div>
        @if ($edit)
            <button type="submit" class="btn btn-primary" id="update-login-details-btn">
                <i class="fa fa-refresh"></i>
                @lang('app.update_details')
            </button>
        @endif
    </div>
</div>