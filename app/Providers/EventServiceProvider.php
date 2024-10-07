<?php

namespace App\Providers;

use App\Listeners\ContentLenghtListener;
use App\Listeners\HandleEntityListener;
use App\Listeners\InternalErrorListener;
use danyk\Framework\Dbal\Event\EntityPersist;
use danyk\Framework\Event\EventDispatcher;
use danyk\Framework\Http\Events\ResponseEvent;
use danyk\Framework\Providers\ServiceProviderInterface;

class EventServiceProvider implements ServiceProviderInterface
{
    private array $listen = [
        ResponseEvent::class => [
            InternalErrorListener::class,
            ContentLenghtListener::class,
        ],
        EntityPersist::class => [
            HandleEntityListener::class,
        ],
    ];

    public function __construct(
        private EventDispatcher $eventDispatcher,
    ) {
    }

    public function register(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                $this->eventDispatcher->addListener($event, new $listener);
            }
        }
    }
}
