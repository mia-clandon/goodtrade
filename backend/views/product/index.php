<?php

/**
 * @var \yii\data\ActiveDataProvider $data_provider
 * @var string $search_form
 * @var string $indexer_form
 */

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

use backend\components\form\controls\Selectize;
use common\libs\form\components\Option;
use common\models\goods\Product;

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Список товаров</h1>
    </div>
</div>

<!-- Модальное окно для переиндексации { -->
<div id="update-index-modal" style="display:none">
    <div class="container" style="width: 100%">
        <div class="row">
            <div class="col-md-12">
                <?=$indexer_form;?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 parts-table"></div>
        </div>
    </div>
</div>
<!-- } -->

<?

/** Форма поиска. */
echo $search_form;

// форма действий над выбранными товарами.
ActiveForm::begin(['action' => Url::to('/product/actions')]);

?>
<div style="overflow-x: auto;" class="card shadow mt-4 mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Список товаров</h6>
        </div>
        <div class="card-body">
            <?php
                /** Список товаров. */
                print GridView::widget([
                    'dataProvider' => $data_provider,
                    'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                    ],
                    'id',
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'title',
                        'format' => 'html',
                        'contentOptions' => ['style' => 'max-width: 40%;'],
                        'value' => function($model) {
                            /** @var $model Product */
                            return Html::a($model->getTitle(), Url::to(['product/update', 'id' => $model->id]));
                        },
                    ],
                    // организация
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'firm_id',
                        'format' => 'html',
                        'label' => 'Организация, автор',
                        'value' => function($model) {
                            /** @var $model Product */
                            /** @var \common\models\firms\Firm $firm */
                            $firm = $model->getFirm()->one();
                            if ($firm) {
                                return Html::a($firm->title, Url::to(['firm/update', 'id' => $firm->id]));
                            }
                            else {
                                return '';
                            }
                        }
                    ],
                    // статус
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'status',
                        'format' => 'html',
                        'value' => function($model) {
                            /** @var $model Product */
                            return '<span class="label label-danger">'.$model->getCurrentStatusText().'</span>';
                        }
                    ],
                    // из импорта?
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'from_import',
                        'format' => 'html',
                        'value' => function($model) {
                            /** @var $model Product */
                            return $model->from_import ? 'Да' : 'Нет';
                        }
                    ],
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{update} {delete}',
                    ]
        ],
    ]);
            ?>
</div>
<div class="action-form shadow">
        <div class="card-body">
            <?
                $action_control = (new Selectize())
                    ->setTitle('Действие над выбранными товарами')
                    ->setPlaceholder('---')
                    ->setName('action')
                    ->setOptions([
                        (new Option('', '')),
                        (new Option('remove', 'Удалить товары'))
                    ])
                ;
                print $action_control->render();
                print Html::submitButton('Выполнить', ['class' => 'btn btn-danger']);
            ?>
        </div>
</div>
    </div>
<?
ActiveForm::end();
?>
<div class="p-4">
    <?
        print Html::a('Добавить товар', ['product/update'], ['class' => 'btn btn-primary']);
    ?>
</div>
