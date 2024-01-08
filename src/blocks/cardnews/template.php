<?php

use Morpheus\blocks\cardnews\CardNews;
use Morpheus\Shared\Dev;

$post = get_field("postObject");

$cardnews   = new CardNews();
$card       = $cardnews->toObject();

$isAdmin    = (!empty($is_preview) && is_bool($is_preview));
$block      = $block ?? ["id" => "block_" . sha1($card->postId)];

$styles = [
    "cardnews",
    "cardnews--{$card->layout}",
    "cardnews--{$card->format}",
    "cardnews--{$card->size}"
];
if ($card->inverted)                array_push($styles, 'cardnews--inverted');
if ($card->boxed)                   array_push($styles, 'cardnews--boxed');
if (empty($card->image))            array_push($styles, 'cardnews--noimage');
if (!empty($card->sponsored))       array_push($styles, "cardnews--{$card->sponsored}");
if (!empty($block['className']))    array_push($styles, $block['className']);
if ($isAdmin)                       array_push($styles, 'is-admin');
$className = implode(" ", $styles);
?>
<article class="<?= $className ?>">
    <div class="cardnews__wrapper">
        <?php if ($card->image) : ?>
            <?php if ($card->layout == 'highlight') : ?>
                <div class="cardnews__image" style="background-image: url(<?= $card->image ?>)"></div>
            <?php else : ?>
                <div class="cardnews__image"><img src="<?= $card->image ?>" alt="<?= $card->title ?>"></div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="cardnews__content">
            <div class="cardnews__category"><span><?= $card->hat ?></span></div>
            <h2 class="cardnews__title"><span><?= $card->title ?></span></h2>
            <?php if ($card->sponsored && $card->sponsorName) : ?>
                <div class="cardnews__sponsor">por <?= $card->sponsorName ?></div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    if (!empty($card->relateds)) :
        echo "<ul class=\"cardnews__relateds\">";
        foreach ($card->relateds as $related) :
            $related = (object) $related;
            echo "<li><span>{$related->title}</span></li>";
        endforeach;
        echo "</ul>";
    endif;
    ?>
</article>
<?php
// Dev::debug($card);
// Dev::debug($block);
