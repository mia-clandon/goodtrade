<?php

/**
 * @var $this \yii\web\View
 * @var $content string
 * @var $controller \backend\controllers\BaseController
 */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$controller=Yii::$app->controller;
$show_left_menu=$controller->isShowLeftMenu();
AppAsset::register($this);

?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language; ?>">
    <head>
        <meta charset="<?= Yii::$app->charset; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow"/>
        <?php //TODO перенести шрифты.?>
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
              rel="stylesheet">
        <?= Html::csrfMetaTags(); ?>
        <title><?= Html::encode($this->title); ?></title>
        <?php $this->head(); ?>
        <?php if (!$show_left_menu) { ?>
            <style>
                @media (min-width: 768px) {
                    #page-wrapper {
                        margin: 0 !important;
                    }
                }
            </style>
        <?php } ?>
    </head>
    <body id="page-top">
    <?php $this->beginBody(); ?>

    <div id="wrapper">

        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
               href="<?= Yii::$app->urlManager->createUrl('/') ?>">
                <div class="sidebar-brand-text mx-3">goodtrade.center</div>
            </a>
            <hr class="sidebar-divider my-0">
            <div class="sidebar-heading"></div>
            <?php
            /** @var [] $menu_items */
            $menu_items=ArrayHelper::getValue(Yii::$app->params, 'menu_items');

            function translitNameItemForOpenSubmenu($nameItemForOpenSubmenu): string
            {
                $replaceCharsAndLowercase=preg_replace('/\s+/', '', (mb_strtolower($nameItemForOpenSubmenu)));
                $translitCyrillicToLatin=strtr($replaceCharsAndLowercase, array('а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'e', 'ж'=>'j', 'з'=>'z', 'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t', 'y'=>'u', 'ф'=>'f', 'x'=>'h', 'ц'=>'c', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shch', 'ы'=>'y', 'э'=>'e', 'ю'=>'yu', 'я'=>'ya', 'ъ'=>'', 'ь'=>''));
                return $translitCyrillicToLatin;
            }

            foreach ($menu_items as $menu_item) {

                $url=Yii::$app->urlManager->createUrl(ArrayHelper::getValue($menu_item, 'url', '#'));
                $icon_class=ArrayHelper::getValue($menu_item, 'icon', '');
                $name=ArrayHelper::getValue($menu_item, 'name', '');
                $sub=ArrayHelper::getValue($menu_item, 'sub', []);
                $permissions=ArrayHelper::getValue($menu_item, 'permissions', []);
                $sub_count=count($sub);
                $nameForCollapseUtilities=translitNameItemForOpenSubmenu($name);

                if (!\common\libs\helpers\Permission::i()->can($permissions)) {
                    continue;
                }
                ?>
                <hr class="sidebar-divider my-0">
                <?php if ($sub_count) { ?>
                    <li class="nav-item">
                        <a href="<?= $url; ?>"
                           class="nav-link collapsed"
                           data-toggle="collapse"
                           data-target="#collapse-<?= $nameForCollapseUtilities ?>"
                           aria-expanded="true"
                           aria-controls="collapseUtilities">
                            <span><?= $name ?></span>
                        </a>

                        <?php if ($sub_count) { ?>
                        <div id="collapse-<?= $nameForCollapseUtilities ?>" class="collapse"
                             aria-labelledby="#headingUtilities"
                             data-parent="#accordionSidebar"
                        >
                            <div class="bg-white py-2 collapse-inner rounded">
                                <h6 class="collapse-header"><?= $name ?></h6>
                                <?php } ?>
                                <?php foreach ($sub as $item) {
                                    $sub_url=Yii::$app->urlManager->createUrl(ArrayHelper::getValue($item, 'url', '#'));
                                    $sub_name=ArrayHelper::getValue($item, 'name', '');
                                    ?>
                                    <a href="<?= $sub_url; ?>" class="collapse-item text-wrap"><?= $sub_name; ?></a>
                                <?php } ?>
                                <?php if ($sub_count) { ?></div>
                        </div><?php } ?>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a href="<?= $url ?>" class="nav-link collapsed">
                           <span> <?= $name; ?></span>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <div id="page-wrapper" class="d-flex flex-column" style="width: 100%">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                 aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                               placeholder="Search for..." aria-label="Search"
                                               aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Выйти</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                 aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="/logout" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Выйти
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">
                    <?php
                    $success_type=\backend\controllers\BaseController::FLASH_MESSAGE_SUCCESS;
                    $error_type=\backend\controllers\BaseController::FLASH_MESSAGE_ERROR;
                    $messages=[$success_type=>Yii::$app->session->getFlash($success_type), $error_type=>Yii::$app->session->getFlash($error_type),];
                    $messages=array_filter($messages);
                    if ($messages) {
                        ?>
                        <div class="row">
                            <div class="col-md-offset-6 col-md-6">
                                <?php foreach ($messages as $type=>$message) { ?>
                                    <div class="alert alert-danger" role="alert" style="margin: 5px 0 5px 0;">
                                        <?= $message; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?= $content; ?>
                    <br/><br/>
                </div>
            </div>
        </div>

    </div>
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Вы уверены?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Выбирая вариант выйти вы обрываете текущую сессию.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Отменить</button>
                    <a class="btn btn-primary" href="/logout">Выйти</a>
                </div>
            </div>
        </div>
    </div>
    <?php $this->endBody(); ?>
    </body>
    </html>
<?php $this->endPage(); ?>