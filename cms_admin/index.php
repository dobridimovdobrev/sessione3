<?php
require "includes/admin_header.php";

?>

<!--Content  -->

<div class="container">
    <?php if (isset($_SESSION["role"])) : ?>
        <h1>Welcome <?= ucfirst($_SESSION["username"]) ?> </h1>
        <?php else: header("Location: ../login.php") ?>
    <?php endif; ?>
</div>

<?php
require "includes/admin_footer.php";
?>