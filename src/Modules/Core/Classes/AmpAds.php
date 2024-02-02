<?php

namespace Morpheus\Modules\Core\Classes;

use Morpheus\Shared\Traits\FactoryMethod;
use Morpheus\Shared\Traits\HasHooks;

class AmpAds
{

    use HasHooks, FactoryMethod;

    const SLOT_ID = '595692431';
    const SLOT_ID_PREFIX = 'umdois';

    public function init(): void
    {
        $this->addAction('the_content',                   [$this, 'contentAd'], 999999);
        $this->registerHooks();
    }

    public function getSectionId(): string
    {
        $section = 'home';
        return str_replace('-', '_', $section);
    }

    /**
     * Adiciona um banner no primeiro parágrafo do post na versão AMP
     * @param string $content
     * @return string
     */
    public function contentAd($content)
    {
        if (!amp_is_request()) return $content;

        $countAds = 3;

        $adElement = self::template('m-pos-3', 'continua após a publicidade');
        $content = preg_replace('/(<\/p>)/', $adElement . '</p>', $content, 1);
        // return $content;

        $content = explode("</p>", $content);
        $countParaghaphs = count($content);

        if ($countParaghaphs > 3) {
            $countAds++;
            $adElement2 = self::template("m-pos-{$countAds}", "continua após a publicidade");
            $content[3] .= $adElement2;
            // $content[4] .= Teads::amp();
        }
        if ($countParaghaphs > 6) {
            $countAds++;
            $adElement3 = self::template("m-pos-{$countAds}", "continua após a publicidade");
            $content[6] .= $adElement3;
        }
        // Se não tiver adicionado nenhum banner entre os parágrados
        // Adiciona ao final do conteúdo
        if ($countAds <= 3) {
            $adElement = self::template("m-pos-{$countAds}", 'continua após a publicidade');
            $content[] = $adElement;
        }

        $result = implode("</p>", $content);
        return $result;
    }

    public function template($pos, $label = true, int $width = 300, int $height = 250, string $tag = null, bool $withContainer = true)
    {
        $slotId = self::SLOT_ID;
        $slotPrefix = self::SLOT_ID_PREFIX;
        $section = self::getSectionId();
        $tag = $tag ? "/{$tag}" : ($section ? "/{$section}" : "");
        $labelText = ($label && !is_bool($label)) ? $label : "publicidade";
        $label = $label ? "<span class=\"ads__title\">{$labelText}</span>" : "";
        $permalink = amp_get_current_url();

        $html = "";
        if ($withContainer) $html .= "<div class=\"ads ads--fullwidth\">";
        $html .= "{$label}
        <amp-ad
            width=\"{$width}\"
            height=\"{$height}\"
            type=\"doubleclick\"
            data-slot=\"/{$slotId}/{$slotPrefix}{$tag}\"
            data-enable-refresh=\"30\"
            data-lazy-fetch=\"true\"
            data-loading-strategy=\"1\"
            json='{\"targeting\":{\"keyword\":[\"amp\"],\"expble\":[\"1\"],\"native\":[\"0\"],\"pos\":[\"{$pos}\"],\"origin\":\"{$permalink}\"}}'
            >
            <div class=\"loader\"></div>
        </amp-ad>";
        if ($withContainer) $html .= "</div>";
        return $html;
    }

    public function sticky($tag = null)
    {
        $banner = self::template('m-pos-2', false, 320, 50, $tag, false);
        return sprintf('<amp-sticky-ad layout="nodisplay">%s</amp-sticky-ad>', $banner);
    }
}
