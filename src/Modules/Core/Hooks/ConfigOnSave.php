<?php

namespace Morpheus\Modules\Core\Hooks;

use Morpheus\Contracts\Actionable;
use Morpheus\Modules\Core\Events\EventConfigsSaved;
use Morpheus\Modules\Core\Events\Payload\ConfigPayload;
use Morpheus\Shared\Events\Payload\EventPayload;
use Morpheus\Shared\Traits\UseConfig;

class ConfigOnSave implements Actionable
{

    use UseConfig;

    public function __invoke(...$params): void
    {
        if (!$this->hasConfigEventbus()) return;
        if (current($params) !== 'options') return;
        if (get_current_screen()?->id !== 'toplevel_page_morpheus') return;

        $detail = new ConfigPayload();
        $payload = new EventPayload();
        $payload->setDetail($detail->export());

        $event = new EventConfigsSaved($payload);
        $event->send();
    }
}
