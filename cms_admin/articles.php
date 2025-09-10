<?php
/* Header with navigation and database included */
require_once "includes/admin_header.php";

$articlesPerPage = 10; // Number of articles per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

//  search variables
$search = '';
$articlesQuery = null;

//  user role
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// condition based on user role
$condition = $isAdmin ? "1" : "status = 'published'";

// Check if the search form was submitted
if (isset($_POST["submit"])) {
    $search = trim(mysqli_real_escape_string($con_db, $_POST["search"]));
    $condition .= ($condition ? " AND " : "") . "title LIKE '%$search%'";
    $articlesSql = "SELECT * FROM articles WHERE $condition";
    $articlesQuery = mysqli_query($con_db, $articlesSql);

    if (!$articlesQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }
}

// Use the function to get paginated articles if no search query is active
if (!$articlesQuery) {
    $articlesPagination = pagination($con_db, 'articles', $currentPage, $articlesPerPage, $condition, 'published_at DESC');
    $articles = $articlesPagination['data'];
    $totalPages = $articlesPagination['totalPages'];
} else {
    $totalRecords = mysqli_num_rows($articlesQuery);
    $totalPages = ceil($totalRecords / $articlesPerPage);
    $offset = ($currentPage - 1) * $articlesPerPage;
    $articlesSql = "SELECT * FROM articles WHERE $condition ORDER BY published_at DESC LIMIT $offset, $articlesPerPage";

    $articlesQuery = mysqli_query($con_db, $articlesSql);

    if (!$articlesQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $articles = mysqli_fetch_all($articlesQuery, MYSQLI_ASSOC);
}

/* Delete query*/
deleteQuery($con_db, 'articles', 'id', 'articles.php');
?>
<!-- Container -->
<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">Articles</h1>
            <?php if ($isAdmin) : ?>
                <a href="includes/add_article.php" class="admin-page__crud-link">Add New Article</a>
            <?php endif; ?>
        </div>
        <!-- Search form -->
        <div>
            <form action="articles.php" class="search-form" method="post">
                <input type="search" name="search" id="search" class="search-form__input" placeholder="Search...">
                <button class="search-form__button" type="submit" name="submit">
                    <svg class="search-form__search-icon-magnifying-glass">
                            <use href="/assets/front-icons/symbol-defs.svg#icon-magnifying-glass"></use>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    <!-- Table to display articles -->
    <div class="default-table">
        <table class="default-table__table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Tags</th>
                    <th>Image</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Check if articles are found -->
                <?php if (!empty($articles)) : ?>
                    <!-- For each loop to display articles -->
                    <?php foreach ($articles as $article) :
                        $articleId = $article["id"];
                        $articleTitle = $article["title"];
                        $articleCategory = $article["cat_id"];
                        $articleAuthor = $article["author"];
                        $articleTags = $article["tags"];
                        $articleImage = $article["imageurl"];
                        $articleViews = $article["views"];
                        $articleStatus = $article["status"];
                        $articleDate = date("Y-m-d H:i", strtotime($article["published_at"]));

                        // Fetch category title
                        $selectCategorySql = "SELECT cat_title FROM categories WHERE cat_id = $articleCategory";
                        $selectCategoryQuery = mysqli_query($con_db, $selectCategorySql);
                        if (!$selectCategoryQuery) {
                            die("Query for category title failed: " . mysqli_error($con_db));
                        } else {
                            $selectCategory = mysqli_fetch_assoc($selectCategoryQuery);
                            $catTitle = $selectCategory["cat_title"];
                        }
                    ?>
                        <!-- Displaying articles into the table -->
                        <tr>
                            <td><?= $articleTitle ?></td>
                            <td><?= $catTitle ?></td>
                            <td><?= $articleAuthor ?></td>
                            <td><?= substr($articleTags, 0, 70) . "..." ?></td>
                            <td><img src="/uploads/<?= $articleImage ?>" alt="<?= $articleTitle ?>" title="<?= $articleTitle ?>" class="default-table__image"></td>
                            <td><?= $articleViews ?></td>
                            <td><?= $articleStatus ?></td>
                            <td><?= $articleDate ?></td>
                            <td>
                                <a href="/article.php?id=<?= $articleId ?>" class="default" target="_blank">View</a>
                                <?php if ($isAdmin) : ?>
                                    <a href="includes/edit_article.php?edit=<?= $articleId ?>" class="default" target="_blank">Edit</a>
                                    <a href="javascript:void(0);" onclick="showDeleteModal(<?= $articleId ?>, 'articles.php')" class="delete">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9">No articles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <span class="close"></span>
        <p class="confirmDeleteparagraph">Are you sure you want to delete this article?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>
<!-- Footer -->
<?php
require "includes/admin_footer.php";
?>