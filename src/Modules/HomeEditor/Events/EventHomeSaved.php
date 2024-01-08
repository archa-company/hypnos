<?php

namespace Morpheus\Modules\HomeEditor\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventHomeSaved extends AbstractEvent
{
    public string $eventName = 'HomeSaved';
}
