<?php
// include database
require "includes/mysql-database.php";
/* include functions */
require "functions.php";
/*starts buffering output  */
ob_start();
/* Session start */
session_start();

// Initialize default values for metadata variables
$headTitle = $pageTitle = $headDescription = "";

// Fetch pages from the database
$pages = fetchData($con_db, 'pages', $condition = '', $orderBy = '', $limit = '' );

/* Set active links for sidebar navigation */
$activeClass = "navigation__link--active";
// Get the current page name
$currentPage = basename($_SERVER['REQUEST_URI']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $headDescription ?>"> <!-- SEO description -->
    <link rel="stylesheet" href="./sass/style.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rubik:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="icon" href="./img/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title><?= $headTitle ?></title> <!-- SEO title -->
</head>

<body>
    <!-- Default header with navigation for all pages including hamburger menu on 900px -->
    <header class="primary-header">
        <div class="navigation flex-system space-between flex-ver-center sticky">
            <a href="#" title="logo" aria-label="logo"><img src="./img/logo.png" alt="logo" title="website logo" class="mylogo"></a>
            <input type="checkbox" class="navigation__checkbox" id="hamburger-menu">
            <label for="hamburger-menu" class="navigation__button">
                <span class="navigation__icon">&nbsp;</span>
            </label>
            <!-- Nice background color effect when opening hamburger menu -->
            <div class="navigation__background">&nbsp;</div>
            <nav class="navigation__main-nav">
                <ul class="navigation__list">
                    <!-- foreach loop for dynamic navigation -->
                    <?php foreach ($pages as $menu) :
                        $isActive = basename($menu['page_url']) === $currentPage ? $activeClass  : '';
                    ?>
                        <li class="navigation__item">
                            <a href="<?= $menu['page_url'] ?>" class="navigation__link <?= $isActive ?>" title="<?= $menu['page_title'] ?>" aria-label="<?= $menu['page_title'] ?>">
                                <?= $menu['page_title'] ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <!-- If not logged in show login page -->
                    <?php if (!isset($_SESSION["username"])) : ?>
                        <li class="navigation__item">
                            <a href="login.php" class="navigation__link <?= ($currentPage == 'login.php') ? $activeClass : '' ?>" title="login" aria-label="login">
                                Login
                                <svg class="login-icon">
                                    <use href="/mysite-mysql/assets/front-icons/symbol-defs.svg#icon-user-tie"></use>   
                                </svg>
                            </a>
                        </li>
                    <?php endif; ?>
                    <!-- If user is logged do not show login page -->
                    <?php if (isset($_SESSION["username"])) : ?>
                        <li class="navigation__item">
                            <a href="/mysite-mysql/cms_admin/dashboard.php" class="navigation__login-link" title="dashboard" aria-label="dashboard">
                                <svg class="logged-in-icon">
                                    <use href="/mysite-mysql/assets/front-icons/symbol-user-login.svg#icon-user-circle"></use>
                                </svg>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main>
