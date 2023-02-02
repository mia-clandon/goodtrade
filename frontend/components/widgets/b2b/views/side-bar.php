<?
/**
 * Левое меню сайта(новая верстка).
 * yerganat
 */
?>

<aside class="nav-left">
    <div class="nav-left__top-part">
        <div class="nav-left__header">
            <div class="nav-left__close-button-container">
                <button class="nav-left__close-button"></button>
            </div>
            <a href="#" class="logo logo_sm-white" title="GoodTrade.kz"></a>
        </div>

        <div class="beta-notify-sidebar">
            <div class="beta-notify-sidebar__image"></div>
            <div class="beta-notify-sidebar__message">
                <p>Сайт находится в стадии публичного beta-тестирования. Некоторые функции могут быть недоступны или работать некорректно. Любые вопросы по поводу работы сервиса можете отправлять на электронную почту: <a href="mailto:support@goodtrade.kz">support@goodtrade.kz</a></p>
            </div>
        </div>

        <nav class="nav-left__menu">
            <ul class="linear-list linear-list_white">
                <li><a href="/">О проекте</a></li>
                <?//todo: ссылки на страницы сайта.?>
                <li><a href="/page/legal.html">Юридическая информация</a></li>
                <li><a href="/page/privacy_policy.html">Политика конфиденциальности</a></li>
                <li><a href="/page/feedback.html">Обратная связь</a></li>
            </ul>
        </nav>
    </div>
    <div class="nav-left__bottom-part">
        <div class="nav-left__copyright">
            <span class="nav-left__copyright-first-line">© GoodTrade.kz, <?= date('Y');?></span>
            <div class="label label_white-invert">18+</div>
            <p>Использование материалов<br>
                GoodTrade.kz разрешено только<br>
                с согласия правообладателей.</p>
        </div>
    </div>
</aside>