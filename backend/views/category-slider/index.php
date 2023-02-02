<?php

/**
 * @var \yii\data\ActiveDataProvider $data_provider
 */

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;

use common\models\CategorySlider;

?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Список записей</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <?
/** Список записей. */
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
            'contentOptions' => ['style' => 'max-width: 40%;'],
            'value' => function($model) {
                /** @var $model CategorySlider */
                return Html::a($model->title, Url::to(['category-slider/update', 'id' => $model->id]));
            },
        ],
        // Ссылка
        [
            'class' => 'yii\grid\DataColumn',
            'attribute' => 'link',
            'format' => 'html',
            'label' => 'Ссылка',
            'value' => function($model) {
                /** @var $model CategorySlider */
                return Html::a($model->link);
            }
        ],
        // Категория
        [
            'class' => 'yii\grid\DataColumn',
            'attribute' => 'category_id',
            'format' => 'html',
            'label' => 'Категория',
            'value' => function($model) {
                /** @var $model CategorySlider */
                /** @var \common\models\Category $category */
                $category = $model->getCategory()->one();
                return !is_null($category)?$category->title:'';
            }
        ],

        [
            'class' => \yii\grid\ActionColumn::class,
            'template' => '{update} {delete}',
        ]
    ],
]);
?>
        <div class="p-0">
            <?
                print Html::a('Добавить запись', ['category-slider/update'], ['class' => 'btn btn-primary']);
            ?>
            </div>
        </div>
    </div>
</div>

