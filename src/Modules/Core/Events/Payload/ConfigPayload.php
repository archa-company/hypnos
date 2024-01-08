<?php

namespace Morpheus\Modules\Core\Events\Payload;

use Morpheus\Contracts\Exportable;
use Morpheus\Shared\Traits\UseConfig;

class ConfigPayload implements Exportable
{
    use UseConfig;

    public function export()
    {
        return $this->parse();
    }

    private function parse(): array
    {
        return [
            'type'              => 'config',
            'payload'           => $this->getConfigs(),
        ];
    }
}
