<?php
/**
 * @var int $compare_count
 * @var \common\models\goods\Product[] $compare_data
 */
use frontend\components\widgets\Compare;
use common\libs\Declension;
?>

<li class="snippets-item dropdown">
    <a role="button" href="#" class="action dropdown-toggle <?//=($compare_count > 0) ? 'is-active' : '';?>">
        <span class="action-icon action-icon-compare">
            <span id="compare" class="action-count"><?= $compare_count; ?></span>
        </span>
    </a>

    <?php if ($compare_count == 0) { ?>

        <div class="dropdown-menu dropdown-menu-compare dropdown-menu-compare_empty">
            <div class="dropdown-menu-head">
                <div class="dropdown-menu-title">Сравнения</div>
            </div>
            <div class="dropdown-menu-body">
                <div class="dropdown-menu-compare__image"></div>
                <div class="dropdown-menu-compare__text">
                    <p>Вы не создали<br>ни одного сравнения</p>
                    <p><a href="#">Что такое сравнения?</a></p>
                </div>
            </div>
        </div>

    <? } else { ?>
        <div class="dropdown-menu dropdown-menu-compare">
            <div class="dropdown-menu-head">
                <div class="dropdown-menu-title">Сравнения</div>
            </div>
            <div class="dropdown-menu-body">
                <ul class="compare-list">
                    <? foreach ($compare_data as $compare_item) { ?>
                        <li class="compare-list-item">
                            <a href="<?= Yii::$app->urlManager->createUrl(['compare', 'category' => $compare_item[Compare::CATEGORY_ID_PROPERTY]])?>" class="compare-list-item-title">
                                <?= $compare_item[Compare::CATEGORY_TITLE_PROPERTY];?>
                            </a>
                            <?//todo;?>
                            <div class="compare-list-item-text">
                                <?= Declension::number($compare_item[Compare::PRODUCT_COUNT_PROPERTY], 'наименование', 'наименования', 'наименований', true);?>
                            </div>
                            <span class="compare-list-item-count js-keeper" data-id="<?= $compare_item[Compare::PRODUCT_IDS_PROPERTY] ?>" data-key="compare<?=\Yii::$app->user->id?>">
                                <a href="#">Удалить сравнение</a>
                            </span>
                        </li>
                    <? } ?>
                </ul>
            </div>
        </div>
    <? } ?>
</li>