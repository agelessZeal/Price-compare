<?php

namespace Vanguard\Providers;

use Vanguard\Events\User\Banned;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\Registered;
use Vanguard\Listeners\ProductEventsSubscriber;
use Vanguard\Listeners\Users\InvalidateSessionsAndTokens;
use Vanguard\Listeners\Login\UpdateLastLoginTimestamp;
use Vanguard\Listeners\PermissionEventsSubscriber;
use Vanguard\Listeners\Registration\SendConfirmationEmail;
use Vanguard\Listeners\Registration\SendSignUpNotification;
use Vanguard\Listeners\RoleEventsSubscriber;
use Vanguard\Listeners\UserEventsSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendConfirmationEmail::class,
            SendSignUpNotification::class,
        ],
        LoggedIn::class => [
            UpdateLastLoginTimestamp::class
        ],
        Banned::class => [
            InvalidateSessionsAndTokens::class
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = array(
        UserEventsSubscriber::class,
        RoleEventsSubscriber::class,
        PermissionEventsSubscriber::class,
        ProductEventsSubscriber::class
    );

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
