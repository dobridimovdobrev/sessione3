<?php
/* Header with navigation and database included */
require "includes/admin_header.php";
?>
<!--Content  -->
<div class="container">
    <?php if (isset($_SESSION["role"]) && $_SESSION['role'] === 'subscriber') : ?>
        <h1>Welcome <?= ucfirst($_SESSION["username"]) ?> </h1>
        <a href="includes/add_article.php" class="admin-page__crud-link">Delete Account</a>
        <?php else: header("Location: ../login.php") ?>
    <?php endif; ?>
</div>
<!-- Footer -->
<?php
require "includes/admin_footer.php";
?>