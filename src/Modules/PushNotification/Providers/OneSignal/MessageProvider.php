<?php

namespace Morpheus\Modules\PushNotification\Providers\OneSignal;

use Morpheus\Modules\PushNotification\Contracts\ProviderMessageInterface;
use Morpheus\Modules\PushNotification\Helpers;
use Morpheus\Modules\PushNotification\Message;
use Morpheus\Shared\Traits\UseConfig;

class MessageProvider implements ProviderMessageInterface
{
    use UseConfig;

    private Message $message;
    private array $payload = [];
    private string $defaultSegment = 'Usuários Ativos - Novo';
    // private string $defaultSegment = 'All';
    // private string $defaultSegment = 'Active Users';

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
        $icon = $this->getConfig('push_message_icon');

        $this->payload = [
            'app_id'                        => $this->getConfig('push_onesignal_app_id'),

            'ttl'                           => 86400,
            'data'                          => $messageData->data ?? null,
            'url'                           => $this->message->getUrl() ?? null,

            'chrome_web_badge'              => $icon,

            'large_icon'                    => $icon,
            'chrome_web_icon'               => $icon,

            'big_picture'                   => $messageData->image ?? null,
            'adm_big_picture'               => $messageData->image ?? null,
            'chrome_big_picture'            => $messageData->image ?? null,
            'chrome_web_image'              => $messageData->image ?? null,

            'collapse_id'                   => 'morpheus-' . date_i18n("U"),
            'android_group'                 => $messageData->group ?? "morpheus-app",
            'android_accent_color'          => $messageData->color ?? "FF0162FB",
            'android_led_color'             => $messageData->color ?? "FF0162FB",
            'android_visibility'            => 1,
            'headings'                      => [
                'en'                            => $this->message->getTitle(),
                'pt'                            => $this->message->getTitle()
            ],
            'contents'                      => [
                'en'                            => $this->message->getMessage(),
                'pt'                            => $this->message->getMessage()
            ]
        ];

        $this->setSchedule();
        $this->setSegments();
    }

    private function setSchedule()
    {
        if (empty($this->message->schedule)) return;
        $date = Helpers::getDateTimeObject($this->message->schedule, 'Y-m-d\TH:i:s.v\Z', 'UTC');
        $this->payload['send_after'] = $date->format('Y-m-d H:i:s');
        // $this->payload['send_after'] = $this->message->schedule;
    }

    private function setSegments()
    {
        if (!empty($this->message->data->segment)) {
            $this->payload["included_segments"] = [];
            $this->payload["included_segments"][0] = $this->message->data->segment;
            return;
        }
        if (!empty($this->message->data->filters)) {
            $this->payload['filters'] = $this->message->data->filters;
            return;
        }

        $this->payload["included_segments"] = $this->defaultSegment;
    }

    public function toArray(): array
    {
        return $this->payload;
    }
}
