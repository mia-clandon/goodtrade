<?
/**
 * @var $model \common\models\firms\Firm
 * @var string $form
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?= $model->isNewRecord ? 'Новая организация' : 'Редактирование организации'; ?></h1>
    </div>
</div>

<div class="card shadow mb-4">
    <?= $form; ?>
</div>
