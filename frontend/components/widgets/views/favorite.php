<?php
/**
 * @var int $favorite_count
 * @var array $favorite_data
 */
use frontend\components\widgets\Favorite;
use common\libs\Declension;
?>

<li class="snippets-item dropdown">
    <a role="button" href="#" class="action dropdown-toggle">
        <span class="action-icon action-icon-favorite"><span id="favorite_count" class="action-count"><?= $favorite_count ?></span></span>
    </a>

    <?php if ($favorite_count == 0) { ?>
        <div class="dropdown-menu dropdown-menu-favorite dropdown-menu-notify dropdown-menu-notify_empty">
            <div class="dropdown-menu-head">
                <div class="dropdown-menu-title">Избранное</div>
            </div>
            <div class="dropdown-menu-body">
                <div class="dropdown-menu-notify__image"></div>
                <div class="dropdown-menu-notify__text">
                    <p>Вы пока не добавили в избранное<br>ни одну компанию или товар</p>
                </div>
            </div>
        </div>
    <? } else { ?>
        <div class="dropdown-menu dropdown-menu-favorite dropdown-menu-notify">
            <div class="dropdown-menu-head">
                <div class="dropdown-menu-title">Избранное</div>
            </div>
            <div class="dropdown-menu-body has-scrollbar">
                <div class="dropdown-menu-content">
                    <? foreach ($favorite_data as $favorite_item) { ?>
                    <div class="notify-company">
                        <div class="notify-company-content">
                            <div class="notify-company-message"><a href="<?= Yii::$app->urlManager->createUrl(['favorite/index', 'firm_id' => $favorite_item[Favorite::FIRM_ID_PROPERTY]])?>"><?= $favorite_item[Favorite::FIRM_TITLE_PROPERTY] ?></a></div>
                            <div class="notify-company-date"><?= Declension::number($favorite_item[Favorite::PRODUCT_COUNT_PROPERTY], 'товар', 'товаров', 'товар', true);?></div>
                        </div>
                    </div>
                    <? } ?>
                </div>
            </div>
        </div>
    <? } ?>
</li>