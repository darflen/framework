<?php

declare(strict_types=1);

namespace Darflen\Framework\Event;

use Override;
use Psr\EventDispatcher\ListenerProviderInterface;

class Provider implements ListenerProviderInterface
{
    private array $listeners = [];

    public function setListenerForEvent(string $eventClassName, callable $listener): void
    {
        $this->listeners[$eventClassName][] = $listener;
    }

    #[Override]
    public function getListenersForEvent(object $event): iterable
    {
        foreach ($this->listeners as $eventClassName => $listeners) {
            if (is_a($event, $eventClassName)) {
                foreach ($listeners as $listener) {
                    yield $listener;
                }
            }
        }
    }
}
