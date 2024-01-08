<?php

namespace Morpheus\Modules\Core\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventMenusSaved extends AbstractEvent
{
    public string $eventName = 'MenusSaved';
}
