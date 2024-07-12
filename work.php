<?php
/* Database */
require "includes/mysql-database.php";
/* include functions */

require "includes/functions.php";
/* Initializa variables */

$pageTitle = $pageDescription = $serviceImage = $serviceContent =
    $serviceTags = $headTitle = $headDescription = "";

if (isset($_GET["id"])) {
    /*  type casting (int) for integer and security against sql inj attacks */
    $serviceId = (int)$_GET["id"];
    /* Views query */
    $viewsSql = "UPDATE services SET views = views + 1 WHERE id = $serviceId";
    $viewsQuery = mysqli_query($con_db, $viewsSql);
    /* Check views query errors */
    if (!errorsQuery($viewsQuery)) {
        /* Services query and fetch data */
        $sql = "SELECT * FROM services WHERE id = $serviceId";
        $serviceQuery = mysqli_query($con_db, $sql);
        /* Check errors for service query */
    }
    if (!errorsQuery($serviceQuery)) {
        /* Fetch service data from database */
        $serviceId = mysqli_fetch_assoc($serviceQuery);
        if ($serviceId) {
            $pageTitle = $serviceId["title"];
            $pageDescription = $serviceId["description"];
            $serviceTags = $serviceId["tags"];
            $serviceDate = $serviceId["published_at"];
            /*head title and description,keywords for the page */
            pageMetaData($pageTitle, $pageDescription, $serviceTags);
            $serviceImage = $serviceId['imageurl'];
            $serviceContent = $serviceId['content'];
        }
    }
}
/* Include menu,sessions */
require "includes/header.php";
?>
<!-- Primary section -->
<section class="primary-section">
    <div class="container">
        <h1 class="primary-heading"><?= $pageTitle ?></h1>
    </div>
</section>
<!-- Page section -->
<div class="page-section">
    <div class="page-container">
        <div class="grid-page">
            <article class="article">
                <!-- Display service from database -->
                <?php if (isset($serviceId) && $serviceId != null) : ?>
                    <!-- Breadcrumb -->
                    <div class="breadcrumb">
                        <div class="breadcrumb__heading">
                            <h2 class="fourth-heading"><?= $pageTitle ?></h2>
                        </div>
                        <!-- Published date -->
                        <div>
                            <p class="paragraph"><i>Published on <?= $serviceDate ?></i></p>
                        </div>
                    </div>
                    <img src="uploads/<?= $serviceImage ?>" alt="<?= $pageTitle ?>" title="<?= $pageTitle ?>" class="article__image">
                    <p class="paragraph"><?= $serviceContent ?></p>
                    <div class=" tags">
                        <svg class="tags__icon">
                            <use href="/mysite-mysql/assets/back-icons/symbol-tags.svg#icon-price-tag"></use>
                        </svg>
                        <p class="tags__label"> Tags: <?= $serviceTags ?></p>
                    </div>
                <?php else : ?>
                    <?= header("Location: services.php"); ?>
                <?php endif; ?>
            </article>
            <!-- Sidebar -->
            <?php
            require "includes/sidebar.php";
            ?>
        </div>
    </div>
</div>
<!-- Footer -->
<?php
require "includes/footer.php";
?>