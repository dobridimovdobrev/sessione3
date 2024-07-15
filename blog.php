<?php
/* Database */
require "includes/mysql-database.php";
/* include functions */
require "includes/functions.php";

/* Head title, Description,Keywords,Page title meta data */
pageMetaData(
  "Blog Articles",
  " This page include all article ordered by new to old with pagination, sidebar and article categories",
  "web developer blog, web development articles, programming, web design"
);

/* Include menu */
require "includes/header.php";
/* Default section with the image after navigation */
require "includes/main.php";

$articlesPerPage = 6; // Number of articles per page
/*  type casting (int) for integer and security against sql inj attacks */
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Use the function to get paginated articles
$articlesPagination = pagination($con_db, 'articles', $currentPage, $articlesPerPage, "status = 'published'", 'published_at DESC');
$articles = $articlesPagination['data'];
$totalPages = $articlesPagination['totalPages'];
?>
<!-- Section Blog -->
<section class="blog-section" id="blog">
  <div class="container">
    <span class="sub-heading">Blog</span>
    <h2 class="secondary-heading">Web Articles</h2>
  </div>
  <div class="container">
    <!-- All Articles -->
    <div class="blog-articles">
      <?php foreach ($articles as $article) :
        $articleImage = $article['imageurl'];
        $articleTitle = $article['title'];
        $articlePublishDate = date("Y-m-d", strtotime($article["published_at"]));
        $articleDescription = strip_tags($article['description']);
        $articleId = $article['id'];
      ?>
        <article class="article">
          <img src="uploads/<?= htmlspecialchars($articleImage) ?>" alt="<?= htmlspecialchars($articleTitle) ?>" title="<?= htmlspecialchars($articleTitle) ?>" class="blog-imgs">
          <div class="blog-box">
            <div class="blog-tags">
              <div>
                <span class="tag"><?= htmlspecialchars($articleTitle) ?></span>
              </div>
              <div>
                <span class="published">Published on <?= htmlspecialchars($articlePublishDate) ?></span>
              </div>
            </div>
            <h2 class="h2-center"><?= htmlspecialchars($articleTitle) ?></h2>
            <p class="paragraf-description"><?= substr($articleDescription, 0, 176) . '...' ?></p>
            <a href="article.php?id=<?= htmlspecialchars($articleId) ?>" class="read-more" aria-label="Read more" title="Read more">Read More</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <!-- Pagination -->
    <div class="pagination">
      <?php if ($currentPage > 1) : ?>
        <a href="?page=<?= $currentPage - 1 ?>" title="Previous Page" aria-label="Previous Page" class="prev">&laquo;</a>
      <?php endif; ?>

      <a href="?page=1" title="Page 1" aria-label="Page 1" class="<?= $currentPage == 1 ? 'active' : '' ?>">1</a>

      <?php if ($totalPages > 2) : ?>
        <?php if ($currentPage > 3) : ?>
          <span>...</span>
        <?php endif; ?>

        <?php for ($i = max(2, $currentPage - 1); $i <= min($totalPages - 1, $currentPage + 1); $i++) : ?>
          <a href="?page=<?= $i ?>" title="Page <?= $i ?>" aria-label="Page <?= $i ?>" class="<?= $currentPage == $i ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages - 2) : ?>
          <span>...</span>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($totalPages > 1) : ?>
        <a href="?page=<?= $totalPages ?>" title="Page <?= $totalPages ?>" aria-label="Page <?= $totalPages ?>" class="<?= $currentPage == $totalPages ? 'active' : '' ?>"><?= $totalPages ?></a>
      <?php endif; ?>

      <?php if ($currentPage < $totalPages) : ?>
        <a href="?page=<?= $currentPage + 1 ?>" title="Next Page" aria-label="Next Page" class="next">&raquo;</a>
      <?php endif; ?>
    </div>
  </div>
</section>
<!-- Footer -->
<?php
require "includes/footer.php";
?>