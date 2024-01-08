<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Modules\Core\Classes\Tracker\Tracker;
use Morpheus\Shared\Traits\FactoryMethod;
use Morpheus\Shared\Traits\HasHooks;
use Morpheus\Shared\Traits\UseConfig;

class AmpAnalytics
{
    use HasHooks, UseConfig, FactoryMethod;

    public function init()
    {
        $this->addAction('amp_post_template_head',          [$this, 'analyticsAdditional'], 11);
        $this->addAction('amp_post_template_footer',        [$this, 'privacyCookieBanner']);
        // $this->addFilter('amp_schemaorg_metadata',          ['Tribuna\\Front\\Seo', 'schemaOrgArray']);
        // $this->addAction('amp_post_template_head',          ['Tribuna\\Front\\Seo', 'pwa']);

        if (function_exists('amp_print_schemaorg_metadata')) {
            $this->removeAction('amp_post_template_head', 'amp_print_schemaorg_metadata');
        }
        if (function_exists('amp_add_generator_metadata')) {
            $this->removeAction('amp_post_template_head', 'amp_add_generator_metadata');
        }
        if (function_exists('wp_generator')) {
            $this->removeAction('amp_post_template_head', 'wp_generator');
        }
        $this->registerHooks();
    }

    public function analyticsAdditional()
    {
        $this->ga4();
        $this->ga4Business();
        $this->ga3();
        Tracker::factory()->displayAmp();
        $this->netdeal();
        $this->cxense();
    }

    public function ga4()
    {
        $gaId = $this->getConfig('analytics_ga4_id');
        if (empty($gaId)) return;
        $this->ga4Analytics($gaId);
    }

    public function ga4Business()
    {
        $gaId = $this->getConfig('analytics_ga4_business_id');
        if (empty($gaId)) return;
        $this->ga4Analytics($gaId);
    }

    public function ga3()
    {
        $gaId = $this->getConfig('analytics_ga3_id');
        if (empty($gaId)) return;
        $this->ga3Analytics($gaId);
    }

    public function ga4Analytics(string $measurementId)
    {
        echo "<amp-analytics type=\"gtag\" data-credentials=\"include\"><script type=\"application/json\">{\"vars\": {\"gtag_id\": \"{$measurementId}\",\"config\": {\"{$measurementId}\": {\"groups\": \"default\"}}}}</script></amp-analytics>";
    }

    public function ga3Analytics(string $accountId)
    {
        echo "<amp-analytics type=\"googleanalytics\"><script type=\"application/json\">{\"vars\": {\"account\": \"{$accountId}\"},\"triggers\": {\"trackPageview\": {\"on\": \"visible\",\"request\": \"pageview\"}}}</script></amp-analytics>";
    }

    public function netdeal(array $sections = [])
    {
        $accountId = $this->getConfig('analytics_netdeal_id');
        if (empty($accountId)) return;
        $sections = json_encode($sections);
        echo "<amp-analytics config=\"https://www.netdeal.com.br/resources/js/amp-analytics-config.json\" data-include-credentials=\"include\"><script type=\"application/json\">{\"vars\": {\"ndid\": \"{$accountId}\",\"restriction_type\": \"default\",\"og_type\": \"article\",\"page_sections\": {$sections}}}</script></amp-analytics>";
    }

    public function cxense()
    {
        $accountId = $this->getConfig('analytics_cxense_id');
        if (empty($accountId)) return;
        echo "<amp-analytics type=\"cxense\"><script type=\"application/json\">{\"vars\":{\"siteId\":\"$accountId\"}}</script></amp-analytics>";
    }

    public function privacyCookieBanner()
    {
        $id = $this->getConfig('lgpd_privacy_tools_amp_id');
        echo "<amp-consent layout=\"nodisplay\" id=\"consent-element\">
            <script type=\"application/json\">{ \"consentInstanceId\": \"privacytools-consent\", \"consentRequired\": true, \"checkConsentHref\": \"https://cdn.privacytools.com.br/public_api/banner/log-amp/{$id}\", \"promptUI\": \"consent-ui\", \"onUpdateHref\": \"https://cdn.privacytools.com.br/public_api/banner/log-amp/<?= $id ?>\" }</script>
            <div id=\"consent-ui\" class=\"consentContainer\"><div class=\"consentMessage\">Utilizamos cookies para oferecer a melhor experiência de navegação, de acordo com a nossa <a class=\"cookie-ba-link\" href=\"https://www.tribunapr.com.br/politica-de-privacidade/\">Política de Privacidade</a>. Ao continuar navegando, você concorda com estas condições.</div><div class=\"consentButtons\"><button class=\"consentAccept\" on=\"tap:consent-element.accept\" role=\"button\">Continuar</button></div></div>
        </amp-consent>\r\n";
    }
}
