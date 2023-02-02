<?
/**
 * @var $model \common\models\User
 * @var string $form
 */
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?= $model->isNewRecord ? 'Новый пользователь' : 'Редактирование пользователя'; ?></h1>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?= $model->isNewRecord ? 'Регистрация нового пользователя' : 'Редактирование пользователя'; ?></h6>
    </div>
    <div class="card-body">
        <?= $form; ?>
    </div>
</div>