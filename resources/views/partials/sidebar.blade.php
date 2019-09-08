<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li class="sidebar-avatar">
                <div class="dropdown">
                    <div>
                        <img alt="image" class="img-circle avatar" width="100" src="{{ Auth::user()->present()->avatar }}">
                    </div>
                    <div class="name"><strong>{{ Auth::user()->present()->nameOrEmail }}</strong></div>
                </div>
            </li>
            <li class="{{ Request::is('/') ? 'active open' : ''  }}">
                <a href="{{ route('dashboard') }}" class="{{ Request::is('/') ? 'active' : ''  }}">
                    <i class="fa fa-dashboard fa-fw"></i> @lang('app.dashboard')
                </a>
            </li>
            @permission('users.manage')
                <li class="{{ Request::is('user*') ? 'active open' : ''  }}">
                    <a href="{{ route('user.list') }}" class="{{ Request::is('user*') ? 'active' : ''  }}">
                        <i class="fa fa-users fa-fw"></i> @lang('app.users')
                    </a>
                </li>
            @endpermission
            @permission('products.manage')
            <li class="{{ Request::is('product*') ? 'active open' : ''  }}">
                <a href="{{ route('product.index') }}" class="{{ Request::is('product*') ? 'active' : ''  }}">
                    <i class="fa fa-list fa-fw"></i> @lang('app.products')
                </a>
            </li>
            <li class="{{ Request::is('category*') ? 'active open' : ''  }}">
                <a href="{{ route('category.index') }}" class="{{ Request::is('category*') ? 'active' : ''  }}">
                    <i class="fa fa-tags fa-fw"></i> @lang('app.low_categories')
                </a>
            </li>
            @endpermission
            @permission('users.activity')
                <li class="{{ Request::is('activity*') ? 'active open' : ''  }}">
                    <a href="{{ route('activity.index') }}" class="{{ Request::is('activity*') ? 'active' : ''  }}">
                        <i class="fa fa-list-alt fa-fw"></i> @lang('app.activity_log')
                    </a>
                </li>
            @endpermission
            @permission(['roles.manage', 'permissions.manage'])
                <li class="{{ Request::is('role*') || Request::is('permission*') ? 'active open' : ''  }}">
                    <a href="#">
                        <i class="fa fa-user fa-fw"></i>
                        @lang('app.roles_and_permissions')
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        @permission('roles.manage')
                            <li>
                                <a href="{{ route('role.index') }}" class="{{ Request::is('role*') ? 'active' : ''  }}">
                                    @lang('app.roles')
                                </a>
                            </li>
                        @endpermission
                        @permission('permissions.manage')
                            <li>
                                <a href="{{ route('permission.index') }}"
                                   class="{{ Request::is('permission*') ? 'active' : ''  }}">@lang('app.permissions')</a>
                            </li>
                        @endpermission
                    </ul>
                </li>
            @endpermission

            @permission(['settings.general', 'settings.auth', 'settings.notifications'], false)
            <li class="{{ Request::is('settings*') ? 'active open' : ''  }}">
                <a href="#">
                    <i class="fa fa-gear fa-fw"></i> @lang('app.settings')
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    @permission('settings.general')
                        <li>
                            <a href="{{ route('settings.general') }}"
                               class="{{ Request::is('settings') ? 'active' : ''  }}">
                                @lang('app.general')
                            </a>
                        </li>
                    @endpermission
                    @permission('settings.auth')
                        <li>
                            <a href="{{ route('settings.auth') }}"
                               class="{{ Request::is('settings/auth*') ? 'active' : ''  }}">
                                @lang('app.auth_and_registration')
                            </a>
                        </li>
                    @endpermission
                    @permission('settings.notifications')
                        <li>
                            <a href="{{ route('settings.notifications') }}"
                               class="{{ Request::is('settings/notifications*') ? 'active' : ''  }}">
                                @lang('app.notifications')
                            </a>
                        </li>
                    @endpermission
                </ul>
            </li>
            @endpermission
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>