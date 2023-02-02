<?php
/**
 * @var Category[] $category_list
 * @var int $parent_id
 * @var int $parent_parent_id
 * @var Category $parent
 */
use common\models\Category;
?>
<div class="categories-panel">
    <div>
        <input type="text" placeholder="Поиск по категориям" class="form-control search"/>
    </div>
    <?
    // Блок выбора сферы деятельности.
    if ($parent_id === 0) { ?>
        <? foreach ($category_list as $category) {
            ?>
            <div class="col-xs-4 col-sm-6 col-md-4 col-lg-3 activity-block" data-category-id="<?= $category['id']; ?>">
                <a href="#" class="thumbnail <?= $category['icon_class']; ?>">
                    <?= $category['title']; ?>
                </a>
            </div>
        <? } ?>
    <? } else { ?>
        <div class="parent_category" data-parent-category-id="<?= $parent_parent_id ?>" data-category-id="<?= $parent->id ?>">
            <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
            <?= $parent->title ?>
        </div>
        <ul class="categories-list">
            <? foreach ($category_list as $category) {
            ?>
                <li data-category-id="<?= $category['id'];?>" class="<?= $category['has_child'] ? 'categories-list__category-item_parent' : 'categories-list__category-item';?>">
                    <?= $category['title'];?>
                </li>
            <? } ?>
        </ul>
    <? } ?>
</div>