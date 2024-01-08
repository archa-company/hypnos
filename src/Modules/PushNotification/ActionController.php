<?php

namespace Morpheus\Modules\PushNotification;

use Morpheus\Shared\Traits\UseConfig;
use WP_REST_Request;
use WP_REST_Response;

class ActionController
{

    use UseConfig;

    /**
     * Controller da API de Mensagem
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function __invoke(WP_REST_Request $request): WP_REST_Response
    {
        $data = $request->get_json_params();
        $data = Helpers::setBlogId($data);
        $data = Helpers::setImage($data);

        /**
         * Criar uma mensagem
         */
        $message = new Message($data);
        $providerEnable = $this->getConfig('push_provider');
        if (!$message->getProvider()) $message->setProvider($providerEnable);

        /**
         * Faz o envio pelo provider escolhido
         */
        $provider = Module::providerFactory($message->getProvider());
        $providerResponse = $provider->sendMessage($message);

        /**
         * Salva os dados da mensagem em PostMeta
         */
        $meta = [
            'author'        => Helpers::getAuthor(),
            'response'      => $providerResponse,
            'datetime'      => date_i18n('Y-m-d H:i:s')
        ];
        if ($postId = $data['postId'] ?? null) add_post_meta($postId, 'push_message', $meta);

        /**
         * Retorna para o blog atual
         */
        if (is_multisite()) restore_current_blog();

        /**
         * Seta o response
         */
        $response = new WP_REST_Response($meta);
        $response->set_status(200);

        return $response;
    }
}
