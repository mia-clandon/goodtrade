<?
/**
 * Футер сайта.
 * @author Артём Широких kowapssupport@gmail.com
 */
?>
<footer role="contentinfo" class="undefined">
    <div class="container">
        <div class="col-xs-6">
            <div class="row links-wrap">
                <div class="col-xs-6 col-sm-2 col-md-1 col-lg-1 text-center">
                    <a href="<?= Yii::$app->urlManager->createUrl(['site/index']);?>" id="logo">
                        <!--<img src="/img/logo-md-black.png">-->
                    </a>
                </div>
                <div class="col-xs-offset-1 col-xs-2 col-sm-2 col-sm-offset-0 col-md-1">
                    <a href="/" class="link block">О проекте</a>
                </div>
                <div class="col-xs-2 col-md-1 col-sm-2">
                    <a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'legal']);?>" class="link block">Юридическая информация</a>
                </div>
                <div class="col-xs-offset-1 col-xs-2 col-sm-2 col-sm-offset-2 col-md-1 col-md-offset-0">
                    <a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'privacy_policy']);?>" class="link block">Политика конфиденциальности</a>
                </div>
                <div class="col-xs-2 col-md-1 col-sm-2">
                    <a href="<?= Yii::$app->urlManager->createUrl(['page/show', 'page' => 'feedback']);?>" class="link block">Обратная связь</a>
                </div>
            </div>
            <div class="row info-wrap">
                <div class="col-xs-6 col-sm-3">
                    <div class="copyright text-sm-center">
                        &copy; GoodTrade.kz, <?= date('Y');?><br>
                        Использование материалов GoodTrade.kz разрешено только с согласия правообладателей.<br>
                        Сайт имеет возрастное ограничение 18+
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3">
                    <div class="address text-sm-center text-sm-offset-top">
                        <!--ТОО «goodtrade.kz». Юридический адрес: 117545, г. Костанай,<br>
                        ул. Дорожная, д.8, корп.1, офис 103 ИНН 7726338554<br>
                        КПП 772601001 ОГРН 1157746406850-->
                        <? /*ТОО «goodtrade.kz»<br>
                        Юридический адрес: 110000, г. Костанай, ул. Карбышева, 16. БИН 160340028349</br>
                        Счёт в АО «КАЗКОМЕРЦ Банк», ИИК KZ04926160119W579000, БИК KZKOKZKX */?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>