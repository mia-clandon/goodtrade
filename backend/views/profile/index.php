<?php
/**
 * @var \yii\data\ActiveDataProvider $data_provider
 * @var string $search_form;
 * @var string $indexer_form;
 */

use yii\grid\GridView;

?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Профили организаций</h1>
    </div>
</div>

<div class="card shadow mb-4">
    <?=$search_form;?>
</div>

<!-- Модальное окно для переиндексации организаций { -->
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
<div style="overflow-x: auto;" class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Профили организаций</h6>
    </div>
    <div class="card-body">
        <?php
            print GridView::widget([
            'dataProvider' => $data_provider,
            'tableOptions'=>['class'=>'table table-bordered dataTable'],
            'rowOptions'=> ['class' => 'text-center'],
            'columns' => [
                'id',
                [
                    'class' => 'yii\grid\DataColumn',
                    'label' => 'Есть привязка ?',
                    'attribute' => 'firm_id',
                    'headerOptions' => [
                        'width' => '100px',
                    ],
                    'value' => function($model) {
                        /** @var $model \common\models\firms\Profile */
                        return ($model->firm_id==0) ? 'Нет' : 'Есть';
                    }
                ],
                'bin',
                [
                    'class' => 'yii\grid\DataColumn',
                    'label' => 'ОКЕД',
                    'attribute' => 'oked',
                    'format' => 'text',
                ],
                [
                    'class' => 'yii\grid\DataColumn',
                    'label' => 'КРП',
                    'attribute' => 'krp',
                    'format' => 'text',
                ],
                'short_title', // краткое название организации.
                [
                    'class' => 'yii\grid\DataColumn',
                    'label' => 'Название организации',
                    'attribute' => 'title',
                    'format' => 'html',
                    'value' => function($model) {
                        /** @var $model \common\models\firms\Profile */
                        return \yii\helpers\Html::a($model->title, \yii\helpers\Url::to(['profile/update', 'id' => $model->id]));
                    }
                ],
                [
                    'class' => 'yii\grid\DataColumn',
                    'label' => 'Вид деятельности',
                    'attribute' => 'activity',
                    'format' => 'text',
                ],
                'locality',
                'company_size',
                'leader',
                'legal_address',
                'phone',
                'email',
                'site',
                [
                    'class' => 'yii\grid\DataColumn',
                    'label' => 'Со справочника ?',
                    'attribute' => 'is_parsed',
                    'headerOptions' => [
                        'width' => '100px',
                    ],
                    'value' => function($model) {
                        /** @var $model \common\models\firms\Profile */
                        return ($model->is_parsed==0) ? 'Нет' : 'Да';
                    }
                ],
                //['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
    </div>
</div>
