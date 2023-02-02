<?php

/**
 * @var \yii\data\ActiveDataProvider $data_provider
 * @var string $search_form
 */

use yii\bootstrap\Html;
use yii\grid\GridView;

?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Каталог ОКЭД</h1>
    </div>
</div>
<!--    <div class="card shadow mb-4 p-4">-->
<?= $search_form; ?>
<div class="card shadow mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Каталог ОКЭД</h6>
    </div>
    <div class="card-body">
        <?php
            print GridView::widget([
                'dataProvider' => $data_provider,
                'tableOptions'=>['class'=>'table table-bordered dataTable'],
                'columns' => [
                    'key',
                    'name',
                    /*
                    [
                        'class' => \yii\grid\ActionColumn::class,
                        'template' => '{update} {delete}',
                    ]
                    */
                ],
            ]);
        ?>
    </div>
</div>
<?php
print Html::a('Добавить ОКЕД', ['oked/update'], ['class' => 'btn btn-primary ml-4']);
