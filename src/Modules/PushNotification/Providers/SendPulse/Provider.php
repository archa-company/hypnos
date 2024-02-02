<?php

namespace Morpheus\Modules\PushNotification\Providers\SendPulse;

use Morpheus\Modules\PushNotification\Contracts\ProviderInterface;
use Morpheus\Modules\PushNotification\Message;
use Morpheus\Shared\Traits\UseConfig;
use Tribuna\Util\Dev;

class Provider implements ProviderInterface
{
    use UseConfig;

    const TRANSIENT_ACCESS_TOKEN = 'sendpulse_access_token';
    const BASE_URL = 'https://api.sendpulse.com';
    public $baseUrl;
    public $websiteId;

    public function __construct()
    {
        $this->baseUrl          = self::BASE_URL;
        $this->websiteId        = $this->getConfig('push_sendpulse_website_id');
    }

    public function sendMessage(Message $message): array
    {
        $payload = new MessageProvider($message);
        return $this->send($payload->toArray());
    }

    private function authenticate()
    {
        if (false !== ($token = get_transient(self::TRANSIENT_ACCESS_TOKEN))) return $token;

        $credentials = json_encode([
            'grant_type'            => 'client_credentials',
            'client_id'             => $this->getConfig('push_sendpulse_client_id'),
            'client_secret'         => $this->getConfig('push_sendpulse_client_secret'),
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/oauth/access_token");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=utf-8",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $credentials);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response);
        set_transient(self::TRANSIENT_ACCESS_TOKEN, $data->access_token, MINUTE_IN_SECONDS * 28);

        return $data->access_token;
    }

    private function send(array $payload): array
    {
        $accessToken = $this->authenticate();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->baseUrl}/push/tasks");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=utf-8",
            "Authorization: Bearer {$accessToken}"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = (object) json_decode($response);
        $successStatus = !isset($responseData->error_code);

        $noticeText = ($successStatus)
            ? "SendPulse: Campanha registrada com sucesso com o id {$responseData->id}"
            : "SendPulse: Não possível criar a campanha. {{$responseData->message}}";

        return [
            'provider' => 'sendpulse',
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
        $scriptHash = $this->getConfig('push_sendpulse_script_hash');
        return "\t<script charset=\"UTF-8\" src=\"https://web.webpushs.com/js/push/{$scriptHash}.js\" async defer></script>\r\n";
    }

    public static function getCustomLink(): string
    {
        return "";
        // return "<div class=\"onesignal-customlink-container\"></div>";
    }
}
