<?php
/* Include database */
require __DIR__ . "/../../includes/mysql-database.php";
/* Include functions */
require "functions.php";
ob_start();
/* Start session */
session_start();
$user_session = session_id();

/* Set active links for sidebar navigation */
$activeClass = "admin_sidebar-nav__item--active";
$pageUrl = basename($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/mysite-mysql/cms_admin/css/summernote-bs4.css">
    <link rel="stylesheet" href="/mysite-mysql/cms_admin/css/bootstrap.css">
    <link rel="stylesheet" href="/mysite-mysql/cms_admin/sass/main.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rubik:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="icon" href="../../img/favicon.png">
    <title>Cms Admin</title>
</head>
<body>
    <header class="admin_header">
        <!-- Header left side navigation -->
        <nav class="admin_header__nav-left">
            <ul>
                <li>
                    <a href="/mysite-mysql/index.php" class="admin_header__nav-left__link" aria-label="dashboard">
                        <svg class="admin_header__nav-left__icons">
                            <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-home"></use>
                        </svg>
                        <span class="admin_header__nav-left__span">Website</span>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- Header right side navigation-->
        <nav class="admin_header__nav-right">
            <ul class="admin_header__nav-right__list">
                <!-- Greeting User by username dynamic mode -->
                <?php if (isset($_SESSION["role"])) : ?>
                    <li class="admin_header__nav-right__item">
                        <!-- Username -->
                        <a href="" class="admin_header__nav-right__link" aria-label="user profile" title="user">
                            <span>Hello <?= ucfirst($_SESSION["username"]) ?></span>
                            <svg class="admin_header__nav-right__icons">
                                <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-user-tie"></use>
                            </svg>
                        </a>
                        <!-- Sub menu for username to logout -->
                        <ul class="admin_header__nav-right__sub-list">
                            <li>
                                <a href="/mysite-mysql/logout.php" class="admin_header__nav-right__link" aria-label="logout" title="logout">
                                    <span>Logout</span>
                                    <svg class="admin_header__nav-right__icons">
                                        <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-log-out"></use>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Sidebar Navigation -->
    <div class="content">
        <aside class="admin_sidebar-nav">
            <nav>
                <ul>
                    <!-- User Profile visible for subscribers -->
                    <?php if ($_SESSION["role"] === "subscriber") : ?>
                        <li class="admin_sidebar-nav__item <?= ($pageUrl === 'profile.php') ? $activeClass : ''; ?>">
                            <a href="/mysite-mysql/cms_admin/profile.php" class="admin_sidebar-nav__link" aria-label="profile" title="profile">
                                <svg class="admin_sidebar-nav__icons">
                                    <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-gauge"></use>
                                </svg>
                                <span class="admin_sidebar-nav__span">Profile</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Dashboard visible for admin -->
                    <?php if ($_SESSION["role"] === "admin") : ?>
                        <li class="admin_sidebar-nav__item <?= ($pageUrl === 'dashboard.php') ? $activeClass : ''; ?>">
                            <a href="/mysite-mysql/cms_admin/dashboard.php" class="admin_sidebar-nav__link" aria-label="dashboard" title="dashboard">
                                <svg class="admin_sidebar-nav__icons">
                                    <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-gauge"></use>
                                </svg>
                                <span class="admin_sidebar-nav__span">Dashboard</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Services (admin and subscribers) -->
                    <li class="admin_sidebar-nav__item <?= ($pageUrl === 'services.php') ? $activeClass : ''; ?>">
                        <a href="/mysite-mysql/cms_admin/services.php" class="admin_sidebar-nav__link" aria-label="Services" title="Services">
                            <svg class="admin_sidebar-nav__icons">
                                <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-tools"></use>
                            </svg>
                            <span class="admin_sidebar-nav__span">Services</span>
                        </a>
                    </li>

                    <!-- Articles (admin and subscribers) -->
                    <li class="admin_sidebar-nav__item <?= ($pageUrl === 'articles.php') ? $activeClass : ''; ?>">
                        <a href="/mysite-mysql/cms_admin/articles.php" class="admin_sidebar-nav__link" aria-label="Articles" title="Articles">
                            <svg class="admin_sidebar-nav__icons">
                                <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-new-message"></use>
                            </svg>
                            <span class="admin_sidebar-nav__span">Articles</span>
                        </a>
                    </li>

                    <!-- Categories (admin only) -->
                    <?php if ($_SESSION["role"] === "admin") : ?>
                        <li class="admin_sidebar-nav__item <?= ($pageUrl === 'categories.php') ? $activeClass : ''; ?>">
                            <a href="/mysite-mysql/cms_admin/categories.php" class="admin_sidebar-nav__link" aria-label="Categories" title="Categories">
                                <svg class="admin_sidebar-nav__icons">
                                    <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-archive"></use>
                                </svg>
                                <span class="admin_sidebar-nav__span">Categories</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Users (admin only) -->
                    <?php if ($_SESSION["role"] === "admin") : ?>
                        <li class="admin_sidebar-nav__item <?= ($pageUrl === 'users.php') ? $activeClass : ''; ?>">
                            <a href="/mysite-mysql/cms_admin/users.php" class="admin_sidebar-nav__link" aria-label="Users" title="Users">
                                <svg class="admin_sidebar-nav__icons">
                                    <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-users"></use>
                                </svg>
                                <span class="admin_sidebar-nav__span">Users</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Newsletter (admin only) -->
                    <?php if ($_SESSION["role"] === "admin") : ?>
                        <li class="admin_sidebar-nav__item <?= ($pageUrl === 'subscribers.php') ? $activeClass : ''; ?>">
                            <a href="/mysite-mysql/cms_admin/subscribers.php" class="admin_sidebar-nav__link" aria-label="Newsletter" title="Newsletter">
                                <svg class="admin_sidebar-nav__icons">
                                    <use href="/mysite-mysql/assets/back-icons/symbol-newsletter.svg#icon-email"></use>
                                </svg>
                                <span class="admin_sidebar-nav__span">Newsletter</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Messages (admin only) -->
                    <?php if ($_SESSION["role"] === "admin") : ?>
                        <li class="admin_sidebar-nav__item <?= ($pageUrl === 'messages.php') ? $activeClass : ''; ?>">
                            <a href="/mysite-mysql/cms_admin/messages.php" class="admin_sidebar-nav__link" aria-label="Messages" title="Messages">
                                <svg class="admin_sidebar-nav__icons">
                                    <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-mail"></use>
                                </svg>
                                <span class="admin_sidebar-nav__span">Messages</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- Validated Css image from W3C -->
            <div>
            <a href="https://jigsaw.w3.org/css-validator/check/referer" title="W3C css validator" aria-label="W3C css validator">
            <img class="admin_sidebar-nav__css-validator"
                src="https://jigsaw.w3.org/css-validator/images/vcss"
                alt="CSS Valido!" title="Css Validated">
            </a>           
            </div>
        </aside>
        <main>