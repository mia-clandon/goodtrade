<?
/**
 * @var $data_provider
 * @var $model \common\models\User
 */
use yii\grid\GridView;
use yii\bootstrap\Html;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Список пользователей</h1>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Список пользователей</h6>
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
                                'attribute' => 'username',
                                'format' => 'html',
                                'value' => function($model) {
                                    /** @var $model \common\models\User */
                                    return Html::a($model->username, \yii\helpers\Url::to(['user/update', 'id' => $model->id]));
                                }
                            ],
                            'email',
                            'phone_real',
                            [
                                'class' => 'yii\grid\DataColumn',
                                'format' => 'html',
                                'value' => function($model) {
                                    return Html::a('Зайти под пользователем', \yii\helpers\Url::to(['user/login-by-user', 'id' => $model->id]));
                                }
                            ],
                            [
                                'class' => 'yii\grid\DataColumn',
                                'format' => 'html',
                                'value' => function($model) {
                                    return Html::a('Права доступа', \yii\helpers\Url::to(['user/permissions', 'id' => $model->id]));
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
    <div class="p-0">
<?php
print Html::a('Добавить пользователя', ['user/update'], ['class' => 'btn btn-primary']);
?>
    </div>
