<?php

namespace Morpheus\Modules\PushNotification\Contracts;

use Morpheus\Modules\PushNotification\Message;

interface ProviderInterface
{
    public function sendMessage(Message $data): array;
    public function getHeadScript(): string;
}
