<?
/**
 * @var \yii\data\ActiveDataProvider $data_provider
 * @var \common\libs\i18n\models\Hint $model
 * @author Артём Широких kowapssupport@gmail.com
 */
use yii\grid\GridView;
use yii\bootstrap\Html;
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Тексты для переводов</h1>
    </div>
</div>

<div class="modal fade" id="modal-add-hint-translate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Добавление перевода к хинту.</h4>
            </div>
            <div class="modal-body">
                <div class="content" id="modal-add-hint-translate-content">
                    <?// сюда подгрузится форма для добавления перевода для хинта.?>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php

print GridView::widget([
    'dataProvider' => $data_provider,
    'columns' => [
        'id',
        [
            'class' => \yii\grid\DataColumn::class,
            'attribute' => 'message',
            'format' => 'html',
            'value' => function($model) {
                /** @var \common\libs\i18n\models\Hint $model */
                return $model->message;
            }
        ],
        [
            'class' => \yii\grid\DataColumn::class,
            'format' => 'raw',
            'label' => 'Переводы',
            'value' => function($model) {
                /** @var \common\libs\i18n\models\Hint $model */
                $languages = $model->getMessages()->select('language')->asArray()->all();
                $languages = \yii\helpers\ArrayHelper::getColumn($languages, 'language');
                $language_string = [];
                foreach ($languages as $language_code) {
                    $language_string[] = \common\libs\i18n\helper\SystemLanguages::i()
                        ->getLanguageName($language_code);
                }
                $add_translate = '<br />'. Html::a('Добавить перевод', ['/translate/add-translate'], [
                    'class' => 'add-translate',
                    'data' => ['hint-id' => $model->id,],
                ]);
                if (empty($language_string)) {
                    return 'Переводы отсутствуют.'. $add_translate;
                }
                return implode(', ', $language_string). $add_translate;
            }
        ],
        [
            'class' => \yii\grid\DataColumn::class,
            'attribute' => 'category',
            'format' => 'html',
            'contentOptions' => [ 'style' => 'width: 25%;' ],
            'value' => function($model) {
                /** @var \common\libs\i18n\models\Hint $model */
                return \common\libs\i18n\helper\SystemLanguages::i()
                    ->getCategoryName($model->category);
            }
        ],
        [
            'class' => \yii\grid\ActionColumn::class,
            'template' => '{update} {delete}',
        ]
    ],
]);

print Html::a('Добавить хинт', ['translate/update'], ['class' => 'btn btn-primary']);