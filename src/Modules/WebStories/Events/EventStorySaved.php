<?php

namespace Morpheus\Modules\WebStories\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventStorySaved extends AbstractEvent
{
    public string $eventName = 'StorySaved';
}
