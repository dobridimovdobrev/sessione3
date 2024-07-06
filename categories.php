<?php
/* Menu with database,functions and session included */
require "includes/header.php";
/* Head title, Description,Page title meta data */
pageMetaData("Categories", "All categories that are connected with all blog articles.");
/* Default section with the image after navigation  */
require "includes/main.php";

$articlesPerPage = 6; // Number of articles per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Initialize variables
$articles = [];
$totalPages = 1;

// Check if a category is selected
if (isset($_GET["id"])) {
  /*  type casting (int) for integer and security against sql inj attacks */
    $categoryId = (int)$_GET["id"];
    $offset = ($currentPage - 1) * $articlesPerPage;

    // Query to count total articles in the selected category
    $totalRecordsSql = "SELECT COUNT(*) FROM articles WHERE cat_id = $categoryId AND status = 'published' " ;
    $totalRecordsResult = mysqli_query($con_db, $totalRecordsSql);
    /* If no errors query */
    if (!errorsQuery($totalRecordsResult)) {
      $totalRecordsRow = mysqli_fetch_array($totalRecordsResult);
      $totalRecords = $totalRecordsRow[0];
      $totalPages = ceil($totalRecords / $articlesPerPage);
    }

    // Query to fetch paginated articles in the selected category
    $articlesSql = "SELECT * FROM articles WHERE cat_id = $categoryId AND status = 'published' LIMIT $offset, $articlesPerPage";
    $articlesQuery = mysqli_query($con_db, $articlesSql);
    /* If no errors query */
    if (!errorsQuery($articlesQuery)) {
      $articles = mysqli_fetch_all($articlesQuery, MYSQLI_ASSOC);
    }
}
?>
<!-- Page section -->
<section class="page-section">
  <div class="page-container">
    <div class="grid-page">
      <div class="search-articles">
        <!-- Display articles if not empty with foreach loop -->
        <?php if (!empty($articles)) : ?>
          <?php foreach ($articles as $article) :
            $articleId = $article['id'];
            $articleTitle = $article['title'];
            $articleDescription = $article['description'];
            $articleDate = date("Y-m-d", strtotime($article['published_at']));
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
                  <span class="published">Author: <?= $articleAuthor ?></span>
                </div>
                <p class="paragraf-description"><?= $articleDescription ?></p>
                <a href="article.php?id=<?= $articleId ?>" class="read-more" title="<?= $articleTitle ?>" aria-label="<?= $articleTitle ?>">Read More</a>
              </div>
            </article>
          <?php endforeach; ?>
          <!-- Show message if no articles -->
        <?php else : ?>
          <h1 class='fifth-heading'>Articles not found!</h1>
        <?php endif; ?>
        <!-- Pagination -->
        <div class="pagination">
          <?php if ($currentPage > 1) : ?>
            <a href="?id=<?= $categoryId ?>&page=<?= $currentPage - 1 ?>" class="prev">&laquo; Previous</a>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <a href="?id=<?= $categoryId ?>&page=<?= $i ?>" class="<?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($currentPage < $totalPages) : ?>
            <a href="?id=<?= $categoryId ?>&page=<?= $currentPage + 1 ?>" class="next">Next &raquo;</a>
          <?php endif; ?>
        </div>
      </div>
      <!-- Sidebar -->
      <?php require "includes/sidebar.php"; ?>
    </div>
  </div>
</section>
<!-- Footer menu -->
<?php
require "includes/footer.php";
?>
