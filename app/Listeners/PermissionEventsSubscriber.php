<?php

namespace Vanguard\Listeners;

use Vanguard\Activity;
use Vanguard\Events\Permission\Created;
use Vanguard\Events\Permission\Deleted;
use Vanguard\Events\Permission\Updated;
use Vanguard\Services\Logging\UserActivity\Logger;

class PermissionEventsSubscriber
{
    /**
     * @var UserActivityLogger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onCreate(Created $event)
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;
        $message = trans('log.new_permission', ['name' => $name]);

        $this->logger->log($message);
    }

    public function onUpdate(Updated $event)
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;
        $message = trans('log.updated_permission', ['name' => $name]);

        $this->logger->log($message);
    }

    public function onDelete(Deleted $event)
    {
        $permission = $event->getPermission();

        $name = $permission->display_name ?: $permission->name;
        $message = trans('log.deleted_permission', ['name' => $name]);

        $this->logger->log($message);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $class = 'Vanguard\Listeners\PermissionEventsSubscriber';

        $events->listen(Created::class, "{$class}@onCreate");
        $events->listen(Updated::class, "{$class}@onUpdate");
        $events->listen(Deleted::class, "{$class}@onDelete");
    }
}
