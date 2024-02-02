<?php

namespace Morpheus\Modules\PushNotification\Providers\Notix;

use DateInterval;
use DateTime;
use DateTimeZone;
use Morpheus\Modules\PushNotification\Contracts\ProviderMessageInterface;
use Morpheus\Modules\PushNotification\Helpers;
use Morpheus\Modules\PushNotification\Message;
use Morpheus\Shared\Traits\UseConfig;

class MessageProvider implements ProviderMessageInterface
{
    use UseConfig;

    private Message $message;
    private array $payload = [];

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->parse();
    }

    private function parse()
    {
        if (!$this->message->getTitle()) throw new \InvalidArgumentException('Título da notificações não informado');
        if (!$this->message->getMessage()) throw new \InvalidArgumentException('Mensagem da notificações não informado');

        $messageData = $this->message->getData();

        $this->payload = [
            'message'                       => [
                'icon'                          => $this->getConfig('push_message_icon'),
                'image'                         => $messageData->image ?? null,
                'url'                           => $this->message->getUrl() ?? null,
                'title'                         => $this->message->getTitle(),
                'text'                          => $this->message->getMessage(),
            ],
            'ttl'                           => 60, // Minutos
        ];

        $this->setSchedule();
    }

    private function setSchedule()
    {
        $date = ($this->message->schedule)
            ? $date = Helpers::getDateTimeObject($this->message->schedule, 'Y-m-d\TH:i:s.v\Z', 'UTC')
            : (new DateTime('now', new DateTimeZone('UTC')))->add(DateInterval::createFromDateString('30 seconds'));

        $this->payload['scheduled_date'] = $date->format('Y-m-d H:i:s');
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
