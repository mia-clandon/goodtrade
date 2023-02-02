<?php
/**
 * @var $model \common\models\Vocabulary
 * @var $form string
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?= $model->isNewRecord ? 'Новая характеристика' : "Обновление характеристики"; ?></h1>
    </div>
</div>

<div class="card shadow mb-4 p-4">
    <div class="card mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Описание типов характеристики
            </h6></div>
        <div class="panel-body">
            <ul>
                <li><strong>Значение</strong> - пользователь вводит сам нужное значение (числовое).</li>
                <li><strong>Диапазон</strong> - пользователь выбирает точку или 2 точки "от" и "до" между диапазоном
                    значений.
                </li>
                <li><strong>Выбор значения</strong> - пользователь выбирает одно из значений забитыми администратором.
                </li>
                <li><strong>Текст</strong> - пользователь сам вводит нужное значение.</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <?= $form; ?>
        </div>
    </div>
</div>