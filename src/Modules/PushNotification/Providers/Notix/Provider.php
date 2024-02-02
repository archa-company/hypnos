<?php

namespace Morpheus\Modules\PushNotification\Providers\Notix;

use Morpheus\Modules\PushNotification\Contracts\ProviderInterface;
use Morpheus\Modules\PushNotification\Message;
use Morpheus\Shared\Traits\UseConfig;

class Provider implements ProviderInterface
{

    use UseConfig;

    private string $appId;
    private string $authToken;
    private string $apiBaseUrl;

    public function __construct()
    {
        $this->appId        = $this->getConfig('push_notix_app_id');
        $this->authToken    = $this->getConfig('push_notix_auth_token');
        $this->apiBaseUrl   = "http://notix.io/api";
    }

    public function sendMessage(Message $message): array
    {
        $payload = new MessageProvider($message);
        return $this->send($payload->toArray());
    }

    private function send(array $payload): array
    {
        $endpoint = "{$this->apiBaseUrl}/send?app={$this->appId}";
        $headers = [
            "Content-Type" => "application/json",
            "Authorization-Token" => $this->authToken,
            "Origin" => ''
        ];
        $body = wp_json_encode($payload);

        $response = wp_remote_post($endpoint, [
            'headers' => $headers,
            'body' => $body,
            'sslverify'   => false,
            'httpversion' => '1.0',
            'timeout'     => 60,
            'redirection' => 5,
            'blocking'    => true,
        ]);

        // Debugando envio
        // dd([
        //     'appId' => $this->appId,
        //     'authToken' => $this->authToken,
        //     'endpoint' => $endpoint,
        //     'headers' => $headers,
        //     'body' => $body,
        //     'response' => $response,
        // ]);

        $sentTerm = (!empty($payload['scheduled_date'])) ? 'agendanda' : 'criada';

        $responseData = (object) json_decode(wp_remote_retrieve_body($response));
        $successStatus = !isset($responseData->error_code);

        $noticeText = ($successStatus)
            ? "Notix: Campanha {$sentTerm} com sucesso."
            : "Notix: Não possível criar a campanha. {{$responseData->message}}";

        return [
            'provider' => 'notix',
            'message' => $payload,
            'response' => $responseData,
            'notice' => [
                'text' => $noticeText,
                'status' => $successStatus ? 'success' : 'warning'
            ],
        ];
    }

    /**
     * Script para o <head>
     * @return string
     */
    public function getHeadScript(): string
    {
        return "\t<script id=\"script\">const s = document.createElement(\"script\"); s.src = \"https://notix.io/ent/current/enot.min.js\"; s.onload = function (sdk) { sdk.startInstall({ \"appId\": \"{$this->appId}\", \"loadSettings\": true }) }; document.head.append(s);</script>\r\n";
    }

    public static function getCustomLink(): string
    {
        return "";
    }
}
