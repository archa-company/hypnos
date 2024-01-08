<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\Core\Events\EventMenusSaved;
use Morpheus\Modules\Core\Events\Payload\MenuPayload;
use Morpheus\Shared\Events\Payload\EventPayload;

class MenuOnSave implements Actionable
{

    public function __invoke(...$params): void
    {
        $detail = new MenuPayload();
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventMenusSaved($payload);
        $event->send();
    }
}
