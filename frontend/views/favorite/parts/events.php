<?
/**
 * Вывод избарнных компании и товаров
 * @var \common\models\firms\Firm $firm
 * @var \common\models\Chrono[] $chronos
 */
?>
<div class="favorites-block">
    <div class="row">
        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
            <div class="favorites-block__header">
                <h2>Ближайшие события</h2>
                <a href="#" class="js-show-chronos">Смотреть историю</a>
            </div>
        </div>
    </div>
    <div class="favorites-block__content">
        <div class="chrono">
            <? foreach ($chronos as $chrono) { ?>
            <div class="row">
                <div class="chrono-event chrono-event-green chrono-event-filled">
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                        <div class="chrono-date"><?= date('j F', $chrono->created_at) ?></div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <div class="chrono-info">
                            <div class="chrono-info-title"><?= $chrono->title ?></div>
                            <!--<div class="chrono-info-text"><span>11:00 - 15:00</span><span>Не подтверждено</span></div>-->
                        </div>
                    </div>
                </div>
            </div>
            <? } ?>
        </div>
    </div>
</div>