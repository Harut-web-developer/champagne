<?php
/** @var string $content */

use app\assets\AppAsset;
//$sub_page = $this->params['sub_page'];

AppAsset::register($this);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
        <title>Login page</title>
        <meta name="description" content="" />
        <!-- Favicon -->
        <?php
        $this->registerLinkTag([
            'rel' => 'icon',
            'type' => 'image/x-icon',
            'href' => Yii::getAlias('@web/favicon.ico')
        ]);
        ?>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="/fonts/boxicons.css" />
        <!-- Core CSS -->
        <link rel="stylesheet" href="/css/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="/css/theme-default.css" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="/css/demo.css" />
        <!-- Vendors CSS -->
        <link rel="stylesheet" href="/css/perfect-scrollbar.css" />
        <!-- Page CSS -->
        <link rel="stylesheet" href="/css/page-auth.css" />
        <link rel="stylesheet" href="/css/main.css" />
    <head>
    <body>
        <!-- Content -->

        <div class="container-xxl">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner">
                    <!-- Register -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="app-brand justify-content-center">
                                <div class="app-brand-link gap-2">
                                    <img src="/img/logo.png" alt="">
                                    <span class="app-brand-text demo text-body fw-bold">Champagne</span>
                                </div>
                            </div>
                            <!-- /Logo -->
                            <h4 class="mb-2">Բարի գալուստ Champagne </h4>
                            <p class="mb-4">Խնդրում ենք մուտք գործել ձեր հաշիվ</p>
                            <?= $content ?>
                            <p class="text-center">
<!--                                <span>Նորությո՞ւն մեր հարթակում:</span>-->
<!--                                <a href="auth-register-basic.html">-->
<!--                                    <span>Ստեղծել հաշիվ</span>-->
<!--                                </a>-->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/js/helpers.js"></script>
        <script src="/js/config.js"></script>
        <script src="/js/jquery.js"></script>
        <script src="/js/popper/popper.js"></script>
        <script src="/js/bootstrap.js"></script>
        <script src="/js/perfect-scrollbar.js"></script>
        <script src="/js/menu.js"></script>
        <script src="/js/main.js"></script>
    </body>
<html>
