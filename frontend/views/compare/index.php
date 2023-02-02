<?
/**
 * Вывод таблицы для сравнения товаров.
 * @var \common\models\goods\Product[] $product_list
 * @var \common\models\Category $category
 * @var int $product_count
 */

use common\models\goods\Product;
use common\libs\StringHelper;
use common\models\commercial\Request;
use common\models\firms\Firm;
use frontend\components\widgets\CommercialRequest;
use frontend\components\lib\CompareProcessor;

$has_commercial_response = count(array_filter($product_list[CompareProcessor::PROP_PRODUCT_RESPONSES]));
?>
<div class="container-fluid">
    <div class="comparison-container">

        <table class="comparison-table">
            <thead>
                <tr data-key="item-title">
                    <th></th>
                    <? // вывод заголовков товаров. ?>
                    <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_TITLE_COMPARE] as $key => $product_title) { ?>
                    <td>
                        <div class="comparison-table__item-title">
                            <a href="<?= $product_list[CompareProcessor::PROP_PRODUCT_LINK_COMPARE][$key];?>" title="<?= $product_title; ?>"><?= $product_title; ?></a>
                        </div>
                        <a href="#" class="comparison-table__item-delete-link  action js-keeper" data-id="<?= $product_list[CompareProcessor::PROP_PRODUCT_ID_COMPARE][$key]; ?>" data-key="compare<?=\Yii::$app->user->id?>">Удалить из сравнения</a>
                    </td>
                    <? } ?>
                </tr>
                <tr data-key="item-photos">
                    <th></th>
                    <? // вывод фотографий товаров. ?>
                    <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_MAIN_IMAGE_COMPARE] as $key => $product_image) { ?>
                    <td class="comparison-table__item-photos">
                        <div class="comparison-table__item-photos-image">
                            <img src="<?= $product_image;?>">
                        </div>
                        <div class="comparison-table__item-photos-text">
                            <?= $product_list[CompareProcessor::PROP_PRODUCT_IMAGE_COUNT_COMPARE][$key]; ?>
                        </div>
                    </td>
                    <? } ?>
                </tr>
            </thead>
            <tfoot>
                <tr data-key="item-button">
                    <th>
                        <div class="popup-dropdown">
                            <? if(!Request::hasAllCommercialRequest($product_list[CompareProcessor::PROP_PRODUCT_ID_COMPARE])) { ?>
                            <div data-vertical-align="bottom"
                                 class="btn btn-blue btn-block btn-comparison popup-toggle all-commercial-popup commercial-popup"
                                 data-id="<?= implode(',', $product_list[CompareProcessor::PROP_PRODUCT_ID_COMPARE])?>">
                                Общий запрос
                            </div>
                            <? } ?>
                        </div>
                        <!-- Временно спрятал -->
                        <div class="btn btn-blue btn-block btn-comparison btn-disabled is-hidden">Ожидается ответ</div>
                    </th>
                    <?
                    /** @var Product $product */
                    foreach ($product_list[CompareProcessor::PROP_PRODUCT_OBJECT] as $key => $product) { ?>
                    <td>
                        <input type="hidden" value="<?=$product->id?>" class="product_id">
                        <?
                        // коммерческий запрос уже посылали на товар.
                        if (!\Yii::$app->user->isGuest && Request::hasRequest($product->id, Firm::get()->id)) {
                            $request = Request::getRequestByProduct($product->id, Firm::get()->id);
                        ?>

                            <?
                            // запрос новый (не просроченый)
                            if ($request->status == Request::STATUS_NEW) {
                                $days = $product_list[CompareProcessor::PROP_PRODUCT_REQUEST_VALIDITY][$key];
                            ?>
                                <div class="btn btn-block btn-disabled btn-comparison">Ожидается ответ (<?= $days; ?>)</div>
                            <? } ?>

                        <? } else {
                            // послать коммерческий запрос.
                        ?>
                            <div class="popup-dropdown">
                                <div data-vertical-align="bottom"
                                     class="btn btn-blue btn-block btn-outline btn-comparison popup-toggle commercial-popup"
                                     data-id="<?= $product->id;?>">
                                    Коммерческий запрос
                                </div>
                            </div>
                        <? } ?>
                    </td>
                    <? } ?>
                    <? /*
                    <td>
                        <div class="popup-dropdown">
                            <div data-vertical-align="bottom"
                                 class="btn btn-blue btn-block btn-outline btn-comparison btn-comparison_dropdown popup-toggle send-contacts-popup">
                                Выслать контакты
                            </div>
                        </div>
                    </td>
                    */?>
                </tr>
            </tfoot>
            <tbody>
                <tr data-key="item-price">
                    <th>Цена</th>
                    <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_PRICE_COMPARE] as $key => $product_price) {
                        $price_formatted = $product_list[CompareProcessor::PROP_PRODUCT_PRICE_FORMATTED_COMPARE][$key];
                    ?>
                    <td>
                        <? if ($product_price) { ?>
                            <div class="total-price"><?= $product_list[CompareProcessor::PROP_PRODUCT_WITH_VAT][$key] ?></div>
                            <div class="price"><?= $price_formatted; ?> тг.</div>
                            <? if(!empty($product_list[CompareProcessor::PROP_PRODUCT_RESPONSES][$key])) { ?>
                                <div class="total-price">Всего: <?= $product_list[CompareProcessor::PROP_PRODUCT_REQUEST_PART_SIZE][$key] * $product_list[CompareProcessor::PROP_PRODUCT_PRICE_COMPARE][$key] ?>тг.</div>
                            <? } else { ?>
                                <div class="label">Базовая цена</div>
                            <? } ?>
                        <? } else { ?>
                            <div class="price no-price">Цена по уточнению</div>
                        <? } ?>
                    </td>
                    <? } ?>
                </tr>

                <? if($has_commercial_response) { ?>
                    <tr data-key="item-commerce-validity">
                        <th>Срок действия КП</th>
                        <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_RESPONSE_VALIDITY] as $key => $days) { ?>
                        <td><?= (empty($days)) ? '&mdash;' : $days;?></td>
                        <? } ?>
                    </tr>
                    <tr data-key="item-commerce-batch-size">
                        <th>Размер партии</th>
                        <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_REQUEST_PART_SIZE] as $key => $part_size) { ?>
                        <td><?= (empty($part_size)) ? '&mdash;' : "$part_size шт.";?></td>
                        <? } ?>
                    </tr>

                    <tr data-key="item-commerce-stock-availability">
                        <th>Наличие товара</th>
                        <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_IN_STOCK] as $key => $pay_persent) { ?>
                            <td><?= $pay_persent ?></td>
                        <? } ?>
                    </tr>

                    <tr data-key="item-commerce-terms-of-payment">
                        <th>Условия оплаты</th>
                        <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_PAY_PERSENT] as $key => $pay_persent) { ?>
                            <td><?= $pay_persent ?></td>
                        <? } ?>
                    </tr>
                <? } ?>

                <tr data-key="item-delivery-conditions">
                    <th>Условия поставки<br><span>Incoterms</span></th>
                    <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_DELIVERY_TERMS] as $key => $delivery_terms) { ?>
                    <td><?= $delivery_terms; ?></td>
                    <? } ?>
                </tr>
                <tr data-key="item-delivery-address">
                    <th>Регион реализации<?/*<br><span>Ваше расположение: Костанай</span>*/?></th>
                    <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_PLACE] as $key => $place_address) { ?>
                    <td><?= (empty($place_address)) ? '&mdash;' : $place_address; ?>.<?/*<br><span>Около 1950 км</span>*/?></td>
                    <? } ?>
                </tr>
                <tr data-key="item-power">
                    <th>Мощность</th>
                    <? foreach ($product_list[CompareProcessor::PROP_PRODUCT_CAPACITY] as $key => $product_capacity) { ?>
                    <td><?= $product_capacity; ?></td>
                    <? } ?>
                </tr>
                <tr>
                    <th>
                        <div class="comparison-table__tech-specs-title">Технические характеристики</div>
                    </th>
                    <td></td>
                </tr>
                <?
                // вывод технических характеристик товаров.
                foreach ($product_list[CompareProcessor::PROP_PRODUCT_SPECIFICATIONS] as $specification_name => $specifications) { ?>
                <tr data-key="item-tech" class="comparison-container__tech-specs">
                <?
                    echo '<th>'.StringHelper::firstUpperLetter($specification_name).'</th>';
                    foreach ($specifications as $product_id => $value) {
                        echo (null === $value)
                            ? '<td>&mdash;</td>'
                            : '<td>'.$value.'</td>';
                    }
                 ?>
                </tr>
                <? } ?>

                <tr data-key="item-manufacturer-info">
                    <th></th>
                    <?
                    /** @var Firm $firm */
                    foreach ($product_list[CompareProcessor::PROP_PRODUCT_FIRMS] as $firm) {
                        if(null === $firm) {
                            echo  '<td></td>';
                        }
                    ?>
                    <td>
                        <div class="comparison-table__item-manufacturer">
                            <a href="<?= \Yii::$app->urlManager->createUrl(['firm/show', 'id' => $firm->id]); ?>"><?= $firm->getTitle() ?></a>
                        </div>
                        <!--<div class="comparison-table__item-manufacturer-contacts">
                            <p><?= $firm->getLocation(true) ?></p>
                            <p><?= $firm->email ? 'E-mail: '.$firm->email :'' ?>
                                <br><?= $firm->phone ? 'Тел. '.$firm->phone :'' ?></p>
                            <p><?= $firm->bin ? 'БИН: '.$firm->bin :'' ?>
                                <br><?= $firm->iik ? 'ИИК: '.$firm->iik :'' ?></p>
                        </div>
                        <a href="#" class="comparison-table__item-manufacturer-contacts-link">Показать контакты</a>-->
                    </td>
                    <? } ?>
                </tr>
            </tbody>
        </table>

        <? if ($product_count) { ?>
        <div data-action="show" class="comparison-add-btn js-add-compare">
            <div class="comparison-add-btn__text">Добавить<br>к сравнению</div>
            <div class="comparison-add-btn__image"></div>
        </div>
        <? } ?>

        <div class="comparison-search is-hidden">

            <div class="comparison-search-description">Результаты из категории &laquo;<?= $category->title ?>&raquo;</div>
            <!--<div class="comparison-search-description">Отфильтровать</div>-->

            <input type="hidden" name="category"  value="<?= $category->id ?>">
            <div class="preview-items-block preview-items-block_comparison-search">
                <div class="preview-items preview-items_comparison-search js-suggest-content">
                    <? //Здесь будет разультат поиска ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= CommercialRequest::widget(['type' => CommercialRequest::REQUEST_TYPE_MODAL]); ?>