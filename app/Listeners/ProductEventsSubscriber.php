<?php

namespace Vanguard\Listeners;

use Vanguard\Events\Product\UpdatedByAdmin;
use Vanguard\Events\Product\Created;
use Vanguard\Events\Product\Deleted;
use Vanguard\Services\Logging\UserActivity\Logger;

class ProductEventsSubscriber
{
    /**
     * @var Logger
     */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function onCreate(Created $event)
    {
        $message = trans(
            'log.new_product',
            ['name' => $event->getCreatedProduct()->pdt_title]
        );

        $this->logger->log($message);
    }

    public function onUpdate(UpdatedByAdmin $event)
    {
        $message = trans(
            'log.updated_product',
            ['name' => $event->getUpdatedProduct()->pdt_title]
        );

        $this->logger->log($message);
    }

    public function onDelete(Deleted $event)
    {
        $message = trans(
            'log.deleted_product',
            ['name' => $event->getDeletedProduct()->pdt_title]
        );

        $this->logger->log($message);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $class = 'Vanguard\Listeners\ProductEventsSubscriber';
        $events->listen(Created::class, "{$class}@onCreate");
        $events->listen(UpdatedByAdmin::class, "{$class}@onUpdate");
        $events->listen(Deleted::class, "{$class}@onDelete");
    }
}
