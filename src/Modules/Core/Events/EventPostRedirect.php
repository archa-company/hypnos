<?php

namespace Morpheus\Modules\Core\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventPostRedirect extends AbstractEvent
{
    public string $eventName = 'PostRedirect';
}
