<?php
require_once "includes/admin_header.php";
// Include the functions file
require_once "includes/functions.php";

$articlesPerPage = 10; // Number of articles per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Initialize search-related variables
$search = '';
$articlesQuery = null;

// Check if the search form was submitted
if (isset($_POST["submit"])) {
    $search = trim(mysqli_real_escape_string($con_db, $_POST["search"]));
    $articlesSql = "SELECT * FROM articles WHERE title LIKE '%$search%' AND status = 'published'";
    $articlesQuery = mysqli_query($con_db, $articlesSql);

    if (!$articlesQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }
}

// Use the function to get paginated articles if no search query is active
if (!$articlesQuery) {
    $articlesPagination = pagination($con_db, 'articles', $currentPage, $articlesPerPage, "status = 'published'", 'published_at DESC');
    $articles = $articlesPagination['data'];
    $totalPages = $articlesPagination['totalPages'];
} else {
    $totalRecords = mysqli_num_rows($articlesQuery);
    $totalPages = ceil($totalRecords / $articlesPerPage);
    $offset = ($currentPage - 1) * $articlesPerPage;
    $articlesSql = "SELECT * FROM articles WHERE title LIKE '%$search%' AND status = 'published' ORDER BY published_at DESC LIMIT $offset, $articlesPerPage";
    $articlesQuery = mysqli_query($con_db, $articlesSql);

    if (!$articlesQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $articles = mysqli_fetch_all($articlesQuery, MYSQLI_ASSOC);
}

/* Delete query*/
deleteQuery($con_db, 'articles', 'id', 'articles.php');
?>


<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">Articles</h1>
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <a href="includes/add_article.php" class="admin-page__crud-link">Add New Article</a>
            <?php endif; ?>
        </div>
        <!-- Search form -->
        <div>
            <form action="articles.php" class="search-form" method="post">
                <input type="search" name="search" id="search" class="search-form__input" placeholder="Search...">
                <button class="search-form__button" type="submit" name="submit">
                    <svg class="search-form__search-icon-magnifying-glass">
                        <use href="../assets/front-icons/symbol-defs.svg#icon-magnifying-glass"></use>
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

                        // I want to see my articles if draft, but subscribers shouldn't see articles if draft

                        $articleId = $article["id"];
                        $articleTitle = $article["title"];
                        $articleCategory = $article["cat_id"];
                        $articleAuthor = $article["author"];
                        $articleTags = $article["tags"];
                        $articleImage = $article["imageurl"];
                        $articleComments = $article["comment_count"]; //future update
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
                            <td><?= htmlspecialchars($articleTitle) ?></td>
                            <td><?= htmlspecialchars($catTitle) ?></td>
                            <td><?= htmlspecialchars($articleAuthor) ?></td>
                            <td><?= htmlspecialchars(substr($articleTags, 0, 70)) . "..." ?></td>
                            <td><img src="../uploads/<?= htmlspecialchars($articleImage) ?>" alt="<?= htmlspecialchars($articleTitle) ?>" width="100" height="60"></td>
                            <td><?= htmlspecialchars($articleViews) ?></td>
                            <td><?= htmlspecialchars($articleStatus) ?></td>
                            <td><?= htmlspecialchars($articleDate) ?></td>
                            <td>
                                <a href="../article.php?id=<?= $articleId ?>" class="default" target="_blank">View</a>
                                <?php if ($_SESSION['role'] === 'admin') : ?>
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
                <a href="?page=<?= $currentPage - 1 ?>" class="prev">&laquo; Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages) : ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="next">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p class="confirmDeleteparagraph">Are you sure you want to delete this article?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>

<?php
require "includes/admin_footer.php";
?>
