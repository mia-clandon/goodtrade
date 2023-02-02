<?
/**
 * Вывод избранных компаний и товаров
 * @var \common\models\firms\Firm[] $firms
 * @var \common\models\firms\Firm $current_firm
 * @var array $firm_product_array
 * @var int $firm_id
 */

use common\modules\image\helpers\Image as ImageHelper;

?>
<div class="inner-sidebar-favorites">
    <div class="inner-sidebar-favorites-tumbler is-hidden">
        <div class="tumbler">
            <a href="#" role="button" class="tumbler-button tumbler-button-active">
                <span>Компании</span><small>6</small>
            </a>
            <a href="#" role="button" class="tumbler-button">
                <span>Товары</span><small>4</small>
            </a>
            <input type="hidden" value="0" name="undefined">
        </div>
    </div>

    <ul class="inner-sidebar-favorites-menu">
        <? foreach ($firms as $firm) {

            $first_activity = $firm->getCategories()->one();

            $image = null;
            if ($firm->image) {
                $image = ImageHelper::i()->generateRelativeImageLink($firm->image, 40, 40);
            }

            if ($first_activity && null === $image) {
                $image = '/img/product_category_stubs/'. $first_activity->icon_class.'.png';
            }
            ?>
            <li class="inner-sidebar-favorites-menu__item<?=isset($current_firm) && $firm->id == $current_firm->id?' is-active':''?>">
                <div class="inner-sidebar-favorites-menu__item-image-container">
                    <a href="<?= Yii::$app->urlManager->createUrl(['favorite/index', 'firm_id' => $firm->id ]);?>">
                        <img src="<?= $image ?>" alt="Логотип компании" class="inner-sidebar-favorites-menu__item-image">
                    </a>
                </div>
                <div class="inner-sidebar-favorites-menu__item-name-container">
                    <a href="<?= Yii::$app->urlManager->createUrl(['favorite/index', 'firm_id' => $firm->id ]);?>" class="inner-sidebar-favorites-menu__item-name"><?= $firm->getTitle() ?></a>
                    <span class="inner-sidebar-favorites-menu__item-description">Отложено <?=count($firm_product_array[$firm->id])?> товаров</span>
                    <a href="#" class="inner-sidebar-favorites-menu__item-delete-link js-keeper" data-id="<?=implode(',', $firm_product_array[$firm->id])?>" data-key="favorite_product<?=\Yii::$app->user->id?>">Удалить из избранного</a>
                </div>
            </li>
        <? } ?>

        <? if(isset($firm_product_array[0])) { ?>
            <li class="inner-sidebar-favorites-menu__menu-item<?= !is_null($firm_id) && $firm_id==0?' is-active':''?>">
                <div class="inner-sidebar-favorites-menu__item-image-container">
                    <a href="<?= Yii::$app->urlManager->createUrl(['favorite/index', 'firm_id' => 0]);?>">
                        <img src="" alt="Логотип компании" class="inner-sidebar-favorites-menu__item-image">
                    </a>
                </div>
                <div class="inner-sidebar-favorites-menu__item-name-container">
                    <span class="inner-sidebar-favorites-menu__item-name">Нет компании</span>
                    <span class="inner-sidebar-favorites-menu__item-description">Отложено <?=count($firm_product_array[0])?> товара</span>
                    <a href="#" class="inner-sidebar-favorites-menu__item-delete-link js-keeper" data-id="<?=implode(',', $firm_product_array[0])?>" data-key="favorite_product<?=\Yii::$app->user->id?>">Удалить из избранного</a>
                </div>
            </li>
        <? } ?>
    </ul>
</div>