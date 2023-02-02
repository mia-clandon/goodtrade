/**
 * Разделение текста приложения коммерческого предложения, если его слишком много
 */
export default $.widget('ui.textSeparator', {
    options: {},
    _create: function () {},
    _init: function () {
        var self = this,
            $root = this.element,
            $enclosureBody = $root.find(".page-body"),
            availableHeight = 282 + (130 - $(".page-company").innerHeight()) + (48 - $(".page-requisites__title").innerHeight());

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
                        var pageTextInnerHTMLs = self.splitParagraph(pageTextChildren[i], innerAvailableHeight - pageTextHeight),
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

                $prototypePage.find(".page-text").children().each(function (index) {
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
                    }

                    $productDesc.html("");

                    for (var r = 0; r < productDescPages[p].length; r++) {
                        $productDesc.append(productDescPages[p][r]);
                    }
                }

            }
        }
    },
    /**
     * Делит параграф на две части. Первая часть будет уменьшаться до тех пор, пока не влезет по высоте.
     * @param paragraph - HTMLElement
     * @param maxHeight - Integer
     * @return [paragraphText - String, tempParagraphText - String] - Array
     */
    splitParagraph: function(paragraph, maxHeight) {
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
});
// TODO: не забыть в настройках CKEditor прописать forcePasteAsPlainText: true