<?php
require "includes/mysql-database.php";
/* Initialize variables */
$articleTitle = $articleDate = $articleAuthor = $headTitle =
    $headDescription = $articleImage = $articleContent = $articleTags = "";

if (isset($_GET["id"])) {
    /*  type casting (int) for integer and security against sql inj attacks */
    $articleId = (int)$_GET["id"];
    /* Views query */
    $viewsSql = "UPDATE articles SET views = views + 1 WHERE id = $articleId";
    $viewsQuery = mysqli_query($con_db, $viewsSql);
    if (!$viewsQuery) {
        die("Views query failed" . mysqli_error($con_db));
    }
    /* Articles query and fetch data */
    $sql = "SELECT * FROM articles WHERE id = $articleId";
    $query = mysqli_query($con_db, $sql);

    if (!$query) {
        die("Query article id failed" . mysqli_error($con_db));
    } else {
        $articleId = mysqli_fetch_assoc($query);
        if ($articleId) {
            $articleTitle = $articleId["title"];
            $articleDate = date("Y-m-d H:i", strtotime($articleId["published_at"]));
            $headTitle = $articleTitle;
            $articleAuthor = $articleId['author'];
            $articleImage = $articleId['imageurl'];
            $articleContent = $articleId['content'];
            $articleTags = $articleId['tags'];
            $headDescription = "Full stack web developer.
        Greetings! I'm Dobri Dobrev , a passionate and innovative web developer
        with a knack for turning ideas into digital reality. 
        Let me take you on a journey through my professional story.";
        }
    }
    require "includes/header.php";
} else {
    header("Location: index.php");
}
?>

<section class="primary-section">
    <div class="container">

        <h1 class="primary-heading"><?= $articleTitle ?></h1>

    </div>
</section>

<section class="page-section">

    <div class="page-container">
        <div class="grid-page">
            <article class="article">
                <?php if (isset($articleId) && $articleId != null) : ?>
                    <!-- For now this part is only for the articles because i need of dynamic data
                for the name of the article the date and the author -->

                    <div class="breadcrumb">
                        <div class="breadcrumb__heading">
                            <h2 class="fourth-heading"><?= $articleTitle ?></h2>
                        </div>
                        <div>
                            <p class="paragraph"><i>Published on <?= $articleDate ?></i></p>
                        </div>

                        <div>
                            <p class="paragraph"><i>Author: <?= $articleAuthor ?></i></p>
                        </div>

                    </div>
                    <img src="uploads/<?= $articleImage ?>" title="<?= $articleTitle ?>" alt="<?= $articleTitle ?>" class="article__image">


                    <!-- <h2 class="article-heading"></h2> -->
                    <p class="paragraph"><?= $articleContent ?></p>

                    <div class=" tags">
                        <svg class="tags__icon">
                            <use href="/mysite-mysql/assets/back-icons/symbol-tags.svg#icon-price-tag"></use>
                        </svg>
                        <p class="tags__label"> Tags: <?= $articleTags ?></p>
                    </div>
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

<!-- Footer -->

<?php
require "includes/footer.php";
?>