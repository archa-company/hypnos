<?php

namespace Morpheus\Modules\Core\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventPostSaved extends AbstractEvent
{
    public string $eventName = 'PostSaved';
}
