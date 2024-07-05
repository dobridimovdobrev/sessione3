<?php
require "includes/mysql-database.php";
/* Initializa variables */
$serviceTitle = $serviceImage = $serviceContent =
    $serviceTags = $headTitle = $headDescription = "";

if (isset($_GET["id"])) {
    /*  type casting (int) for integer and security against sql inj attacks */
    $serviceId = (int)$_GET["id"];
    /* Views query */
    $viewsSql = "UPDATE services SET views = views + 1 WHERE id = $serviceId";
    $viewsQuery = mysqli_query($con_db, $viewsSql);
    if(!$viewsQuery){
        die("Views query failed" . mysqli_error($con_db));
    }
    /* Services query and fetch data */
    $sql = "SELECT * FROM services WHERE id = $serviceId";
    $query = mysqli_query($con_db, $sql);

    if (!$query) {
        die("Query service id failed" . mysqli_error($con_db));
    } else {
        $serviceId = mysqli_fetch_assoc($query);
        if ($serviceId) {
            $serviceTitle = $serviceId["title"];
            $serviceImage = $serviceId['imageurl'];
            $serviceContent = $serviceId['content'];
            $serviceTags = $serviceId["tags"];
            $headTitle = $serviceTitle;
            $headDescription = "Full stack web developer.
        Greetings! I'm Dobri Dobrev , a passionate and innovative web developer
        with a knack for turning ideas into digital reality. 
        Let me take you on a journey through my professional story.";
        }
    }

    require "includes/header.php";
}
/* require "includes/main.php"; */
?>
<section class="primary-section">
    <div class="container">

        <h1 class="primary-heading"><?= $serviceTitle ?></h1>

    </div>
</section>
<section class="page-section">

    <div class="page-container">
        <div class="grid-page">
            <article class="article">
                <?php if (isset($serviceId) && $serviceId != null) : ?>

                    <img src="uploads/<?= $serviceImage ?>" alt="<?= $serviceTitle ?>" title="<?= $serviceTitle ?>" class="article__image">
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
</section>

<!-- Footer -->

<?php
require "includes/footer.php";
?>