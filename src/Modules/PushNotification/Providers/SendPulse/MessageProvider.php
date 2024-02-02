<?php

namespace Morpheus\Modules\PushNotification\Providers\SendPulse;

use Morpheus\Modules\PushNotification\Contracts\ProviderMessageInterface;
use Morpheus\Modules\PushNotification\Helpers;
use Morpheus\Modules\PushNotification\Message;
use Morpheus\Shared\Traits\UseConfig;
use Tribuna\Util\Helper;

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

        $this->payload = [
            'website_id'                    => $this->getConfig('push_sendpulse_website_id'),
            'ttl'                           => 86400,
            'title'                         => $this->message->getTitle(),
            'body'                          => $this->message->getMessage(),
            'link'                          => $this->message->getUrl(),
        ];

        $this->setIcon();
        $this->setImage();
        $this->setSchedule();
    }

    private function setIcon()
    {
        $imageUrl = $this->getConfig('push_message_icon');
        $this->payload['icon'] = ["name" => "morpheus-push.jpg", "data" => $this->imageToBase64($imageUrl)];
    }

    private function setImage()
    {
        if (empty($this->message->image)) return;
        $this->payload['image'] = ["name" => "morpheus-push.jpg", "data" => $this->imageToBase64($this->message->image)];
    }

    private function setSchedule()
    {
        if (empty($this->message->schedule)) return;
        $date = Helpers::getDateTimeObject($this->message->schedule, 'Y-m-d\TH:i:s.v\Z', 'UTC');
        $this->payload['send_date'] = $date->format('Y-m-d H:i:s');
    }

    private function imageToBase64(string $imageUrl): string
    {
        $content = file_get_contents($imageUrl);
        return base64_encode($content);
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
