<?php

namespace Morpheus\Shared\Clients;

use Aws\EventBridge\EventBridgeClient;

class EventBridge
{
    public EventBridgeClient $client;
    public string $region;

    public function __construct(string $region = 'us-east-1')
    {
        $this->region = $region;
        $this->client = $this->getClient();
    }

    private function getClient()
    {
        return new EventBridgeClient([
            'region' => $this->region,
            'version' => 'latest',
        ]);
    }

    private function validate(array $payload)
    {
        if (empty($payload['Detail'])) throw new \InvalidArgumentException('Detail não foi informado.');
        if (empty($payload['DetailType'])) throw new \InvalidArgumentException('DetailType não foi informado.');
        if (empty($payload['EventBusName'])) throw new \InvalidArgumentException('EventBusName não foi informado.');
        if (empty($payload['Source'])) throw new \InvalidArgumentException('Source não foi informado.');
    }

    public function send(array $payload)
    {
        $payload['Source'] = 'morpheus.wp';

        $this->validate($payload);

        return $this->client->putEvents([
            'Entries'       => [
                $payload,
            ]
        ]);
    }
}
