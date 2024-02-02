<?php

namespace Morpheus\Modules\PushNotification\Providers\OneSignal;

use Morpheus\Modules\PushNotification\Contracts\ProviderInterface;
use Morpheus\Modules\PushNotification\Message;
use Morpheus\Shared\Traits\UseConfig;
use Tribuna\Util\Dev;

class Provider implements ProviderInterface
{

    use UseConfig;

    private string $appId;
    private string $restKey;
    private string $apiBaseUrl;

    public function __construct()
    {
        $this->appId        = $this->getConfig('push_onesignal_app_id');
        $this->restKey      = $this->getConfig('push_onesignal_rest_key');
        $this->apiBaseUrl   = "https://onesignal.com/api/v1";
    }

    public function sendMessage(Message $message): array
    {
        $payload = new MessageProvider($message);
        return $this->send($payload->toArray());
    }

    private function send(array $payload): array
    {
        // return json_encode([
        //     'response' => 'debug mode. don`t send to onesignal'
        // ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->apiBaseUrl}/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=utf-8",
            "Authorization: Basic " . $this->restKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = (object) json_decode($response);
        $responseSuccess = isset($responseData->recipients);

        $sentTerm = (!empty($payload['send_after'])) ? 'agendanda' : 'enviada';
        $noticeText = ($responseSuccess)
            ? "OneSignal: Mensagem {$sentTerm} com sucesso para {$responseData->recipients} destinatários"
            : "OneSignal: Não possível criar a mensagem.";

        return [
            'provider' => 'onesignal',
            'message' => $payload,
            'response' => $responseData,
            'notice' => [
                'text' => $noticeText,
                'status' => $responseSuccess ? 'success' : 'warning'
            ],
        ];
    }

    public function playerEditTag(string $playerId, array $data)
    {
        $data = json_encode(array(
            'app_id'    => $this->appId,
            'tags'      => $data
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->apiBaseUrl}/players/{$playerId}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=utf-8",
            "Authorization: Basic " . $this->restKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Retorna o cadastro do usuário no OneSignal
     * @param string $playerId
     * @return string
     */
    public function getPlayer(string $playerId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->apiBaseUrl}/players/{$playerId}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=utf-8",
            "Authorization: Basic " . $this->restKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Retorna os players do OneSignal em lote
     * @param int $offset Padrão é 0
     * @param int $limit O Máximo e padrão é 300
     * @return string
     */
    public function getPlayers(int $offset = 0, int $limit = 300)
    {
        $params = http_build_query([
            "app_id"    => $this->appId,
            "limit"     => $limit,
            "offset"    => $offset
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$this->apiBaseUrl}/players?{$params}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=utf-8",
            "Authorization: Basic " . $this->restKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Script para o <head>
     * @return string
     */
    public function getHeadScript(): string
    {
        return "\t<script>window.OneSignal = window.OneSignal || []; OneSignal.push(function(){ OneSignal.init({ appId: \"{$this->appId}\",}); });</script>\r\n";
        // return "\t<script src=\"https://cdn.onesignal.com/sdks/OneSignalSDK.js\" async defer></script>\r\n\t<script>window.OneSignal = window.OneSignal || []; OneSignal.push(function(){ OneSignal.init({ appId: \"{$oneSignalAppId}\",}); });</script>\r\n";
    }

    public static function getCustomLink(): string
    {
        return "<div class=\"onesignal-customlink-container\"></div>";
    }
}
