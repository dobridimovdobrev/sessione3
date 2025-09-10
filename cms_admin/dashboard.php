<?php
/* Include menu, functions and database */
require "includes/admin_header.php";
/* Verify user ip adress */
$user_ip_adress = verify_user_ip();
?>
<!--Content  -->
<div class="container">
    <?php if ($_SESSION["role"] === 'admin') : ?>
        <h1>Welcome <?= ucfirst($_SESSION["username"])  ?> </h1>
        <?= "Your ip adress is : " . $user_ip_adress; ?>
    <?php else : header("Location: index.php") ?>
    <?php endif; ?>
    <div class="dashboard-box">
        <!-- Services -->
        <div class="dashboard service-theme">
            <div class="dashboard__top">
                <svg class="dashboard__article-icon">
                    <use href="../assets/back-icons/symbol-defs.svg#icon-tools"></use>
                </svg>
                <div class="dashboard__dynamic-data">
                    <h2 class="dashboard__heading"><?= countTable('services'); ?></h2>
                    <p class="dashboard__paragraph">Services </p>
                </div>
            </div>
            <div class="dashboard__bottom">
                <a href="services.php" class="dashboard__link">
                    <span>View Details</span>
                    <svg class="dashboard__eye-icon">
                        <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-eye"></use>
                    </svg>
                </a>
            </div>
        </div>
        <!-- Articles -->
        <div class="dashboard article-theme">
            <div class="dashboard__top">
                <svg class="dashboard__article-icon">
                    <use href="../assets/back-icons/symbol-defs.svg#icon-new-message"></use>
                </svg>
                <div class="dashboard__dynamic-data">
                    <h2 class="dashboard__heading"><?= countTable('articles'); ?></h2>
                    <p class="dashboard__paragraph">Articles</p>
                </div>
            </div>
            <div class="dashboard__bottom">
                <a href="articles.php" class="dashboard__link">
                    <span>View Details</span>
                    <svg class="dashboard__eye-icon">
                        <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-eye"></use>
                    </svg>
                </a>
            </div>
        </div>
        <!-- Users -->
        <div class="dashboard user-theme">
            <div class="dashboard__top">
                <svg class="dashboard__article-icon">
                    <use href="../assets/back-icons/symbol-defs.svg#icon-users"></use>
                </svg>
                <div class="dashboard__dynamic-data">
                    <h2 class="dashboard__heading"><?= countTable('users'); ?></h2>
                    <p class="dashboard__paragraph">Users</p>
                </div>
            </div>
            <div class="dashboard__bottom">
                <a href="users.php" class="dashboard__link">
                    <span>View Details</span>
                    <svg class="dashboard__eye-icon">
                        <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-eye"></use>
                    </svg>
                </a>
            </div>
        </div>
        <!-- Newsletter -->
        <div class="dashboard newsletter-theme">
            <div class="dashboard__top">
                <svg class="dashboard__article-icon">
                    <use href="../assets/back-icons/symbol-newsletter.svg#icon-email"></use>
                </svg>
                <div class="dashboard__dynamic-data">
                    <h2 class="dashboard__heading"><?= countTable('subscribers'); ?></h2>
                    <p class="dashboard__paragraph">Newsletter</p>
                </div>
            </div>
            <div class="dashboard__bottom">
                <a href="subscribers.php" class="dashboard__link">
                    <span>View Details</span>
                    <svg class="dashboard__eye-icon">
                        <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-eye"></use>
                    </svg>
                </a>
            </div>
        </div>
        <!-- Messages -->
        <div class="dashboard message-theme">
            <div class="dashboard__top">
                <svg class="dashboard__article-icon">
                    <use href="../assets/back-icons/symbol-defs.svg#icon-mail"></use>
                </svg>
                <div class="dashboard__dynamic-data">
                    <h2 class="dashboard__heading"><?= countTable('messages'); ?></h2>
                    <p class="dashboard__paragraph">Messages</p>
                </div>
            </div>
            <div class="dashboard__bottom">
                <a href="messages.php" class="dashboard__link">
                    <span>View Details</span>
                    <svg class="dashboard__eye-icon">
                        <use href="/mysite-mysql/assets/back-icons/symbol-defs.svg#icon-eye"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<?php
require "includes/admin_footer.php";
?>