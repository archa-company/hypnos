<?php

/**
 * Core do Projeto Morpheus do Grupo de trabalho CMS8x
 *
 * @package     Morpheus
 * @author      cms8x
 * @copyright   2023 Editora O Estado do Paraná
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Morpheus - Hypnos
 * Plugin URI:  https://gitlab.gazetadopovo.com.br/morpheus/hypnos
 * Description: Core - Regras de negócio e configurações do Projeto Morpheus
 * Version:     1.0
 * Author:      Tribuna PR, Clube Gazeta e Pinó
 * Author URI:  https://gitlab.gazetadopovo.com.br/morpheus
 * Text Domain: morpheus
 * License:     MIT
 */

//  Exit if accessed directly.
defined('ABSPATH') || exit;

if (!defined('MORPHEUS_CORE_PATH')) {
    define('MORPHEUS_CORE_PATH', plugin_dir_path(__FILE__));
}
if (!defined('MORPHEUS_CORE_URL')) {
    define('MORPHEUS_CORE_URL', plugin_dir_url(__FILE__));
}


require_once __DIR__ . '/vendor/autoload.php';
$core = Morpheus\Modules::getInstance();

register_activation_hook(__FILE__,      [$core, 'activation']);
register_deactivation_hook(__FILE__,    [$core, 'deactivation']);
