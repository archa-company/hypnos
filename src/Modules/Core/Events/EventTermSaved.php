<?php

namespace Morpheus\Modules\Core\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventTermSaved extends AbstractEvent
{
    public string $eventName = 'TermSaved';
}
