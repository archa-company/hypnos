<?php

namespace Morpheus\Modules\WebStories\Events;

use Morpheus\Shared\Events\AbstractEvent;

class EventStoryRemoved extends AbstractEvent
{
    public string $eventName = 'StoryRemoved';
}
