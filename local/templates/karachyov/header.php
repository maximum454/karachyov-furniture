<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

// Подключаем стили и скрипты через ядро
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/assets/index-MSZETOk0.css');
Asset::getInstance()->addString('<script type="module" crossorigin src="' . SITE_TEMPLATE_PATH . '/assets/index-BfRHxpZN.js"></script>');
?>
<!DOCTYPE html>
<html lang="<?= LANGUAGE_ID ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php $APPLICATION->ShowTitle(); ?></title>

    <?php $APPLICATION->ShowHead(); ?>

    <!-- Иконки и Favicon -->
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="<?= SITE_TEMPLATE_PATH ?>/assets/favicon-BvOYcBHh.png" type="image/png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#2F352E">

    <!-- Open Graph (можно сделать динамическим через ShowProperty) -->
    <meta property="og:site_name" content="KARACHYOV">
    <meta property="og:title" content="<?php $APPLICATION->ShowProperty("og_title", "KARACHYOV — Кровати, которые стоят вашего времени"); ?>">
    <meta property="og:description" content="<?php $APPLICATION->ShowProperty("og_description", "Созданы для тех, кто ценит качество, комфорт и продуманный дизайн."); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://karachyov.kz/">
    <meta property="og:image" content="https://karachyov.kz/images/og-cover.jpg">
    <meta property="og:locale" content="ru_KZ">

    <!-- Шрифты -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class="page">
    <?php $APPLICATION->ShowPanel(); ?>

    <!-- 1. HEADER -->
    <header class="header">
        <div class="header__container">
            <a href="/" class="header__logo logo" aria-label="KARACHYOV - На главную">
                <img src="<?= SITE_TEMPLATE_PATH ?>/assets/logo-B_s-j38y.png" alt="Логотип KARACHYOV" class="logo__img" width="80" height="80">
            </a>

            <nav class="header__nav nav">
                <?php
                // Здесь в будущем подключается меню Битрикса (bitrix:menu)
                ?>
                <ul class="nav__list">
                    <li class="nav__item"><a href="#advantages" class="nav__link">Преимущества</a></li>
                    <li class="nav__item"><a href="#catalog" class="nav__link">Каталог</a></li>
                    <li class="nav__item"><a href="#about" class="nav__link">О нас</a></li>
                    <li class="nav__item"><a href="#contacts" class="nav__link">Контакты</a></li>
                </ul>
            </nav>

            <div class="header__contacts">
                <a href="tel:+77477747047" class="header__phone">+7 747 774 70 47</a>
            </div>
        </div>
    </header>

    <main class="main">