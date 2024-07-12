<?php
/* Database */
require "includes/mysql-database.php";
/* include functions */
require "includes/functions.php";
/* Initialize variables */
$pageTitle = $pageDescription = $articleDate = $articleAuthor
 = $articleImage = $articleContent = $articleTags = "";

if (isset($_GET["id"])) {
    /*  type casting (int) for integer and security against sql inj attacks */
    $articleId = (int)$_GET["id"];
    /* Views query */
    $viewsSql = "UPDATE articles SET views = views + 1 WHERE id = $articleId";
    $viewsQuery = mysqli_query($con_db, $viewsSql);
    
    /* Check for errors query */
    if (!errorsQuery($viewsQuery)) {
        /* Articles query */
        $sql = "SELECT * FROM articles WHERE id = $articleId";
        $articleQuery = mysqli_query($con_db, $sql);
    }
    
    /* Check for errors query */
    if (!errorsQuery($articleQuery)) {
        /* fetch data article from database */
        $articleId = mysqli_fetch_assoc($articleQuery);
        /* Fetch article data if exist */
        if ($articleId) {
            $pageTitle = $articleId["title"];
            $pageDescription = $articleId["description"];
            $articleTags = $articleId['tags'];
            /*head title and description,keywords for the page */
            pageMetaData($pageTitle, $pageDescription, $articleTags);
            $articleDate = date("Y-m-d H:i", strtotime($articleId["published_at"]));
            $articleAuthor = $articleId['author'];
            $articleImage = $articleId['imageurl'];
            $articleContent = $articleId['content'];
            // After fetching $articleContent from the database, clean it up
            
        }
    }

}
/* Include menu*/
require "includes/header.php";
?>
<!-- Primary section -->
<section class="primary-section">
    <div class="container">
        <!-- Page title -->
        <h1 class="primary-heading"><?= $pageTitle ?></h1>
    </div>
</section>
<!-- Page section -->
<div class="page-section">
    <!-- Page container -->
    <div class="page-container">
        <div class="grid-page">
            <article class="article">
                <?php if (isset($articleId) && $articleId != null) : ?>
                    <!-- Breadcrumb -->
                    <div class="breadcrumb">
                        <div class="breadcrumb__heading">
                            <h2 class="fourth-heading"><?= $pageTitle ?></h2>
                        </div>
                        <!-- Published date -->
                        <div>
                            <p class="paragraph"><i>Published on <?= $articleDate ?></i></p>
                        </div>
                        <!-- Author -->
                        <div>
                            <p class="paragraph"><i>Author: <?= $articleAuthor ?></i></p>
                        </div>
                    </div>
                    <!-- Image -->
                    <img src="uploads/<?= $articleImage ?>" title="<?= $pageTitle ?>" alt="<?= $pageTitle ?>" class="article__image">
                    <!-- Content -->
                    <p class="paragraph"><?= $articleContent ?></p>
                    <!-- Tags -->
                    <div class=" tags">
                        <svg class="tags__icon">
                            <use href="/mysite-mysql/assets/back-icons/symbol-tags.svg#icon-price-tag"></use>
                        </svg>
                        <p class="tags__label"> Tags: <?= $articleTags ?></p>
                    </div>
                    <!-- Redirect to blog page if aricle id is not exist -->
                <?php else : ?>
                    <?= header("Location: blog.php"); ?>
                <?php endif; ?>
            </article>
            <!-- Sidebar -->
            <?php
            require "includes/sidebar.php";
            ?>
        </div>
    </div>
</div>
<!-- Footer menu-->
<?php
require "includes/footer.php";
?>