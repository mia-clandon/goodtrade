<?
/**
 * Футер сайта(новая верстка).
 * @author yerganat
 */
?>
<footer>
    <div class="container container_main">
        <div class="row">
            <div class="col-lg-1 col-lg_no-right-gutter">
                <a href="#"
                   class="logo logo_md logo_footer" title="GoodTrade.kz"></a>
            </div>
            <div class="col-lg-3">
                <nav class="multi-col-menu">
                    <div class="row">
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'about']);?>">О компании</a></div>
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'blog']);?>">Блог</a></div>
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'media']);?>">СМИ о нас</a></div>
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'tariff']);?>">Тарифы</a></div>
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'contact']);?>">Контакты</a></div>
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'advertising']);?>">Реклама на сайте</a></div>
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'faq']);?>">Справка и FAQ</a></div>
                        <div class="col-4 col_no-right-gutter col-lg col-lg_third multi-col-menu__item"><a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'admin']);?>">Администрация</a></div>
                    </div>
                </nav>
            </div>
            <div class="col-lg-4">
                <div class="footer__copyright">
                    <p>© GoodTrade.kz, <?= date('Y');?><br>
                        Использование материалов GoodTrade.kz разрешено только с согласия правообладателей.<br>
                        Сайт имеет возрастное ограничение 18+</p>
                    <?/*<p>ТОО «goodtrade.kz».<br>
                        Юридический адрес: 110000, г. Костанай, ул. Карбышева, 16.  БИН 160340028349.<br>
                        Счёт в АО «КАЗКОМЕРЦ Банк»,  ИИК KZ04926160119W579000, БИК KZKOKZKX</p>*/?>
                </div>
            </div>
        </div>
    </div>
</footer>