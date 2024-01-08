<?php

namespace Morpheus\Shared\Events;

use Morpheus\Contracts\Sendable;
use Morpheus\Shared\Clients\EventBridge;
use Morpheus\Shared\Events\Payload\EventPayload;
use Throwable;

abstract class AbstractEvent implements Sendable
{
    public string $eventName;
    public EventBridge $client;
    public EventPayload $payload;

    public function __construct(EventPayload $payload)
    {
        $this->client = new EventBridge;
        $this->payload = $payload;
        $this->payload->setDetailType($this->eventName);
    }

    public function send()
    {
        try {
            return $this->client->send($this->payload->export());
        } catch (Throwable $error) {
            if (!function_exists('wp_sentry_safe')) return;
            wp_sentry_safe(function (\Sentry\State\HubInterface $client) use ($error) {
                $client->captureException($error);
            });
        }
    }
}
