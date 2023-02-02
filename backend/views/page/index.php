<?php
/**
 * @var \yii\data\ActiveDataProvider $data_provider
 */

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Html;
use common\models\Page;

$links[] = Html::a('Экспорт', Url::to(['page/export']));

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Список страниц сайта</h1>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Экспорт страниц сайта</h6>
    </div>
    <div class="card-body">
        <?php
        // ссылки действий.
        echo implode(' | ', $links);
        ?>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Импорт страниц сайта</h6>
    </div>
    <div class="card-body">
        <?php
            #region - форма для импорта.
            echo Html::beginForm('/page/import', 'post', [
                    'enctype'=>'multipart/form-data',
                'style'=>'display: flex; flex-direction: row; align-items: center'
            ]);
            echo Html::fileInput('json_file');
            echo Html::submitButton('Импорт', array('class'=>'btn btn-primary'));
            echo Html::endForm();
            #endregion;
        ?>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Список страниц сайта</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?php
            /** Список товаров. */
                print GridView::widget([
                'dataProvider' => $data_provider,
                'tableOptions'=>['class'=>'table table-bordered dataTable', 'id' => 'dataTable', 'width'=>'100%'],
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                    ],
                    'id',
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'title',
                        'format' => 'html',
                        'value' => function($model) {
                            /** @var $model Page */
                            return Html::a($model->title, Url::to(['page/update', 'id' => $model->id]));
                        },
                    ],
                    'alias',
                    [
                        'class' => 'yii\grid\DataColumn',
                        'attribute' => 'created_at',
                        'format' => 'html',
                        'value' => function($model) {
                            /** @var $model Page */
                            return date('d.m.Y H:i:s', $model->created_at);
                        },
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

<div class="p-0">
    <?= print HTML::a('Добавить страницу сайта', ['page/update'], ['class' => 'btn btn-primary']);?>
</div>

