<?
/**
 * @var $data_provider
 * @var $model \common\models\Vocabulary
 */

use yii\grid\GridView;
use yii\bootstrap\Html;

use common\models\Vocabulary;
?>

    <div class="row">
        <div class="col-lg-12">
            <h2 class="h-2 mb-2 text-gray-800">Список характеристик</h2>
        </div>
    </div>

    <div class="alert alert-warning alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Закрыть"><span
                    aria-hidden="true">&times;</span></button>
        <strong>Внимание!</strong> При удалении характеристики удалятся все её значения !
    </div>

    <div class="modal fade" id="modal-term-value" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <!-- сюда будет подгружаться форма. -->
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Список характеристик</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <?php
                    print GridView::widget([
                        'dataProvider' => $data_provider,
                        'tableOptions'=>['class'=>'table table-bordered dataTable', 'id' => 'dataTable', 'width'=>'100%'],
                        'columns' => [
                            'id',
                            [
                                'class' => 'yii\grid\DataColumn',
                                'attribute' => 'title',
                                'format' => 'html',
                                'contentOptions' => ['style'=>'min-width: 400px;'],
                                'value' => function($model) {
                                    /** @var $model Vocabulary */
                                    return \yii\helpers\Html::a($model->title, \yii\helpers\Url::to(['vocabulary/update', 'id' => $model->id]));
                                }
                            ],
                            // тип характеристики.
                            [
                                'class' => 'yii\grid\DataColumn',
                                'attribute' => 'type',
                                'format' => 'html',
                                'value' => function($model) {
                                    /** @var $model Vocabulary */
                                    return $model->getTypeName();
                                }
                            ],
                            [
                                'class' => \yii\grid\ActionColumn::class,
                                'buttons' => [
                                    'delete' => function($url) {
                                        return Html::a(Html::tag('span','', ['class'=>'glyphicon glyphicon-trash text-danger',]), $url, [
                                            'title' => Yii::t('yii', 'Удалить'),
                                            'data' => [
                                                'confirm' => Yii::t('yii', 'Вы действительно хотите удалить характеристику ? Удалятся так же все её значения.'),
                                            ],
                                        ]);
                                    }
                                ],
                                'template' => '{update} {delete}',
                            ]
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
print Html::a('Добавить характеристику', ['vocabulary/update'], ['class' => 'btn btn-primary']);