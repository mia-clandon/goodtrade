<?
/**
 * Слайдер с подкатегориями сферы деятельности
 * @var \common\models\Category $category
 * @var \common\models\Category[] $children
 * @var \common\models\Category[] $siblings
 * @author yerganat
 */

use yii\helpers\Url;

?>
<? if($children) { ?>
<div class="buttons-slider-block">
    <div class="buttons-slider">
        <? foreach ($children as $child) { ?>
            <a href="<?= Url::to(['category/show', 'id' => $child->id])?>" class="button button_normalcase"><span class="button__text"><?=$child->title?></span></a>
        <? } ?>
    </div>
</div>
<? } ?>