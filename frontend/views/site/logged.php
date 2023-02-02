<?
/**
 * Страница авторизованного пользователя.
 * @var string $search_form
 * @author Артём Широких kowapssupport@gmail.com
 */
?>

<main role="main" id="page-wrap" class="undefined">
    <div class="container">


        <?//todo: var_dump($search_form);?>


        <div class="row">
            <div class="col-xs-6">
                <form action="/product/index" method="GET" class="has-validation-callback">
                    <div class="search">
                        <div class="input-group input-group-search">
                            <div class="input">
                                <input type="text" class="input-field" name="query" value="" placeholder="Поставщики и товары" required="required">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-blue btn-outline" name="submit">Найти</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="preview-items-block preview-items-block_recommended-companies">
            <div class="row">
                <div class="col-xs-6">
                    <h2 class="sub-title text-sm-center">18 производителей используют похожий на ваш продукт</h2>
                </div>
            </div>
            <div class="preview-items">
                <div class="row">
                    <div class="slider slider-company">
                        <div class="slider-item">
                            <div class="col-xs-6 col-md-3 col-lg-3 preview preview-small">
                                <div class="row">
                                    <div class="col-xs-6 col-md-6 col-lg-2 preview-thumbnail">
                                        <img src="../img/company-preview-sm.png"/>
                                    </div>
                                    <div class="col-xs-6 col-md-6 col-lg-4 preview-content">
                                        <div class="preview-title-block">
                                            <a href="/" class="preview-title">ТОО УралМаш</a>
                                        </div>
                                        <div class="preview-result">По запросу найдено <span>4 наименования</span></div>
                                        <ul class="preview-thumbs">
                                            <li><img src="../img/company-preview-sm-1.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                            <li><img src="../img/company-preview-sm-3.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-3 col-lg-3 preview preview-small">
                                <div class="row">
                                    <div class="col-xs-6 col-md-6 col-lg-2 preview-thumbnail">
                                        <img src="../img/company-preview-sm.png"/>
                                    </div>
                                    <div class="col-xs-6 col-md-6 col-lg-4 preview-content">
                                        <div class="preview-title-block">
                                            <a href="/" class="preview-title">ТОО УралМаш</a>
                                        </div>
                                        <div class="preview-result">По запросу найдено <span>4 наименования</span></div>
                                        <ul class="preview-thumbs">
                                            <li><img src="../img/company-preview-sm-1.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                            <li><img src="../img/company-preview-sm-3.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="slider-item">
                            <div class="col-xs-6 col-md-3 col-lg-3 preview preview-small">
                                <div class="row">
                                    <div class="col-xs-6 col-md-6 col-lg-2 preview-thumbnail">
                                        <img src="../img/company-preview-sm.png"/>
                                    </div>
                                    <div class="col-xs-6 col-md-6 col-lg-4 preview-content">
                                        <div class="preview-title-block">
                                            <a href="/" class="preview-title">ТОО УралМаш</a>
                                        </div>
                                        <div class="preview-result">По запросу найдено <span>4 наименования</span></div>
                                        <ul class="preview-thumbs">
                                            <li><img src="../img/company-preview-sm-1.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                            <li><img src="../img/company-preview-sm-3.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6 col-md-3 col-lg-3 preview preview-small">
                                <div class="row">
                                    <div class="col-xs-6 col-md-6 col-lg-2 preview-thumbnail">
                                        <img src="../img/company-preview-sm.png"/>
                                    </div>
                                    <div class="col-xs-6 col-md-6 col-lg-4 preview-content">
                                        <div class="preview-title-block">
                                            <a href="/" class="preview-title">ТОО УралМаш</a>
                                        </div>
                                        <div class="preview-result">По запросу найдено <span>4 наименования</span></div>
                                        <ul class="preview-thumbs">
                                            <li><img src="../img/company-preview-sm-1.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                            <li><img src="../img/company-preview-sm-3.png"/></li>
                                            <li><img src="../img/company-preview-sm-2.png"/></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-4 col-xs-offset-1">
                    <div class="btn btn-blue block">Смотреть полный список</div>
                </div>
            </div>
        </div>

        <div class="preview-items-block">
            <div class="row">
                <div class="col-xs-6">
                    <div class="sub-title">Возможно вас заинтересуют эти поставщики</div>
                </div>
            </div>
            <div class="preview-items">
                <div class="row">
                    <div class="col-xs-3 preview preview-small">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/product_category_stubs/textile_and_clothing.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">ТОО &laquo;VIP CARPET VE TEKSTIL (ВИП КАРПЕТ ВЕ ТЕКСТИЛЬ)&raquo;</a>
                                </div>
                                <div class="preview-item-categories-block">
                                    <div class="preview-item-category"><span>Текстиль, одежда</span></div>
                                    <div class="preview-item-category"><span>Прядильное, ткацкое и отделочное производство</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3 preview preview-small">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/product_category_stubs/construction.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">ТОО &laquo;STROY SERVICE DEVELOPMENT&raquo;</a>
                                </div>
                                <div class="preview-item-categories-block">
                                    <div class="preview-item-category"><span>Стройка</span></div>
                                    <div class="preview-item-category"><span>Прочие отделочные работы</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3 preview preview-small">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/product_category_stubs/instrumentation.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">ЗАО &laquo;Новые Промышленные Системы&raquo;</a>
                                </div>
                                <div class="preview-item-categories-block">
                                    <div class="preview-item-category"><span>Приборостроение</span></div>
                                    <div class="preview-item-category"><span>Монтаж систем водоснабжения, отопления и кондиционирования воздуха</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3 preview preview-small">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/product_category_stubs/agriculture.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">ТОО &laquo;AKS AGRO&raquo;</a>
                                </div>
                                <div class="preview-item-categories-block">
                                    <div class="preview-item-category"><span>Сельское хозяйство</span></div>
                                    <div class="preview-item-category"><span>Оптовая торговля зерном, семенами и кормами для животных</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3 preview preview-small">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/product_category_stubs/agriculture.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">ТОО &laquo;DARKHAN AGRO (ДАРХАН АГРО)&raquo;</a>
                                </div>
                                <div class="preview-item-categories-block">
                                    <div class="preview-item-category"><span>Сельское хозяйство</span></div>
                                    <div class="preview-item-category"><span>Животноводство</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3 preview preview-small">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/product_category_stubs/agriculture.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">ТОО &laquo;ALAR-TRADE AGRO&raquo;</a>
                                </div>
                                <div class="preview-item-categories-block">
                                    <div class="preview-item-category"><span>Сельское хозяйство</span></div>
                                    <div class="preview-item-category"><span>Животноводство</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-xs-offset-1">
                    <div class="btn block btn-offset-search">Показать еще</div>
                </div>
            </div>
        </div>

        <div class="preview-items-block">
            <div class="row">
                <div class="col-xs-6">
                    <div class="sub-title">Возможно вас заинтересуют эти предложения</div>
                </div>
            </div>
            <div class="preview-items">
                <div class="row">
                    <div class="col-xs-3 preview preview-small fade-menu-wrap">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/preview-small.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">Промышленная печь СДО-4.10.4/12,5 с выкатным
                                        подом</a>
                                </div>
                                <div class="preview-price-block">
                                    <div class="price">380 000 тг.</div>
                                    <div class="label">Ориентировочно</div>
                                </div>
                                <div class="preview-offer">
                                    <div class="popup-dropdown">
                                        <div class="btn btn-sm btn-lowercase popup-toggle">
                                            <span>Коммерческий запрос</span><i class="icon icon-sm-commerce"></i><i
                                                    class="icon icon-complete"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fade-menu fade-menu_preview">
                            <a role="button" href="#" data-id="1" data-key="hold" class="action js-keeper"><span class="action-icon action-icon-hold"></span></a>
                            <a role="button" href="#" data-id="1" data-key="compare" class="action js-keeper"><span class="action-icon action-icon-compare"></span></a>
                        </div>
                    </div>
                    <div class="col-xs-3 preview preview-small fade-menu-wrap">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/preview-small.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">Промышленная печь СДО-4.10.4/12,5 с выкатным
                                        подом</a>
                                </div>
                                <div class="preview-price-block">
                                    <div class="price">380 000 тг.</div>
                                    <div class="label">Ориентировочно</div>
                                </div>
                                <div class="preview-offer">
                                    <div class="popup-dropdown">
                                        <div class="btn btn-sm btn-lowercase popup-toggle">
                                            <span>Коммерческий запрос</span><i class="icon icon-sm-commerce"></i><i
                                                    class="icon icon-complete"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fade-menu fade-menu_preview">
                            <a role="button" href="#" data-id="2" data-key="hold" class="action js-keeper"><span class="action-icon action-icon-hold"></span></a>
                            <a role="button" href="#" data-id="2" data-key="compare" class="action js-keeper"><span class="action-icon action-icon-compare"></span></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3 preview preview-small fade-menu-wrap">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/preview-small.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">Промышленная печь СДО-4.10.4/12,5 с выкатным
                                        подом</a>
                                </div>
                                <div class="preview-price-block">
                                    <div class="price">380 000 тг.</div>
                                    <div class="label">Ориентировочно</div>
                                </div>
                                <div class="preview-offer">
                                    <div class="popup-dropdown">
                                        <div class="btn btn-sm btn-lowercase popup-toggle">
                                            <span>Коммерческий запрос</span><i class="icon icon-sm-commerce"></i><i
                                                    class="icon icon-complete"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fade-menu fade-menu_preview">
                            <a role="button" href="#" data-id="3" data-key="hold" class="action js-keeper"><span class="action-icon action-icon-hold"></span></a>
                            <a role="button" href="#" data-id="3" data-key="compare" class="action js-keeper"><span class="action-icon action-icon-compare"></span></a>
                        </div>
                    </div>
                    <div class="col-xs-3 preview preview-small fade-menu-wrap">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/preview-small.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">Промышленная печь СДО-4.10.4/12,5 с выкатным
                                        подом</a>
                                </div>
                                <div class="preview-price-block">
                                    <div class="price">380 000 тг.</div>
                                    <div class="label">Ориентировочно</div>
                                </div>
                                <div class="preview-offer">
                                    <div class="popup-dropdown">
                                        <div class="btn btn-sm btn-lowercase popup-toggle">
                                            <span>Коммерческий запрос</span><i class="icon icon-sm-commerce"></i><i
                                                    class="icon icon-complete"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fade-menu fade-menu_preview">
                            <a role="button" href="#" data-id="4" data-key="hold" class="action js-keeper"><span class="action-icon action-icon-hold"></span></a>
                            <a role="button" href="#" data-id="4" data-key="compare" class="action js-keeper"><span class="action-icon action-icon-compare"></span></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-3 preview preview-small fade-menu-wrap">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/preview-small.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">Промышленная печь СДО-4.10.4/12,5 с выкатным
                                        подом</a>
                                </div>
                                <div class="preview-price-block">
                                    <div class="price">380 000 тг.</div>
                                    <div class="label">Ориентировочно</div>
                                </div>
                                <div class="preview-offer">
                                    <div class="popup-dropdown">
                                        <div class="btn btn-sm btn-lowercase popup-toggle">
                                            <span>Коммерческий запрос</span><i class="icon icon-sm-commerce"></i><i
                                                    class="icon icon-complete"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fade-menu fade-menu_preview">
                            <a role="button" href="#" data-id="5" data-key="hold" class="action js-keeper"><span class="action-icon action-icon-hold"></span></a>
                            <a role="button" href="#" data-id="5" data-key="compare" class="action js-keeper"><span class="action-icon action-icon-compare"></span></a>
                        </div>
                    </div>
                    <div class="col-xs-3 preview preview-small fade-menu-wrap">
                        <div class="row">
                            <div class="col-xs-2 preview-thumbnail"><img src="../img/preview-small.png"/></div>
                            <div class="col-xs-4 preview-content">
                                <div class="preview-title-block">
                                    <a href="/" class="preview-title">Промышленная печь СДО-4.10.4/12,5 с выкатным
                                        подом</a>
                                </div>
                                <div class="preview-price-block">
                                    <div class="price">380 000 тг.</div>
                                    <div class="label">Ориентировочно</div>
                                </div>
                                <div class="preview-offer">
                                    <div class="popup-dropdown">
                                        <div class="btn btn-sm btn-lowercase popup-toggle">
                                            <span>Коммерческий запрос</span><i class="icon icon-sm-commerce"></i><i
                                                    class="icon icon-complete"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="fade-menu fade-menu_preview">
                            <a role="button" href="#" data-id="6" data-key="hold" class="action js-keeper"><span class="action-icon action-icon-hold"></span></a>
                            <a role="button" href="#" data-id="6" data-key="compare" class="action js-keeper"><span class="action-icon action-icon-compare"></span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-xs-offset-1">
                    <div class="btn block btn-offset-search">Показать еще</div>
                </div>
            </div>
        </div>
    </div>
</main>