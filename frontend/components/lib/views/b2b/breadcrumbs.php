<?
use frontend\components\lib\b2b\Breadcrumbs;
/**
 * @var array $breadcrumbs
 */
?>
<div id="breadcrumbs" class="bar-top__breadcrumbs">
    <? foreach ($breadcrumbs as $breadcrumb) { ?>
    <div class="bar-top__breadcrumbs-item">
        <? if(!empty($breadcrumb[Breadcrumbs::KEY_ICON])) { ?>
        <div class="bar-top__breadcrumbs-item-icon bar-top__breadcrumbs-item-icon_<?= $breadcrumb[Breadcrumbs::KEY_ICON]; ?>"></div>
        <? } ?>
        <div class="bar-top__breadcrumbs-item-title">
            <a href="<?= $breadcrumb[Breadcrumbs::KEY_LINK]; ?>">
                <?= $breadcrumb[Breadcrumbs::KEY_TITLE]; ?>
            </a>
        </div>
    </div>
    <? } ?>
</div>