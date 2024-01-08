<?php

namespace Morpheus\Shared\Events\Payload;

use Morpheus\Contracts\Exportable;
use Morpheus\Shared\Traits\UseConfig;

class EventPayload implements Exportable
{
    use UseConfig;

    public array $payload;

    public function __construct()
    {
        $this->payload['EventBusName'] = $this->getConfigEventbus();
    }

    private function validate()
    {
        if (empty($this->payload['Detail'])) throw new \InvalidArgumentException('Detail não foi informado.');
        if (empty($this->payload['DetailType'])) throw new \InvalidArgumentException('DetailType não foi informado.');
        if (empty($this->payload['EventBusName'])) throw new \InvalidArgumentException('EventBusName não foi informado.');
    }

    private function parse(): array
    {
        return array_merge($this->payload, [
            'Detail' => (is_array($this->payload['Detail']))
                ? json_encode($this->payload['Detail'])
                : $this->payload['Detail'],
        ]);
    }

    public function export(): array
    {
        $this->validate();
        return $this->parse();
    }

    public function setDetail(array $detail)
    {
        $this->payload['Detail'] = json_encode($detail);
    }

    public function setEventBus(string $eventBus)
    {
        $this->payload['EventBusName'] = $eventBus;
    }

    public function setDetailType(string $detailType)
    {
        $this->payload['DetailType'] = $detailType;
    }
}
