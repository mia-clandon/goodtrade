<?
/**
 * @var \common\models\Category[] $categories
 * @author Артём Широких kowapssupport@gmail.com
 */

use common\models\base\Category as BaseCategory;

$iterator = 0;
foreach ($categories as $activity) {
    $category_info = $activity->getChildCategoryList();
?>

<li class="ui-product-menu-item <?=$category_info[BaseCategory::PROPERTY_KEY_ICON_CLASS]?> <?=($iterator==0) ? 'ui-product-menu-item-active' : '';?>" data-id="<?=$category_info[BaseCategory::PROPERTY_KEY_ID];?>">
    <div class="ui-product-menu-item-wrap">
        <div class="ui-product-menu-item-icon"></div>
        <div class="ui-product-menu-item-label"><?=$category_info[BaseCategory::PROPERTY_KEY_TITLE];?></div>
    </div>
    <? if (isset($category_info[BaseCategory::CHILD_KEY_PROPERTY])) { ?>
    <!--second-level-submenu-is-open-->
    <ul class="ui-product-submenu">
        <!-- submenu-is-open-->
        <? foreach ($category_info[BaseCategory::CHILD_KEY_PROPERTY] as $item) {
            $has_child = (bool)$item[BaseCategory::PROPERTY_KEY_HAS_CHILD];
        ?>
        <li class="product-category-item ui-product-submenu-item <?if ($has_child){?>with-submenu<?}?>">
            <span data-activity-id="<?=$activity[BaseCategory::PROPERTY_KEY_ID];?>"
                  data-id="<?=$item[BaseCategory::PROPERTY_KEY_ID];?>">
                <?=$item[BaseCategory::PROPERTY_KEY_TITLE];?>
            </span>
            <? if ($has_child) { ?>
            <ul class="ui-product-submenu-submenu">
                <? foreach ($item[BaseCategory::CHILD_KEY_PROPERTY] as $sub_item) { ?>
                <li class="product-category-item ui-product-submenu-submenu-item">
                    <span data-activity-id="<?=$activity[BaseCategory::PROPERTY_KEY_ID];?>"
                          data-id="<?=$sub_item[BaseCategory::PROPERTY_KEY_ID];?>">
                        <?=$sub_item[BaseCategory::PROPERTY_KEY_TITLE];?>
                    </span>
                </li>
                <? } ?>
            </ul>
            <? } ?>
        </li>
        <? } ?>
        <a role="button" href="#" class="ui-product-menu-back submenu">← Назад</a>
    </ul>
    <? } ?>
</li>
<? $iterator++; } ?>