<?
/**
 * Левое меню сайта.
 * @author Артём Широких kowapssupport@gmail.com
 */
?>
<aside id="sidebar">
    <div class="sidebar-head">
        <button class="menu menu-white"></button>
        <div class="logo logo-sm outline"><a></a></div>
    </div>
    <div class="sidebar-body">

        <div class="beta-notify-sidebar">
            <div class="beta-notify-sidebar__image"></div>
            <div class="beta-notify-sidebar__message">
                <p>Сайт находится в стадии публичного beta-тестирования. Некоторые функции могут быть недоступны или работать некорректно. Любые вопросы по поводу работы сервиса можете отправлять на электронную почту: <a href="mailto:support@goodtrade.kz">support@goodtrade.kz</a></p>
            </div>
        </div>

        <? /* Временно скрыто
            <ul class="sidebar-links">
                <li><a>О компании</a></li>
                <li><a>СМИ о нас</a></li>
                <li><a>Отзывы</a></li>
                <li><a>Реклама на сайте</a></li>
            </ul>
            <ul class="sidebar-links sidebar-links-small">
                <li><a>Пользовательское соглашение</a></li>
                <li><a>Безопасность</a></li>
                <li><a>Правила</a></li>
                <li><a>Справка и FAQ</a></li>
                <li><a>Контакты</a></li>
            </ul>
            */ ?>
        <ul class="sidebar-links">
            <li><a href="/">О проекте</a></li>
            <li><a href="/page/legal.html">Юридическая информация</a></li>
            <li><a href="/page/privacy_policy.html">Политика конфиденциальности</a></li>
            <li><a href="/page/feedback.html">Обратная связь</a></li>
        </ul>
    </div>
    <div class="sidebar-copyright">
        <p>© b2bMarket, <?= date('Y');?> <span class="sidebar-copyright__highlighted-text">18 +</span><br>Использование материалов b2bMarket разрешено только с согласия правообладателей.</p>
    </div>
</aside>