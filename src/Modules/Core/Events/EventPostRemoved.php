<?php

namespace Morpheus\Modules\Core\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventPostRemoved extends AbstractEvent
{
    public string $eventName = 'PostRemoved';
}
