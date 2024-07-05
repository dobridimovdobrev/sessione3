<?php
$headTitle = $pageTitle = "Articles";
$pageDescription = "Search articles page. Here you can find all articles by typing the title into the search input";
require "includes/header.php";
require "includes/main.php";
require "includes/functions.php";


$articlesPerPage = 6; // Number of articles per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Initialize search-related variables
$search = '';
$articlesQuery = null;

// Check if the search form was submitted
if (isset($_POST["submit"])) {
    $search = $_POST["search"];
    $articlesSql = "SELECT * FROM articles WHERE title LIKE '%$search%'";
    $articlesQuery = mysqli_query($con_db, $articlesSql);

    if (!$articlesQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }
}

// Use the function to get paginated articles if no search query is active
if (!$articlesQuery) {
    $result = getPaginatedArticles($con_db, $currentPage, $articlesPerPage);
    $articles = $result['articles'];
    $totalPages = $result['totalPages'];
} else {
    // When there's a search query, paginate the search results
    $totalRecords = mysqli_num_rows($articlesQuery);
    $totalPages = ceil($totalRecords / $articlesPerPage);
    $offset = ($currentPage - 1) * $articlesPerPage;
    $articlesSql = "SELECT * FROM articles WHERE title LIKE '%$search%' AND status = 'published' LIMIT $offset, $articlesPerPage";
    $articlesQuery = mysqli_query($con_db, $articlesSql);

    if (!$articlesQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $articles = mysqli_fetch_all($articlesQuery, MYSQLI_ASSOC);
}

?>

<section class="page-section">
  <div class="page-container">
    <div class="grid-page">
      <div class="search-articles">

        <?php if (!empty($articles)) : ?>
          <?php foreach ($articles as $article) :
            $articleId = $article['id'];
            $articleTitle = $article['title'];
            $articleDescription = $article['description'];
            $articleDate = date("Y-m-d H:i", strtotime($article['published_at']));
            $articleAuthor = $article["author"];
          ?>
            <article class="article <?= $articleId ?>">
              <div class="blog-box">
                <div class="blog-tags">
                  <div>
                    <span class="tag"><?= $articleTitle ?></span>
                  </div>
                <div>
                  <span class="published">Published on <?= $articleDate ?></span>
                </div>
                  </div>
                  <h2 class="h2-center"><?= $articleTitle ?></h2>
                  <div>
                    <span class="published">Author: <?= $articleAuthor?></span>
                  </div>
                  <p class="paragraf-description"><?= $articleDescription ?></p>
                <a href="article.php?id=<?= $articleId ?>" class="read-more" title="<?= $articleTitle ?>" aria-label="<?= $articleTitle ?>">Read More</a>
              </div>
            </article>
          <?php endforeach; ?>
        <?php else : ?>
          <?= "<h1 class='fifth-heading'>Articles not found!</h1>" ?>
        <?php endif; ?>

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
      <!-- Sidebar -->
      <?php require "includes/sidebar.php"; ?>
    </div>
  </div>
</section>
<?php require "includes/footer.php"; ?>
