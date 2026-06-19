<?php

declare(strict_types=1);

namespace Darflen\Framework\Event;

use Override;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class Dispatcher implements EventDispatcherInterface
{
    private ListenerProviderInterface $provider;

    public function __construct(ListenerProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    #[Override]
    public function dispatch(object $event): object
    {
        $listeners = $this->provider->getListenersForEvent($event);
        /** @disregard */
        foreach ($listeners as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
            $listener($event);
        }
        return $event;
    }
}
