<?

use common\models\commercial\Request;
use common\libs\Declension;

/**
 * @var \common\models\commercial\Response $response
 * @var string $commercial_data
 * @var \common\models\goods\Product $product
 * @var \common\models\commercial\Request $request
 * @var \common\models\firms\Firm $response_owner_firm
 */

$request_dates = $request->getRequestDates();

$product_categories = [];
/** @var \common\models\Category $category */
foreach($product->getCategories()->all() as $category) {
    $product_categories[] = $category->title;
}

?>
<main role="main" id="page-wrap">
    <div class="container">
        <div class="row">
            <div class="col-xs-4 col-xs-offset-1">
                <div class="row">
                    <div id="print">
                        <div class="page-wrap">
                            <div class="page-mini-title">
                                <span class="muted">Коммерческое предложение</span>
                            </div>
                            <div class="page">
                                <div class="page-head">
                                    <div class="page-company"><?=$response_owner_firm->getTitle();?></div>
                                    <div class="clearfix">
                                        <div class="page-col-left">
                                            <div class="page-contacts">
                                                <div class="page-contacts__address popup-dropdown">
                                                    <span data-bind="text: 'г. ' + cities[city_id()]"></span><span
                                                            data-bind="text: formattedAddress"></span>
                                                </div>
                                                <div></div>
                                                <div class="page-contacts__email popup-dropdown">
                                                    <span class="page-contacts__email-label">E-mail:</span>
                                                    <!-- ko foreach: emails-->
                                                    <div class="page-contacts__email-item"
                                                         data-bind="text: $data"></div>
                                                    <!-- /ko -->
                                                </div>
                                                <div></div>
                                                <div class="page-contacts__tel popup-dropdown">
                                                    <span class="page-contacts__tel-label">Тел.:</span>
                                                    <!-- ko foreach: tels-->
                                                    <div class="page-contacts__tel-item" data-bind="text: $data"></div>
                                                    <!-- /ko -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="page-col-right">
                                            <div class="page-number">
                                                <div class="page-number-number">№ <?=$request->id?></div>
                                                <div class="page-number-date">от <?=Yii::$app->formatter->asDate($response->response_time, 'php:j F Y');?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="page-body">
                                    <div class="page-title">Коммерческое предложение</div>
                                    <div class="page-text">
                                        <p>Уважаемые господа!<br>
                                            Настоящим разрешите выразить Вам искреннее уважение и предоставить Вашему
                                            вниманию коммерческое предложение в ответ на запрос № <?=$request->id;?> от
                                            <?=$request_dates[Request::PROP_REQUEST_DATE];?>.
                                        </p>
                                    </div>
                                    <table class="page-product">
                                        <tbody>
                                            <tr>
                                                <td class="page-product-title-container">
                                                    <span class="page-product-title"><?= $product->getTitle();?></span><br>
                                                    <span class="page-product-categories"><?=implode(', ', $product_categories);?></span>
                                                </td>
                                                <td class="page-product-count">
                                                    <span data-bind="text: productCount"></span>
                                                    <span data-bind="text: productUnitShort"></span>
                                                </td>
                                                <td class="page-product-price">
                                                    <span data-bind="text: formatPrice(productPrice())"></span>тг.
                                                    <div class="page-product-price-vat" data-bind="visible: withVat">С учетом НДС</div>
                                                    <div class="page-product-price-vat" data-bind="visible: !withVat">Без учета НДС</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td>Итого</td>
                                                <td class="page-product-price"><span
                                                            data-bind="text: formatPrice(totalPrice())"></span>тг.
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="clearfix">
                                        <div class="page-col-left">
                                            <div class="page-condition popup-dropdown">
                                                <div class="page-condition__title">Условия поставки:</div>
                                                <div class="page-condition__content">
                                                    <div data-bind="text: formattedDeliveryConditions"></div>
                                                    <div data-bind="visible: inStock">Товар в наличии</div>
                                                    <div data-bind="visible: !inStock()">Срок до отправки <span
                                                                data-bind="text: deliveryTime"></span> дней
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="page-col-right">
                                            <div class="page-condition popup-dropdown">
                                                <div class="page-condition__title">Условия оплаты:</div>
                                                <div class="page-condition__content">
                                                    Предоплата - <span data-bind="text: prepay() + '%'"></span><br>
                                                    При получении - <span data-bind="text: postpay() + '%'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="page-expires popup-dropdown">
                                        <span>Коммерческое предложение действительно в течение </span> <strong><?= Declension::number($response->response_validity, 'день', 'дня', 'дней', true);?></strong>
                                    </div>
                                </div>
                                <div class="page-foot">
                                    <div class="clearfix">
                                        <div class="page-col-left page-col-left_page-foot">
                                            <div class="page-requisites popup-dropdown">
                                                <div class="page-requisites__title">
                                                    <span><?=$response_owner_firm->getTitle();?></span>
                                                </div>
                                                <div class="page-requisites__content">
                                                    <span class="page-requisites__content-element page-requisites__content-element_address">Республика Казахстан<span
                                                                data-bind="text: ', г. ' + cities[city_id()]"></span><span
                                                                data-bind="text: formattedAddress"></span></span>
                                                    <!-- ko if: bin() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_bin"
                                                          data-bind="text: 'БИН: ' + bin()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: bank() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_bank"
                                                          data-bind="text: 'Банк: ' + bank()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: bik() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_bik"
                                                          data-bind="text: 'БИК: ' + bik()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: iik() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_iik"
                                                          data-bind="text: 'ИИК: ' + iik()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: kbe() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_kbe"
                                                          data-bind="text: 'КБе: ' + kbe()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: knp() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_knp"
                                                          data-bind="text: 'КНП: ' + knp()"></span>
                                                    <!-- /ko -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="page-col-right page-col-right_page-foot">
                                            <div class="page-watermark">
                                                <div class="page-watermark-logo"><img src="/img/requisites-logo.png">
                                                </div>
                                                <div class="page-watermark-text">Создано на платформе<br> b2bMarket
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="page-wrap page-wrap-enclosure">
                            <div class="page-mini-title">
                                <span class="muted">Приложение №1</span>
                                <span class="muted page-mini-title__notice">Генерируется автоматически</span>
                            </div>
                            <div class="page">
                                <div class="page-head">
                                    <div class="page-company"><?= $response_owner_firm->getTitle();?></div>
                                    <div class="clearfix">
                                        <div class="page-col-left">
                                            <div class="page-contacts">
                                                <div class="page-contacts__address">
                                                    <span data-bind="text: 'г. ' + cities[city_id()]"></span><span
                                                            data-bind="text: formattedAddress"></span>
                                                </div>
                                                <div class="page-contacts__email popup-dropdown">
                                                    <span class="page-contacts__email-label">E-mail:</span>
                                                    <!-- ko foreach: emails-->
                                                    <div class="page-contacts__email-item"
                                                         data-bind="text: $data"></div>
                                                    <!-- /ko -->
                                                </div>
                                                <div></div>
                                                <div class="page-contacts__tel popup-dropdown">
                                                    <span class="page-contacts__tel-label">Тел.:</span>
                                                    <!-- ko foreach: tels-->
                                                    <div class="page-contacts__tel-item" data-bind="text: $data"></div>
                                                    <!-- /ko -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="page-col-right">
                                            <div class="page-number">
                                                <div class="page-number-number">№ <?=$request->id;?></div>
                                                <div class="page-number-date">от <?=Yii::$app->formatter->asDate($response->response_time, 'php:j F Y');?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="page-body">
                                    <div class="page-title">Приложение №1</div>
                                    <div class="page-text">
                                        <p><?= $product->getTitle();?></p>
                                        <?= $product->getText();?>
                                    </div>
                                    <div class="page-product-description clearfix">
                                        <?= Yii::$app->getView()->renderFile(Yii::getAlias('@frontend/views/commercial/response/parts/vocabulary_terms.php'),[
                                            'product' => $product,
                                        ]);?>
                                    </div>
                                </div>
                                <div class="page-foot">
                                    <div class="clearfix">
                                        <div class="page-col-left page-col-left_page-foot">
                                            <div class="page-requisites">
                                                <div class="page-requisites__title">
                                                    <span><?=$response_owner_firm->getTitle();?></span>
                                                </div>
                                                <div class="page-requisites__content">
                                                    <span class="page-requisites__content-element page-requisites__content-element_address">Республика Казахстан<span
                                                                data-bind="text: ', г. ' + cities[city_id()]"></span><span
                                                                data-bind="text: formattedAddress"></span></span>
                                                    <!-- ko if: bin() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_bin"
                                                          data-bind="text: 'БИН: ' + bin()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: bank() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_bank"
                                                          data-bind="text: 'Банк: ' + bank()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: bik() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_bik"
                                                          data-bind="text: 'БИК: ' + bik()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: iik() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_iik"
                                                          data-bind="text: 'ИИК: ' + iik()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: kbe() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_kbe"
                                                          data-bind="text: 'КБе: ' + kbe()"></span>
                                                    <!-- /ko -->
                                                    <!-- ko if: knp() !== "" -->
                                                    <span class="page-requisites__content-element page-requisites__content-element_knp"
                                                          data-bind="text: 'КНП: ' + knp()"></span>
                                                    <!-- /ko -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="page-col-right page-col-right_page-foot">
                                            <div class="page-watermark">
                                                <div class="page-watermark-logo"><img src="/img/requisites-logo.png">
                                                </div>
                                                <div class="page-watermark-text">Создано на платформе<br> b2bMarket
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script type="text/javascript">
    // Деление на страницы, если текста слишком много.
    var $root = $(".page-wrap-enclosure"),
        $enclosureBody = $root.find(".page-body"),
        availableHeight = 282 + (130 - $(".page-company").innerHeight()) + (48 - $(".page-requisites__title").innerHeight());

    console.log("availableHeight\n", availableHeight);

    // Если высота всего содержимого тела листа больше доступной
    if ($enclosureBody.innerHeight() > availableHeight) {
        var $pageTitle = $enclosureBody.children(".page-title"),
            $pageText = $enclosureBody.children(".page-text"),
            $productDesc = $enclosureBody.children(".page-product-description"),
            innerAvailableHeight = availableHeight - $pageTitle.outerHeight(true) - parseFloat($pageText.children().eq(0).css("line-height"));

        // Если высота описания товара больше внутренней доступной высоты
        if ($pageText.innerHeight() > innerAvailableHeight) {
            var pageTextChildren = [],
                pageTextPages = [[]],
                pageTextHeight = 0,
                page = 0; // Счётчик

            $pageText.children().each(function () {
                pageTextChildren.push(this);
            });

            for (var i = 0, c = 0; i < pageTextChildren.length; i++) {
                c++; // Защита от бесконечного цикла

                if (i === 0) {
                    pageTextPages[page].push(pageTextChildren[i]);
                    pageTextHeight += $(pageTextChildren[i]).outerHeight(true);
                    continue;
                }

                // Если высота абзаца больше внутренней доступной высоты
                if ($(pageTextChildren[i]).innerHeight() > innerAvailableHeight) {
                    var pageTextInnerHTMLs = splitParagraph(pageTextChildren[i], innerAvailableHeight - pageTextHeight),
                        clone = $(pageTextChildren[i]).clone()[0],
                        isSplitted = true;

                    pageTextChildren[i].innerHTML = pageTextInnerHTMLs[0];
                    clone.innerHTML = pageTextInnerHTMLs[1];

                    $(pageTextChildren[i]).after(clone);
                    pageTextPages[page].push(pageTextChildren[i]);
                    pageTextHeight += innerAvailableHeight;
                } else {
                    pageTextPages[page].push(pageTextChildren[i]);
                    pageTextHeight += $(pageTextChildren[i]).outerHeight(true);
                }

                if (pageTextHeight > innerAvailableHeight) {
                    page++;
                    pageTextHeight = 0;

                    pageTextPages.push([]);
                    pageTextPages[page].push($(pageTextPages[0][0]).clone()[0]);
                    pageTextHeight += $(pageTextPages[0][0]).outerHeight(true);

                    if (isSplitted) {
                        pageTextChildren.push(clone);
                        pageTextPages[page].push(clone);
                        isSplitted = false;
                    }
                }

                if (c === 1000) break; // Защита от бесконечного цикла
            }

            // Распределение абзацев по страницам
            for (var j = 0; j < pageTextPages.length; j++) {
                if (j > 0) {
                    var $clone = $root.clone();

                    $pageText = $clone.find(".page-text");

                    $root.parent().append($clone);
                }

                $pageText.html("");

                for (var l = 0; l < pageTextPages[j].length; l++) {
                    $pageText.append(pageTextPages[j][l]);
                }
            }

            $root.parent().find(".page-product-description").remove();
            $clone.find(".page-body").append($productDesc);
        }

        var descAvailableHeight = innerAvailableHeight - $pageText.outerHeight(true);

        // Если высота технических характеристик больше внутренней доступной высоты
        if ($productDesc.innerHeight() > descAvailableHeight) {
            var productDescChildren = [],
                productDescPages = [[]],
                productDescHeight = 0,
                descPage = 0, // Счётчик
                $prototypePage = $root.parent().children().last().clone();

            $prototypePage.find(".page-text").children().each(function (index, element) {
                if (index === 0) { return true; }
                $(this).remove();
            });
            $prototypePage.find(".page-product-description").html("");

            $productDesc.children().each(function () {
                productDescChildren.push(this);
            });

            // Если первая строка технических характеристик больше внутренней доступной высоты
            if ( $(productDescChildren[0]).outerHeight(true) > descAvailableHeight ) {
                var $pageClone = $prototypePage.clone();

                $root.parent().append($pageClone);
                $pageClone.find(".page-text").after($productDesc);
                descAvailableHeight = innerAvailableHeight - $pageText.children().eq(0).outerHeight(true);
            }

            for (var i2 = 0, c2 = 0; i2 < productDescChildren.length; i2++) {
                c2++; // Защита от бесконечного цикла

                productDescHeight += $(productDescChildren[i2]).outerHeight(true);

                if (productDescHeight > descAvailableHeight) {
                    if (descPage === 0) {
                        descAvailableHeight = innerAvailableHeight - $pageText.children().eq(0).outerHeight(true);
                    }
                    descPage++;
                    productDescPages.push([]);
                    productDescHeight = $(productDescChildren[i2]).outerHeight(true);
                }

                productDescPages[descPage].push(productDescChildren[i2]);

                if (c2 === 1000) break; // Защита от бесконечного цикла
            }

            // Распределение технических характеристик по страницам
            for (var p = 0; p < productDescPages.length; p++) {
                if (p > 0) {
                    var $clonePrototype = $prototypePage.clone();

                    $productDesc = $clonePrototype.find(".page-product-description");
                    $root.parent().append($clonePrototype);

                    console.log("$clonePrototype\n", $clonePrototype);
                    console.log("$prototypePage\n", $prototypePage);
                }

                $productDesc.html("");

                for (var r = 0; r < productDescPages[p].length; r++) {
                    $productDesc.append(productDescPages[p][r]);
                }
            }
        }
    }

    /**
     * Делит параграф на две части. Первая часть будет уменьшаться до тех пор, пока не влезет по высоте.
     * @param paragraph - HTMLElement
     * @param maxHeight - Integer
     * @return [paragraphText - String, tempParagraphText - String]
     */
    function splitParagraph(paragraph, maxHeight) {
        var firstText = paragraph.innerHTML.trim(),
            secondText = "",
            match = [],
            counter = 0;

        firstText = firstText.replace('\r', '').split('\n').join(""); // Превращаем текст в одну строку, без переносов.

        while ($(paragraph).innerHeight() > maxHeight) {
            counter++; // Защита от бесконечного цилка (в конце продолжение)

            if (firstText[firstText.length - 1] === ">") {
                // Если в конце строки есть тег переноса строки
                if ( /[<]br.{0,2}[>]$/.test(firstText) ) {
                    match = firstText.match(/[<]br.{0,2}[>]$/);
                }

                // Если в конце строки есть закрывающий тег
                if ( /[<][/][A-Za-z-]+[>]$/.test(firstText) ) {
                    var unclosedTags = [];

                    //match = firstText.match(/[^> ]+[ ]*[<][/][A-Za-z-]+[>]$/);

                    // Перебор закрывающих тегов
                    var tmpFirstText = firstText.slice(0, match.index);

                    while ( /[<][/][A-Za-z-]+[>]$/.test(tmpFirstText) ) {
                        match = tmpFirstText.match(/[<][/][A-Za-z-]+[>]$/);
                        unclosedTags.push(match[0].slice(2, match[0].length - 1)); // Помещаем название незакрытого тега
                        tmpFirstText = tmpFirstText.slice(0, match.index);
                    }

                    // Если в конце строки есть слово после > или пробела
                    if ( tmpFirstText.match(/[^> ]*[ ]*$/)[0].length > 0 ) {
                        match = tmpFirstText.match(/[^> ]*[ ]*$/); // Поиск в конце строки слова после > или пробела.
                    }
                    // Если в конце строки есть тег переноса строки
                    else if ( /[<]br.{0,2}[>]$/.test(tmpFirstText) ) {
                        match = tmpFirstText.match(/[<]br.{0,2}[>]$/);
                    }

                    tmpFirstText = tmpFirstText.slice(0, match.index);

                    // Если в конце строки есть открывающий тег
                    if ( /[<][A-Za-z-]+[>]$/.test(tmpFirstText) && !(/[<]br.{0,2}[>]$/.test(tmpFirstText)) ) {
                        while ( /[<][A-Za-z-]+[>]$/.test(tmpFirstText) ) {
                            match = tmpFirstText.match(/[<][A-Za-z-]+[>]$/);
                            unclosedTags.pop();
                            tmpFirstText = tmpFirstText.slice(0, match.index);
                        }
                    }
                    // Закрытие незакрытых тегов
                    else {
                        var firstHalf = firstText.slice(0, match.index),
                            secondHalf = firstText.slice(match.index);

                        // Подстановка открытых тегов
                        for (var i = unclosedTags.length - 1; i >= 0; i--) {
                            secondHalf = "<" + unclosedTags[i] + ">" + secondHalf;
                        }

                        // Подстановка закрывающих тегов
                        for (var j = unclosedTags.length - 1; j >= 0; j--) {
                            firstHalf = firstHalf + "</" + unclosedTags[j] + ">";
                        }

                        match.index = firstHalf.length;
                        firstText = firstHalf + secondHalf;
                    }
                }
            } else {
                match = firstText.match(/[^> ]*[ ]*$/); // Поиск в конце строки слова после > или пробела.
            }

            secondText = firstText.slice(match.index) + secondText;
            firstText = paragraph.innerHTML = firstText.slice(0, match.index);

            // Удаление нецелесообразного закрытия и открытия тега.
            // Например: <em>важное </em><em>сообщение</em> => <em>важное сообщение</em>
            while ( (secondText.indexOf("</strong><strong>") !== -1) || (secondText.indexOf("</em><em>") !== -1) || (secondText.indexOf("</u><u>") !== -1) ) {
                secondText = secondText.replace("</strong><strong>", "");
                secondText = secondText.replace("</em><em>", "");
                secondText = secondText.replace("</u><u>", "");
            }

            if (counter > 1000) break; // Защита от бесконечного цикла
        }

        // Удаление лишних переносов строк в начале текста
        while ( /^(([<](?!br)[A-Za-z-]+[>]){0,3})[<]br.{0,2}[>]/.test(secondText) ) {
            secondText = secondText.replace(/^(([<](?!br)[A-Za-z-]+[>]){0,3})[<]br.{0,2}[>]/, "$1");
        }

        return [firstText, secondText];
    }
    // TODO: не забыть в настройках CKEditor прописать forcePasteAsPlainText: true


    /**
     * TODO общий скрипт нужно бы перенести в отдельный файл так как он дублируется полностью с show.php (перенесу сам)
     * Knockout
     */
        // ViewModel
    var CommerceSettings = function(dataObj) {
            var self = this;

            if (typeof dataObj !== "undefined") {
                this.commerceTime = dataObj.commerceTime !== null ? ko.observable(dataObj.commerceTime) : ko.observable(0);
                this.inStock = dataObj.inStock !== null ? ko.observable(dataObj.inStock) : ko.observable(0);
                this.withVat = dataObj.withVat !== null ? ko.observable(dataObj.withVat) : ko.observable(0);
                this.deliveryTime = dataObj.deliveryTime !== null ? ko.observable(dataObj.deliveryTime) : ko.observable(0);
                this.productUnitShort = dataObj.productUnitShort !== null ? ko.observable(dataObj.productUnitShort) : ko.observable("");
                this.productUnitLong = dataObj.productUnitLong !== null ? ko.observable(dataObj.productUnitLong) : ko.observable("");
                this.productCount = dataObj.productCount !== null ? ko.observable(dataObj.productCount) : ko.observable(0);
                this.productPrice = dataObj.productPrice !== null ? ko.observable(dataObj.productPrice) : ko.observable(0);
                this.prepay = dataObj.prepay !== null ? ko.observable(dataObj.prepay) : ko.observable(0);
                this.postpay = dataObj.postpay !== null ? ko.observable(dataObj.postpay) : ko.observable(0);
                this.deliveryConditions = dataObj.deliveryConditions !== null ? ko.observableArray(dataObj.deliveryConditions) : ko.observableArray([]);
                this.city_id = dataObj.city_id !== null ? ko.observable(dataObj.city_id) : ko.observable(0);
                this.cities = dataObj.cities !== null ? dataObj.cities : {};
                this.address = dataObj.address !== null ? ko.observable(dataObj.address) : ko.observable("");

                this.emails = ko.observableArray();
                if (dataObj.emails !== null) {
                    dataObj.emails.forEach(function (item) {
                        this.emails.push(ko.observable(item));
                    }, this);
                }

                this.tels = ko.observableArray();
                if (dataObj.emails !== null) {
                    dataObj.tels.forEach(function (item) {
                        this.tels.push(ko.observable(item));
                    }, this);
                }

                this.bin = dataObj.bin !== null ? ko.observable(dataObj.bin) : ko.observable("");
                this.bank = dataObj.bank !== null ? ko.observable(dataObj.bank) : ko.observable("");
                this.bik = dataObj.bik !== null ? ko.observable(dataObj.bik) : ko.observable("");
                this.iik = dataObj.iik !== null ? ko.observable(dataObj.iik) : ko.observable("");
                this.kbe = dataObj.kbe !== null ? ko.observable(dataObj.kbe) : ko.observable("");
                this.knp = dataObj.knp !== null ? ko.observable(dataObj.knp) : ko.observable("");
            }
            else {
                this.commerceTime = ko.observable(0);
                this.inStock = ko.observable(true);
                this.withVat = ko.observable(true);
                this.deliveryTime = ko.observable(0);
                this.productUnitShort = ko.observable("");
                this.productUnitLong = ko.observable("");
                this.productCount = ko.observable(0);
                this.productPrice = ko.observable(0);
                this.prepay = ko.observable(0);
                this.postpay = ko.observable(0);
                this.deliveryConditions = ko.observableArray([]);
                this.city_id = ko.observable(0);
                this.cities = {};
                this.address = ko.observable("");
                this.emails = ko.observableArray([]);
                this.tels = ko.observableArray([]);
                this.bin = ko.observable("");
                this.bank = ko.observable("");
                this.bik = ko.observable("");
                this.iik = ko.observable("");
                this.kbe = ko.observable("");
                this.knp = ko.observable("");
            }

            // Подсчитываемая сумма (итоговая цена) товара
            this.totalPrice = ko.computed(function () {
                if (typeof this.productCount() !== "undefined" && typeof this.productPrice() !== "undefined") {
                    // Удаляем пробелы из цены для арифметических операций
                    var productPrice = String(this.productPrice()),
                        productCount = Number(this.productCount());

                    productPrice = productPrice.replace(/(\s|\u00A0)+/g, '');
                    productPrice = Number(productPrice);

                    return productCount * productPrice;
                }
                return "";
            }, this);

            // Форматирование вывода цены. Например: из 250000 в 250 000
            this.formatPrice = function (price) {
                if (typeof price === "undefined") {
                    return false;
                }
                if (price === "") {
                    price = 0;
                }
                price = String(price);
                // Удаляем пробелы
                price = price.replace(/(\s|\u00A0)+/g, '');
                // Добавляем пробелы через каждые 3 цифры
                return price.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 ");
            };

            // Форматирование вывода адреса с условием. Если адрес не указан, то вообще ничего видно не должно быть
            this.formattedAddress = ko.computed(function () {
                if (this.address() !== "") {
                    return ", " + this.address();
                }
            }, this);

            // Форматирование вывода условий поставки
            this.formattedDeliveryConditions = ko.computed(function () {
                if (this.deliveryConditions() !== []) {
                    var deliveryConditions = this.deliveryConditions().join(", ");
                    if (deliveryConditions.length > 1) {
                        var lastCondition = deliveryConditions.slice(-3);
                        deliveryConditions = deliveryConditions.replace(/, [A-Z]{3}$/, " и " + lastCondition);
                    }
                    return deliveryConditions;
                }
            }, this);

            this.addEmail = function () {
                self.emails.push(ko.observable(""));
            };
            this.delEmail = function (index, data, event) {
                self.emails.splice(index, 1);
            };

            this.addTel = function () {
                self.tels.push(ko.observable(""));
            };
            this.delTel = function (index, data, event) {
                self.tels.splice(index, 1);
            };

            // Отслеживание изменения значения выпадающего списка и передача события другим с таким же name
            this.selectChanged = function (data, event) {
                var otherSelects = $("select[name='" + event.target.name + "']").not(event.target);

                if (otherSelects.length !== 0) {
                    otherSelects.each(function (index, element) {
                        var evt = document.createEvent("Event");
                        evt.initEvent("changedOutside", false, true);
                        element.dispatchEvent(evt);
                    });
                }
            };

            // Отслеживание изменения значения флажка и передача события другим с таким же name и value (если он есть)
            this.checkboxClicked = function (data, event) {
                var otherCheckboxes = null;

                if (event.target.attributes.value) {
                    otherCheckboxes = $("input[type=checkbox][name='" + event.target.name + "'][value='" + event.target.attributes.value.value + "']").not(event.target);
                } else {
                    otherCheckboxes = $("input[type=checkbox][name='" + event.target.name + "']").not(event.target);
                }

                if (otherCheckboxes.length !== 0) {
                    otherCheckboxes.each(function (index, element) {
                        var evt = document.createEvent("Event");
                        evt.initEvent("clickedOutside", false, true);
                        element.dispatchEvent(evt);
                    });
                }
            };
        };

    // Объект с начальными параметрами коммерческого предложения
    var commerceData = <?=\yii\helpers\Json::encode($commercial_data);?>;
    ko.applyBindings(new CommerceSettings(commerceData));
</script>