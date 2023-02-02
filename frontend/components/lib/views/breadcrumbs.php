<?
use frontend\components\lib\Breadcrumbs;
/**
 * @var array $breadcrumbs
 */
?>
<div id="breadcrumbs" class="breadcrumbs">
    <? foreach ($breadcrumbs as $breadcrumb) { ?>
    <div class="breadcrumbs-item">
        <a href="<?= $breadcrumb[Breadcrumbs::KEY_LINK]; ?>">
            <?= $breadcrumb[Breadcrumbs::KEY_TITLE]; ?>
        </a>
    </div>
    <? } ?>
</div>