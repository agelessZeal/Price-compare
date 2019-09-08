<div class="panel panel-default">
    <div class="panel-heading">@lang('app.general')</div>
    <div class="panel-body">
        {!! Form::open(['route' => 'settings.auth.update', 'id' => 'registration-settings-form']) !!}
        <div class="form-group">
            <label for="reg_enabled">@lang('app.allow_registration')</label>
            <br>
            <input type="hidden" name="reg_enabled" value="0">
            <input type="checkbox" name="reg_enabled"
                   class="switch" data-on-text="@lang('app.yes')" data-off-text="@lang('app.no')" value="1"
                    {!! settings('reg_enabled') ? 'checked' : '' !!}>
        </div>

        <div class="form-group">
            <label for="forgot_password">
                @lang('app.terms_and_conditions')
                <span class="fa fa-question-circle"
                      data-toggle="tooltip"
                      data-placement="top"
                      title="@lang('app.the_user_has_to_confirm')"></span>
            </label>
            <br>
            <input type="hidden" name="tos" value="0">
            {!! Form::checkbox('tos', 1, settings('tos'),
                ['class' => 'switch', 'data-on-text' => trans('app.yes'), 'data-off-text' => trans('app.no')]) !!}
        </div>

        <div class="form-group">
            <label for="reg_email_confirmation">@lang('app.email_confirmation')</label>
            <br>
            <input type="hidden" name="reg_email_confirmation" value="0">
            {!! Form::checkbox('reg_email_confirmation', 1, settings('reg_email_confirmation'), ['class' => 'switch']) !!}
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-refresh"></i>
            @lang('app.update_settings')
        </button>
        {!! Form::close() !!}
    </div>
</div>