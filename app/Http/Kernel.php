<?php

namespace Vanguard\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Vanguard\Http\Middleware\VerifyInstallation::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Vanguard\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \Vanguard\Http\Middleware\TrustProxies::class,
    ];
    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Vanguard\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Vanguard\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'api' => [
            \Vanguard\Http\Middleware\UseApiGuard::class,
            'throttle:60,1',
            'bindings'
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Vanguard\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \Vanguard\Http\Middleware\RedirectIfAuthenticated::class,
        'registration' => \Vanguard\Http\Middleware\Registration::class,
        'social.login' => \Vanguard\Http\Middleware\SocialLogin::class,
        'role' => \Vanguard\Http\Middleware\CheckRole::class,
        'permission' => \Vanguard\Http\Middleware\CheckPermissions::class,
        'session.database' => \Vanguard\Http\Middleware\DatabaseSession::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
