<?
/**
 * @var \common\models\Oked[] $oked_list
 * @var array $related_oked_list_filtered
 * @author Артём Широких kowapssupport@gmail.com
 */
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Связи ОКЭД'ов</h1>
    </div>
</div>
<div class="card shadow mb-4 p-4">

    <div class="row oked">
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Потенциальный покупатель</div>
                <div class="panel-body customer">
                    <input  type="text" name="customer-code" placeholder="Добавить новый ОКЕД" class="form-control"/>
                    <div class="items-add"></div>
                    <input type="button" class="btn btn-primary btn-block mt-2" name="connect" value="Связать ОКЭД">
                    <div class="items-title card-header mt-2">СВЯЗАННЫЕ ОКЭДЫ <i class="fa fa-angle-up"></i></div>
                    <div class="items"></div>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <i class="arrow-next fa fa-arrow-right"></i>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">&nbsp</div>
                <div class="panel-body main">
                    <input type="text" name="main-code" class="form-control form-control-user" placeholder="Введите ОКЭД"/>
                    <div class="items"></div>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <i class="arrow-next fa fa-arrow-right"></i>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Потенциальный поставщик</div>
                <div class="panel-body provider">
                    <input type="text" name="provider-code" placeholder="Добавить новый ОКЕД" class="form-control form-control-user"/>
                    <div class="items-add"></div>
                    <input type="button" class="btn btn-primary btn-block mt-2" name="connect" value="Связать ОКЭД">
                    <div class="items-title card-header mt-2">СВЯЗАННЫЕ ОКЭДЫ <i class="fa fa-angle-up"></i></div>
                    <div class="items"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow mb-4 p-4">

    <div class="row">
        <div class="d-flex justify-content-center col-md-12 mb-2">
            <button type="button" disabled="disabled" class="btn btn-primary relation-oked-button">Связать ОКЭД</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="oked-list shadow p-4">
                <?
                echo Yii::$app->getView()->renderFile(Yii::getAlias('@backend/views/oked/parts/oked_table.php'), ['oked_list'=>$oked_list,]);
                ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="oked-list shadow p-4">
                <?
                echo Yii::$app->getView()->renderFile(Yii::getAlias('@backend/views/oked/parts/oked_table.php'), ['oked_list'=>$oked_list,]);
                ?>
            </div>
        </div>
    </div>
    <div class="row">&nbsp;</div>
    <div class="panel panel-default shadow">
        <div class="panel-body">
            <div class="row">
                <? foreach ($related_oked_list_filtered as $oked_from=>$oked_list_to) { ?>
                    <div class="col-md-2">
                        <? foreach ($oked_list_to as $oked_to) { ?>
                            <div>
                                <span><?= $oked_from; ?> => <?= $oked_to; ?></span>
                                [<a class="remove-oked-relation text-danger" data-from="<?= $oked_from; ?>"
                                    data-to="<?= $oked_to; ?>" href="#">удалить</a>]
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
</div>