<?php

namespace Morpheus\Modules\Core\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventConfigsSaved extends AbstractEvent
{
    public string $eventName = 'ConfigSaved';
}
