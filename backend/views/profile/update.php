<?php
/**
 * @var $profile \common\models\firms\Profile
 * @var $profile_form string
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800"><?=$profile->isNewRecord ? 'Новая организация' : "Обновление организации - бин : {$profile->bin}";?></h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <?=$profile_form;?>
    </div>
</div>