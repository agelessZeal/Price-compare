<div class="panel panel-default">
    <div class="panel-heading">@lang('app.two_factor_authentication')</div>
    <div class="panel-body">
        @if (! Authy::isEnabled($user))
            <div class="alert alert-info">
                @lang('app.in_order_to_enable_2fa_you_must') <a target="_blank" href="https://www.authy.com/">Authy</a> @lang('app.application_on_your_phone').
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="country_code">@lang('app.country_code')</label>
                        <input type="text" class="form-control" id="country_code" placeholder="381"
                               name="country_code" value="{{ $user->two_factor_country_code }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone_number">@lang('app.cell_phone')</label>
                        <input type="text" class="form-control" id="phone_number" placeholder="@lang('app.phone_without_country_code')"
                               name="phone_number" value="{{ $user->two_factor_phone }}">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" data-toggle="loader" data-loading-text="@lang('app.enabling')">
                <i class="fa fa-phone"></i>
                @lang('app.enable')
            </button>
        @else
            <button type="submit" class="btn btn-danger" data-toggle="loader" data-loading-text="@lang('app.disabling')">
                <i class="fa fa-close"></i>
                @lang('app.disable')
            </button>
        @endif
    </div>
</div>
