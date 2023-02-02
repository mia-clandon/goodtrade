<?php
/**
 * Строка комерческого запроса над футером.
 * @var \common\models\goods\Product $product
 * @author Артём Широких kowapssupport@gmail.com
 */
?>

<div id="bottom-controls" class="has-border">
    <div class="container">
        <div class="row">
            <div class="col-xs-6">
                <div class="pull-left">
                    <? if (!$product->isMine()) { ?>
                    <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="favorite_product<?=\Yii::$app->user->id?>" class="action js-keeper">
                        <span class="action-icon action-icon-hold"></span>
                        <span class="action-text">Отложить</span>
                    </a>
                    <a role="button" href="#" data-id="<?= $product->id; ?>" data-key="compare<?=\Yii::$app->user->id?>" class="action js-keeper">
                        <span class="action-icon action-icon-compare"></span>
                        <span class="action-text">Сравнить</span>
                    </a>
                    <? } ?>
                    <a role="button" href="#" class="action">
                        <span class="action-icon action-icon-share"></span>
                        <span class="action-text">Поделиться</span>
                    </a>
                </div>
                <? if (!$product->isMine()) { ?>
                <div class="pull-right">
                    <div class="popup-dropdown">
                        <button data-vertical-align="bottom" data-horizontal-align="right" data-id="<?= $product->id; ?>" class="btn btn-blue popup-toggle commercial-popup">
                            <span>Коммерческий запрос</span>
                            <i class="icon icon-commerce-white"></i>
                            <i class="icon icon-complete"></i>
                        </button>
                    </div>
                </div>
                <? } ?>
            </div>
        </div>
    </div>
</div>