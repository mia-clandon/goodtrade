<?php
/**
 * @var string $form
 * @var string $category_control
 */
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="h2 mb-2 text-gray-800">Выгрузка прайс листа.</h1>
    </div>
</div>

<?=$form;?>

<div id="excel-table"></div>

<!-- ko with: PriceList -->
<!-- ko if: ko_data_loaded -->
<button class="btn btn-success" data-bind="click: saveData">Импортировать прайс лист</button>
<!-- /ko -->
<!-- /ko -->

<!-- Модальное окно "привязка товара к категории." { -->
<div class="modal fade" id="product-category-relation" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Привязка товаров к категории.</h4>
            </div>
            <div class="modal-body">
                <div class="category-control-block">
                    <?=$category_control;?>
                </div>
                <a target="_blank" href="<?=Yii::$app->urlManager->createUrl(['category/update', 'id' => 0]);?>">[Я не нашел категорию в списке]</a>
                <a href="#" class="update-category-options">[Обновить список существующих категорий]</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary tie-category">Привязать категорию.</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно "карточка товара к категории." } -->

<!-- Модальное окно "карточка товара" { -->
<div class="modal fade" id="import-vocabulary-create" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Карточка товара</h4>
            </div>
            <div class="modal-body">
                <div class="image-block">
                    <!-- контейнер загруженных фото { -->
                    <div id="file-uploader">
                        <noscript>
                            <p>Пожалуйста, включите JavaScript для использования загрузчика фото.</p>
                            <!-- or put a simple form for upload here -->
                        </noscript>
                    </div>
                    <!-- контейнер загруженных фото } -->
                    <table class="table">
                        <!-- ko if: ko_images().length -->
                        <caption>Фото товара</caption>
                        <!-- /ko -->
                        <tbody>
                        <!-- ko foreach: {data: getImages(), as: 'image'} -->
                            <tr>
                                <td>
                                    <input placeholder="Вставьте URL фото" class="form-control" data-bind="textInput: image"
                                           type="text" name="image_url[]">
                                </td>
                            </tr>
                        <!-- /ko -->
                        </tbody>
                    </table>
                </div>
                <div class="image-actions">
                    <a class="btn btn-success" data-bind="click: addImage" href="#">Добавить URL фото</a>
                    <a class="btn btn-default" id="pick-files">Выбрать файлы с PC</a>
                </div>
                <hr />
                <table class="table">
                    <!-- ko if: ko_vocabularies().length -->
                    <caption>Список характеристик</caption>
                    <!-- /ko -->
                    <tbody>
                        <!-- ko foreach: {data: getVocabularies(), as: 'vocabulary'} -->
                        <tr>
                            <td>
                                <label style="display: block;">
                                    <input  placeholder="Название характеристики" class="form-control"
                                            data-bind="textInput: title" type="text" name="vocabulary[]">
                                </label>
                            </td>
                            <td>
                                <!-- ko foreach: {data: vocabulary.getTerms(), as: 'term'} -->
                                <div class="term">
                                    <label>
                                        <input placeholder="Значение характеристики" class="form-control"
                                               data-bind="textInput: title" type="text" name="term[]">
                                    </label>
                                    <!-- ko with: PriceList -->
                                    <a data-bind="click: function(data,e) { addTerm(vocabulary,data,e) }" href="#" class="add-term" title="Добавить">
                                        <span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                    <!-- /ko -->
                                </div>
                                <!-- /ko -->
                            </td>
                        </tr>
                        <!-- /ko -->
                    </tbody>
                </table>
                <button class="btn btn-success" data-bind="click: addVocabulary">Добавить характеристику</button>
            </div>
        </div>
    </div>
</div>
<!-- Модальное окно "карточка товара" } -->