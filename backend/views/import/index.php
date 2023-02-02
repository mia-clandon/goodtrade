<?php
/**
 * @var \yii\data\ActiveDataProvider $data_provider
 */

use yii\grid\GridView;
use yii\helpers\Html;

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Список прайс листов</h1>
        <div class="alert alert-warning alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Закрыть"><span aria-hidden="true">×</span></button>
            <strong>Внимание!</strong> Прайс листы со статусом "обработан" удаляются автоматически в течении 3х суток.
        </div>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Список прайс листов</h6>
    </div>
    <div class="card-body">
        <?php

print GridView::widget([
    'dataProvider' => $data_provider,
    'tableOptions'=>['class'=>'table table-bordered dataTable'],
    'columns' => [
        'id',
        [
            'class' => 'yii\grid\DataColumn',
            'label' => 'Импорт',
            'format' => 'html',
            'value' => function($model) {
                /** @var \common\models\PriceQueue $model */
                $links = [
                    Html::a('Перейти к импорту "'.htmlspecialchars($model->getFileName()).'"', ['import/import', 'id' => $model->id]),
                    Html::a('Скачать прайс лист "'.htmlspecialchars($model->getFileName()).'"', ['import/download', 'id' => $model->id]),
                ];
                return implode(' | ', $links);
            }
        ],
        'firm.title',
        [
            'class' => 'yii\grid\DataColumn',
            'label' => 'Статус',
            'format' => 'html',
            'value' => function($model) {
                /** @var \common\models\PriceQueue $model */
                $links = [];
                if ($model->isNewStatus()) {
                    $links = [
                        Html::a('Сменить статус на `в обработку`', [
                                'import/change-status',
                                'id' => $model->id,
                                'new_status' => \common\models\PriceQueue::STATUS_PROCESSING,
                        ]),
                    ];
                }
                if ($model->isProcessingStatus()) {
                    $links = [
                        Html::a('Сменить статус на `обработан`', [
                            'import/change-status',
                            'id' => $model->id,
                            'new_status' => \common\models\PriceQueue::STATUS_PROCESSED,
                        ]),
                    ];
                }
                return $model->getStatusName($model->status)
                    . '<br />'. implode(' | ', $links);
            }
        ],
//        [
//            'class' => \yii\grid\ActionColumn::class,
//            'template' => '{delete}',
//        ]
    ],
]);

?>
</div>
<div class="p-0">
    <?php
        print Html::a('Добавить прайс лист', ['import/add'], ['class' => 'btn btn-primary ml-4 mb-2']);
    ?>
</div>
</div>
