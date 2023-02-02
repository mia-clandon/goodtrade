<?php

/**
 * @var common\models\MainSlider[] $slides
 * @var array $array_by_slide
 */

use common\models\MainSlider;
use yii\helpers\Url;

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800 ml-2">Слайдер</h1>
    </div>
</div>

<?php foreach ($slides as $i => $slide) {
    ?>
    <div class="row slider p-4">
        <div class="card col-lg-12 shadow-sm pt-0">
            <div class="row p-4 card-header" style="display: flex; flex-direction: row; align-items: center; ">
                <h3 class="m-0 font-weight-bold text-primary"> Слайд #<?= ++$i ?></h3>
                <a class="btn btn-primary ml-4" href="<?= Url::to(['slider/update', 'id' => $slide->id]) ?>">Редактировать</a>
                <a class="btn btn-danger ml-4" href="<?= Url::to(['slider/delete', 'id' => $slide->id]) ?>">Удалить</a>
            </div>
            <div class="row header ml-0 mt-2">
                <div class="col-md-2 h4">
                    Заголовок
                </div>
                <div class="col-md-3 h4">
                    Описание
                </div>
                <div class="col-md-2 h4">
                    Кнопка
                </div>
                <div class="col-md-2 h4">
                    Ком. к кнопке
                </div>
                <div class="col-md-3 h4">
                    Картинка
                </div>
            </div>
            <div class="row ml-0">
                <div class="col-md-2">
                    <?= $slide->title ?>
                </div>
                <div class="col-md-3">
                    <?= mb_substr($slide->description, 0, 50) ?>
                </div>
                <div class="col-md-2">
                    <?= $slide->button ?>
                </div>
                <div class="col-md-2">
                    <?= $slide->title ?>
                </div>
                <div class="col-md-3">
                    <img src="<?= $slide->image; ?>" height="100px" width="100px"/>
                </div>
            </div>
            <div class="row">
                <?
                /**
                 * @var MainSlider $part
                 */
                foreach ($array_by_slide[$slide->id] as $part) { ?>
                    <div class="col-md-6 element">
                        <div class="row header">
                            <div class="col-md-3">
                                Размер
                            </div>
                            <div class="col-md-3">
                                Тип
                            </div>
                            <div class="col-md-3">
                                Название
                            </div>
                            <div class="col-md-3">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <?= $part->getCurrentTypeText() ?>
                            </div>
                            <div class="col-md-3 description">
                                <?= $part->getCurrentTagText() ?>
                            </div>
                            <div class="col-md-3 description">
                                <?= mb_substr($part->title, 0, 20); ?>
                            </div>
                            <div class="col-md-3">
                                <a class="glyphicon glyphicon-pencil"
                                   href="<?= Url::to(['slider/update', 'id' => $part->id, 'slide_id' => $slide->id]) ?>"></a>
                                <a class="glyphicon glyphicon-remove js-remove"
                                   href="<?= Url::to(['slider/delete', 'id' => $part->id, 'slide_id' => $slide->id]) ?>"></a>
                            </div>
                        </div>
                    </div>
                <? } ?>
                <? if ($slide->isFull()) { ?>
                    <div class="col-md-6 full">
                        <span>Все ячейки заполнены</span>
                    </div>
                <? } else { ?>
                    <div class="col-md-6">
                        <a class="btn btn-primary mb-4 ml-2"
                           href="<?= Url::to(['slider/update', 'slide_id' => $slide->id]) ?>">Добавить элемент</a>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
<? } ?>

<div class="row slider">
    <div class="col-lg-12">
        <a href="<?= Url::to(['slider/update']) ?>" class="btn btn-primary m-3">Добавить слайд</a>
    </div>
</div>