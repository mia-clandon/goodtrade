<?
/**
 * @var $data_provider
 * @var $model \common\models\User
 */

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

?>

<div class="row">
    <div class="col-lg-12">
        <h2 class="h2 mb-2 text-gray-800">Список организаций</h2>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Список организаций</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <div id="dataTable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                <?php
                    print GridView::widget([
                        'dataProvider'=>$data_provider,
                        'tableOptions'=>['class'=>'table table-bordered dataTable'],
                        'columns'=>[
                                'id',
                            [
                                 'class'=>'yii\grid\DataColumn',
                                 'attribute'=>'title',
                                 'format'=>'html',
                                 'value'=>function ($model) {
                                     /** @var $model \common\models\firms\Firm */
                                    return Html::a($model->title, Url::to(['firm/update', 'id'=>$model->id]));
                                }
                            ], // владелец
                            [
                                'class'=>'yii\grid\DataColumn',
                                'attribute'=>'user_id',
                                'format'=>'html',
                                'value'=>function ($model) {
                                    /** @var $model \common\models\firms\Firm */
                                    /** @var \common\models\User $user */
                                    $user=$model->getOwner()->one();
                                    if ($user) {
                                        return Html::a($user->username, Url::to(['user/update', 'id'=>$user->id]));
                                    } else {
                                        return '';
                                    }
                                }
                            ], // статус
                            [
                                'class'=>'yii\grid\DataColumn',
                                'attribute'=>'status',
                                'format'=>'html',
                                'value'=>function ($model) {
                                    /** @var $model \common\models\firms\Firm */
                                    $label=$model->status == \common\models\firms\Firm::STATUS_ENABLED ? 'label-success' : 'label-danger';
                                    return '<span class="label ' . $label . '">' . $model->getCurrentStatusText() . '</span>';
                                }
                            ], // сферы деятельности
                            [
                                'class'=>'yii\grid\DataColumn',
                                'format'=>'html',
                                'label'=>'Категории',
                                'value'=>function ($model) {
                                    /** @var $model \common\models\firms\Firm */
                                    $categories=$model->getCategories()->select(['id', 'title'])->asArray()->all();
                                    $category_list=[];
                                    foreach ($categories as $item) {
                                        $category_list[]=Html::a($item['title'], Url::to(['category/update', 'id'=>$item['id']]), ['class'=>'label label-primary']);
                                    }
                                    return implode(' ', $category_list);
                                }
                            ],
                            [
                                'class'=>'yii\grid\DataColumn',
                                'format'=>'html',
                                'value'=>function ($model) {
                                    /** @var $model \common\models\firms\Firm */
                                    return Html::a('Зайти под пользователем', \yii\helpers\Url::to(['user/login-by-user', 'id'=>$model->user_id]));
                                }
                            ],
                            [
                                'class'=>'yii\grid\DataColumn',
                                'format'=>'html',
                                'label'=>'Адрес',
                                'value'=>function ($model) {
                                    /** @var $model \common\models\firms\Firm */
                                    return $model->getLocation();
                                }
                            ],
                            [
                                'class'=>\yii\grid\ActionColumn::class,
                                'template'=>'{update} {delete}',
                                ]
                        ],
                        ]
                    );
                ?>
            </div>
        </div>
    </div>
    <div class="p-4">
        <?php
            print Html::a('Добавить организацию', ['firm/update'], ['class'=>'btn btn-primary']);
        ?>
    </div>
</div>