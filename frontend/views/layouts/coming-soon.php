<?
/**
 * @var string $content
 */
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <?= Html::csrfMetaTags() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <link rel="stylesheet" href="/soon/css/style.css">
    <link href="/soon/css/materialdesignicons.min.css" media="all" rel="stylesheet" type="text/css"/>
    <title>Good Trade</title>
     <link rel="shortcut icon" href="/soon/img/favicon.gif" type="image/x-icon">
    <?php $this->head(); ?>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KWJQVB"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
</head>

<body class="dark">
<?php $this->beginBody() ?>
<aside class="bar_left">
    <header class="bar_left__logo">
        <a href="/">b2bMarket</a>
    </header>
    <nav class="bar_left__navigation">
        <ul class="bar_left__navigation__primary">
            <li>
                <a href="/">Главная</a>
                <a href="/">О компании</a>
                <a href="/">Тарифы</a>
                <a href="/">Блог</a>
                <a href="/">Контакты</a>
            </li>
        </ul>
        <ul class="bar_left__navigation__secondary">
            <li>
                <a href="/">СМИ о нас</a>
                <a href="/">Реклама на сайте</a>
                <a href="/">Справка и FAQ</a>
                <a href="/">Администрация</a>
            </li>
        </ul>
    </nav>
</aside>

<div class="block_main">
    <header class="bar_top ">
        <div class="container-fluid">
            <!--<span class="bar_top__burger">
                <span class="line_1"></span>
                <span class="line_2"></span>
                <span class="line_3"></span>
            </span>-->
            <a href="/" class="bar_top__logo">
                <svg xmlns="http://www.w3.org/2000/svg" class="logo" viewBox="0 0 592.9 87.3">
                    <path class="logo-path"
                          d="M74.8 38H49.3c-.9 0-1.4.4-1.7 1.3l-4 13.9c-.1 1.2.3 1.8 1.4 1.8h12.8v11.4c-.4.3-.9.7-1.3 1-2.2 1.2-6.3 3-11.7 3.2-1.3 0-5.3.1-10.2-1.7-1.9-.7-5.5-2.3-8.8-5.4-8.4-7.8-8.5-19.1-8.4-20.1.1-9.3 5.1-17.6 11.1-21.7 3.3-2.2 7.3-3.4 7.5-3.5.9-.3 4.2-1.2 8.6-1.2 5.2 0 9.4 1.3 12.3 2.5 1.8.8 3.6 1.5 5.3 2.3.1 0 .6.2 1.1-.1.5-.3.7-.8.7-.9l5.1-13.2c-5-3.5-10.8-6-17-7.2C49.5.1 47 0 44.5 0c-4.2-.1-12.4.4-21.1 4.9-2.5 1.3-7.6 4.2-12.2 9.3C-.2 26.6-.1 42.9 0 47.3c.6 5.3 3 18.5 13.8 28.3 4.5 4 8.9 6.2 11 7.1 3.8 1.8 12.7 5.8 24.4 4.4 1.1-.1 16.1-2.4 27.2-14.1.2-.2.3-.3.4-.5v-33c-.7-.5-1.3-1-2-1.5zM374.7 30.4v9.3c0 .7-.3 1-1 1h-15.8V85c0 .7-.3 1-1 1H346c-.7 0-1-.3-1-1V40.7h-15.8c-.7 0-1-.3-1-1v-9.3c0-.7.3-1 1-1h44.5c.6-.1 1 .3 1 1zM383.7 29.3H406c6.3 0 11.3 1.5 14.8 4.6 3.5 3.1 5.3 7.3 5.3 12.8 0 3.4-.9 6.4-2.7 8.8-1.8 2.5-4.1 4.3-6.7 5.5-1 .5-1 1-.1 1.5 2.7 1.7 5 4.6 7 8.8l5.6 13.5c.3.8 0 1.2-.8 1.2h-11.5c-.6 0-1-.3-1.2-.8l-3.5-8.6c-1.6-3.9-3.4-6.7-5.3-8.3-1.9-1.7-4.9-2.5-9-2.5h-2.2V85c0 .7-.3 1-1 1h-10.9c-.7 0-1-.3-1-1V30.4c-.1-.7.2-1.1.9-1.1zm11.9 25.3h8.7c5.9 0 8.8-2.3 8.8-7 0-2.1-.7-3.8-2-5.1-1.3-1.3-3-1.9-5.2-1.9h-10.4v14zM473.8 78.5h-23.5l-2.4 6.7c-.2.5-.6.8-1.2.8h-11.3c-.9 0-1.1-.4-.9-1.2l11.3-30.6c3.5-9.5 6-17.5 7.6-24.1.2-.6.6-.9 1.2-.9h14.9c.6 0 1 .3 1.2.9 1.6 6.6 4.1 14.6 7.6 24.1l11.3 30.6c.3.8 0 1.2-.9 1.2h-11.3c-.6 0-1-.3-1.2-.8l-2.4-6.7zm-4.1-11.2l-1.1-3.2c-3.1-8.5-5-14.8-5.8-19-.1-.5-.3-.7-.7-.7-.4 0-.6.2-.7.7-.9 4.4-2.8 10.7-5.8 19l-1.1 3.2h15.2zM498.2 29.3H518c7.9 0 14.2 2.5 18.8 7.6 4.6 5.1 6.8 12 6.8 20.8 0 8.5-2.3 15.4-7 20.6-4.6 5.2-10.9 7.8-18.6 7.8h-19.8c-.7 0-1-.3-1-1V30.4c0-.7.3-1.1 1-1.1zm11.9 11.4v34.1h7.9c3.9 0 7-1.5 9.2-4.5 2.2-3 3.4-7.2 3.4-12.6 0-5.4-1.1-9.6-3.4-12.6-2.2-3-5.3-4.4-9.2-4.4h-7.9zM565.4 51.3H585c.7 0 1 .3 1 1v9.2c0 .7-.3 1-1 1h-19.6v12.2h26.2c.7 0 1 .3 1 1V85c0 .7-.3 1-1 1h-38.1c-.7 0-1-.3-1-1V30.4c0-.7.3-1 1-1H592c.7 0 1 .4.9 1.2l-2.6 9.3c-.2.6-.5.9-1.1.9h-23.8v10.5z"/>
                    <g>
                        <path class="logo-path"
                              d="M160.2 69.3c-5.3-7.2-8.4-16.2-8.4-25.8 0-10.6 3.8-20.3 10-27.9 6 7 9.6 15.8 9.1 25.5-.1.5-.1.9-.1 1.4v.3c-.9 9.4-4.8 18.8-10.6 26.5z"/>
                        <path class="logo-path"
                              d="M161.8 15.6c2-2.5 4.3-4.7 6.9-6.7 4.2 4.9 7.4 10.6 9.6 16.8-4.1 4-6.8 9.3-7.4 15.3.4-9.6-3.1-18.4-9.1-25.4z"/>
                        <path class="logo-path"
                              d="M168.6 78.1c4.2-4.9 7.4-10.6 9.6-16.8 4.5 4.4 10.6 7.1 17.3 7.1 13.7 0 24.8-11.1 24.8-24.8 0-13.7-11.2-24.8-24.8-24.8-6.7 0-12.9 2.7-17.3 7.1-2.2-6.2-5.5-11.9-9.6-16.8 7.4-5.8 16.8-9.3 27-9.3 24.2 0 43.8 19.6 43.8 43.8 0 24.2-19.6 43.8-43.8 43.8-10.2-.1-19.5-3.5-27-9.3z"/>
                    </g>
                    <g>
                        <path class="logo-path"
                              d="M162.2 17.7c5.3 7.2 8.4 16.2 8.4 25.8 0 10.6-3.8 20.3-10 27.9-6-7-9.6-15.8-9.1-25.5.1-.5.1-.9.1-1.4v-.3c.9-9.4 4.8-18.8 10.6-26.5z"/>
                        <path class="logo-path"
                              d="M160.6 71.4c-2 2.5-4.3 4.7-6.9 6.7-4.2-4.9-7.4-10.6-9.6-16.8 4.1-4 6.8-9.3 7.4-15.3-.4 9.6 3.1 18.4 9.1 25.4z"/>
                        <path class="logo-path"
                              d="M153.8 8.9c-4.2 4.9-7.4 10.6-9.6 16.8-4.5-4.4-10.6-7.1-17.3-7.1-13.7 0-24.8 11.1-24.8 24.8 0 13.7 11.2 24.8 24.8 24.8 6.7 0 12.9-2.7 17.3-7.1 2.2 6.2 5.5 11.9 9.6 16.8-7.4 5.8-16.8 9.3-27 9.3-24.2.1-43.8-19.5-43.8-43.7C83 19.3 102.6-.3 126.8-.3c10.2 0 19.5 3.4 27 9.2z"/>
                    </g>
                    <g>
                        <path class="logo-path"
                              d="M247.7 1h29.8c4-.2 11.8.1 20.1 4.2 1.7.8 6.4 3.2 11 7.8C321.8 26 322 43.5 322 43.5c.1 13.1-5.9 25.5-15 32.7-4.5 3.6-9.4 5.6-11.3 6.3-7.3 2.9-13.9 3.5-18.1 3.6h-29.8c-1 0-1.6-.5-1.6-1.6v-82l1.5-1.5zm17.9 17v51.1h11.9c2.2.1 5.7-.1 9.8-1.6 1.7-.7 5-2.1 8-4.8 8.2-7.4 8-18.5 8-19.5 0-.7-.5-11.1-8.5-18.5-3.7-3.4-7.5-4.9-8.3-5.2-3.7-1.4-6.9-1.5-8.9-1.5h-12z"/>
                    </g>
                </svg>
            </a>
            <div class="bar_top__actions">
                <ul>
                    <li><a href="#">Политика конфиденциальности</a></li>
                    <!--<li><span class="button line_main">Получить бесплатный доступ</span></li>-->
                </ul>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="row">
            <div class="block_main__headline col-xs-12">
                <h1>Good trade - платформа для эффективного налаживания новых бизнес связей</h1>
                <!--<h2>Платформа для эффективного управления продажами и закупками со всеми необходимыми для этого интрументами</h2>-->
                <h2>Сайт находится в разработке</h2>
            </div>
        </div>
        <div class="block_main__statistics row">
            <div class="col-md-2"></div>
            <div class="col-md-8 col-xs-12">
                <p> Цель <b>Good Trade</b> – Предоставить предпринимателям
                    необходимые инструменты для поиска, коммуникации и аналитики с&nbsp;целью увеличения вероятности
                    достижения успешности сделок.
                </p>
                <p>Роль <b>Good Trade</b> – сопровождение клиента по сценарию принятия бизнес
                    решений на основании коммерческих опросов других участников
                    платформы и управления событиями до момента вывода коммуникации в
                    оффлайн для заключения сделок.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2"></div>
            <?=$content;?>
            <div class="col-md-2"></div>
        </div>
    </div>
    <div class="block_main__background"></div>
</div>

<script>
    document.getElementById("block_main").style.height = height;
</script>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KWJQVB');</script>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KWJQVB"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>