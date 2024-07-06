<?php
/* Include menu, functions and database */
require "includes/header.php";
/* Initialize variables */
$pageTitle = $pageDescription = $articleDate = $articleAuthor = $headTitle =
    $headDescription = $articleImage = $articleContent = $articleTags = "";

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
            /*head title and description for the page */
            pageMetaData($pageTitle,$pageDescription);
            $articleDate = date("Y-m-d H:i", strtotime($articleId["published_at"]));
            $articleAuthor = $articleId['author'];
            $articleImage = $articleId['imageurl'];
            $articleContent = $articleId['content'];
            $articleTags = $articleId['tags'];
        }
    }    
}
?>
<!-- Primary section -->
<section class="primary-section">
    <div class="container">
        <!-- Page title -->
        <h1 class="primary-heading"><?= $pageTitle ?></h1>
    </div>
</section>
<!-- Page section -->
<section class="page-section">
    <!-- Page container -->
    <div class="page-container">
        <div class="grid-page">
            <article class="article">
                <?php if (isset($articleId) && $articleId != null) : ?>
                    <!-- For now this part is only for the articles because i need of dynamic data
                for the name of the article the date and the author -->
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
</section>
<!-- Footer menu-->
<?php
require "includes/footer.php";
?>