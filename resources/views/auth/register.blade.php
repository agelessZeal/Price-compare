@extends('layouts.auth')

@section('page-title', trans('app.sign_up'))

@if (settings('registration.captcha.enabled'))
    <script src='https://www.google.com/recaptcha/api.js'></script>
@endif

@section('content')

    <div class="form-wrap col-md-5 auth-form">
        <div style="text-align: center; margin-bottom: 25px;">
            <img src="{{ url('assets/img/bastaprice-logo.png') }}" alt="{{ settings('app_name') }}">
        </div>


        @include('partials/messages')

        <form role="form" action="<?= url('register') ?>" method="post" id="registration-form" autocomplete="off">
            <input type="hidden" value="<?= csrf_token() ?>" name="_token">
            <div class="form-group input-icon">
                <i class="fa fa-at"></i>
                <input type="email" name="email" id="email" class="form-control" placeholder="@lang('app.email')" value="{{ old('email') }}">
            </div>
            <div class="form-group input-icon">
                <i class="fa fa-user"></i>
                <input type="text" name="username" id="username" class="form-control" placeholder="@lang('app.username')"  value="{{ old('username') }}">
            </div>
            <div class="form-group input-icon">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" id="password" class="form-control" placeholder="@lang('app.password')">
            </div>
             <div class="form-group input-icon">
                <i class="fa fa-lock"></i>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="@lang('app.confirm_password')">
            </div>

            @if (settings('tos'))
                <div class="form-group">
                    <div class="checkbox">
                        <input type="checkbox" name="tos" id="tos" value="1"/>
                        <label for="tos">@lang('app.i_accept') <a href="#tos-modal" data-toggle="modal">@lang('app.terms_of_service')</a></label>
                    </div>
                </div>
            @endif


            {{-- Only display captcha if it is enabled --}}
            @if (settings('registration.captcha.enabled'))
                <div class="form-group">
                    {!! app('captcha')->display() !!}
                </div>
            @endif
            {{-- end captcha --}}

            <div class="form-group">
                <button type="submit" class="btn btn-custom btn-lg btn-block" id="btn-login">
                    @lang('app.register')
                </button>
            </div>
        </form>

        @include('auth.social.buttons')

    </div>

    @if (settings('tos'))
        <div class="modal fade" id="tos-modal" tabindex="-1" role="dialog" aria-labelledby="tos-label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="@lang('app.terms_of_service')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title" id="tos-label">@lang('app.terms_of_service')</h3>
                    </div>
                    <div class="modal-body">
                        <h4>1. Terms</h4>

                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                            Donec quis lacus porttitor, dignissim nibh sit amet, fermentum felis.
                            Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere
                            cubilia Curae; In ultricies consectetur viverra. Nullam velit neque,
                            placerat condimentum tempus tincidunt, placerat eu lectus. Nam molestie
                            porta purus, et pretium risus vehicula in. Cras sem ipsum, varius sagittis
                            rhoncus nec, dictum maximus diam. Duis ac laoreet est. In turpis velit, placerat
                            eget nisi vitae, dignissim tristique nisl. Curabitur sollicitudin, nunc ut
                            viverra interdum, lacus...
                        </p>

                        <h4>2. Use License</h4>

                        <ol type="a">
                            <li>
                                Aenean vehicula erat eu nisi scelerisque, a mattis purus blandit. Curabitur congue
                                ollis nisl malesuada egestas. Lorem ipsum dolor sit amet, consectetur adipiscing elit:
                            </li>
                        </ol>

                        <p>...</p>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('app.close')</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@stop

@section('scripts')
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Auth\RegisterRequest', '#registration-form') !!}
@stop