<?
/**
 * Блок с категориями
 * @var \common\models\Category $category
 * @var \common\models\Category[] $children
 * @var \common\models\Category[] $siblings
 * @author yerganat
 */

use common\models\Category;
use yii\helpers\Url;


$SUB_CATEGORY_LIMIT = 6;

?>
<nav class="inner-bar">
    <? if($children) { ?>
        <div class="list-title">Каталог</div>
        <ul class="linear-list">
            <? foreach ($children as $child) { ?>
            <li>
                <a href="<?= Url::to(['category/show', 'id' => $child->id])?>"><?=$child->title?></a>
                <ul>
                    <?

                    $more_link  = false;
                    $sub_categories = Category::find()
                        ->where(['parent' => $child->id])
                        ->all();

                    foreach ($sub_categories as $i => $sub_category) {
                        if($i >= $SUB_CATEGORY_LIMIT-1 ){
                            $more_link = true;
                        }
                        ?>
                        <li <?= $more_link?'style="display: none"':'' ?> class="<?= $more_link?'hidden"':'' ?>"><a href="<?= Url::to(['category/show', 'id' => $sub_category->id])?>"><?=$sub_category->title?></a></li>
                    <? }
                       if ($more_link) {
                           echo '<li class="list-more-link"><a href="#">Еще</a></li>';
                           echo '<li class="list-less-link" style="display: none"><a href="#">Скрыть</a></li>';
                       }
                    ?>
                </ul>
            </li>
            <? } ?>
        </ul>
    <? } ?>

    <? if($siblings) { ?>
        <div class="list-title">Смотрите так же</div>

        <? if($category->parent) { ?>
            <div class="breadcrumbs">
                <div class="breadcrumbs__item">
                    <a href="<?= Url::to(['category/show', 'id' => $category->getActivity()->id])?>" class="breadcrumbs__item-icon breadcrumbs__item-icon_<?=$category->getActivity()->icon_class?>"></a>
                </div>
                <div class="breadcrumbs__item">
                    <div class="breadcrumbs__item-title">
                        <a href="<?=Url::to(['category/show', 'id' => $category->parent])?>"><?=$category->getParent()->one()->title?></a>
                    </div>
                </div>
                <div class="breadcrumbs__item"></div>
            </div>
        <? } ?>
        <ul class="linear-list">
            <? foreach ($siblings as $sibling) {
                ?>
                <li>
                    <a href="<?= Url::to(['category/show', 'id' => $sibling->id])?>"><?=$sibling->title?></a>
                </li>
            <? } ?>
        </ul>
    <? } ?>
</nav>
