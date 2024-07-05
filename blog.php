<?php
$headTitle = $pageTitle = "Blog Articles";
$headDescription = "Full stack web developer. Greetings! I'm Dobri Dobrev, a passionate and innovative web developer with a knack for turning ideas into digital reality. Let me take you on a journey through my professional story.";
require "includes/header.php";


/* Default section with the image after navigation */
require "includes/main.php";

// Include the functions file
require "includes/functions.php";

$articlesPerPage = 12; // Number of articles per page
/*  type casting (int) for integer and security against sql inj attacks */
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Use the function to get paginated articles
$result = getPaginatedArticles($con_db, $currentPage, $articlesPerPage);
$articles = $result['articles'];
$totalPages = $result['totalPages'];
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
          <img src="uploads/<?= htmlspecialchars($articleImage) ?>" alt="<?= htmlspecialchars($articleTitle) ?>" class="blog-imgs">
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
            <p class="paragraf-description"><?= substr($articleDescription, 0, 176) . '...'?></p>
            <a href="article.php?id=<?= htmlspecialchars($articleId) ?>" class="read-more" aria-label="Read more" title="Read more">Read More</a>
          </div>
        </article>
    <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
    <?php if ($currentPage > 1): ?>
      <a href="?page=<?= $currentPage - 1 ?>" class="prev">&laquo; Previous</a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>" class="<?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($currentPage < $totalPages): ?>
      <a href="?page=<?= $currentPage + 1 ?>" class="next">Next &raquo;</a>
    <?php endif; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<?php
require "includes/footer.php";
?>
