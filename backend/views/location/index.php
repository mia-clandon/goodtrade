<?php
/**
 * @var \yii\data\ActiveDataProvider $data_provider
 * @var $search_form string
 */

use yii\grid\GridView;

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Города / регионы</h1>
    </div>
</div>
<div class="card shadow mb-4">
<?=$search_form;?>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Города/регионы</h6>
    </div>
    <div class="card-body">
    <div class="table-responsive">
        <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
            <?php
                print GridView::widget([
                'dataProvider' => $data_provider,
                'tableOptions'=>['class'=>'table table-bordered dataTable'],
                'columns' => [
                    'id',
                    [
                        'class' => 'yii\grid\DataColumn',
                        'label' => 'Название города',
                        'attribute' => 'title',
                        'format' => 'html',
                        'value' => function($model) {
                            /** @var $model \common\models\Location */
                            return \yii\helpers\Html::a($model->title, \yii\helpers\Url::to(['location/update', 'id' => $model->id]));
                        }
                    ],
                    [
                        'class' => 'yii\grid\DataColumn',
                        'label' => 'Область',
                        'attribute' => 'region',
                        'format' => 'html',
                        'value' => function($model) {
                            /** @var $model \common\models\Location */
                            $regions = array_flip($model->getPossibleRegions());
                            return \yii\helpers\Html::a(
                                (isset($regions[$model->region])) ? $regions[$model->region] : 'Не указана область'
                                , \yii\helpers\Url::to(['location/update', 'id' => $model->id])
                            );
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
    </div>
</div>
</div>
