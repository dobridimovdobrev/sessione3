<?php
/* Header with navigation and database included */
require "includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

$usersPerPage = 10; // Number of user per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Initialize search-related variables
$search = '';
$usersQuery = null;

// Check if the search form was submitted
if (isset($_POST["submit"])) {
    $search = $_POST["search"];
    $usersSql = "SELECT * FROM users WHERE username LIKE '%$search%'";
    $usersQuery = mysqli_query($con_db, $usersSql);

    if (!$usersQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }
}

// Use the function to get paginated users if no search query is active
if (!$usersQuery) {
    $usersPagination = pagination($con_db, 'users', $currentPage, $usersPerPage, 'user_date', 'user_id DESC');
    $users = $usersPagination['data'];
    $totalPages = $usersPagination['totalPages'];
} else {
    // When there's a search query, paginate the search results
    $totalRecords = mysqli_num_rows($usersQuery);
    $totalPages = ceil($totalRecords / $usersPerPage);
    $offset = ($currentPage - 1) * $usersPerPage;
    $usersSql = "SELECT * FROM users WHERE username LIKE '%$search%' LIMIT $offset, $usersPerPage";
    $usersQuery = mysqli_query($con_db, $usersSql);

    if (!$usersQuery) {
        die("Query failed: " . mysqli_error($con_db));
    }

    $users = mysqli_fetch_all($usersQuery, MYSQLI_ASSOC);
}

// Handle user deletion
deleteQuery($con_db, 'users', 'user_id', 'users.php');
?>
<!-- Container -->
<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">All Users</h1>
            <a href="includes/add_user.php" class="admin-page__crud-link">Add New User</a>
        </div>
        <!-- Search form -->
        <div>
            <form action="users.php" class="search-form" method="post">
                <input type="search" name="search" id="search" class="search-form__input" placeholder="Search...">
                <button class="search-form__button" type="submit" name="submit">
                    <svg class="search-form__search-icon-magnifying-glass">
                        <use href="../assets/front-icons/symbol-defs.svg#icon-magnifying-glass"></use>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    <!-- Default table -->
    <div class="default-table">
        <table class="default-table__table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registration on</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- For each loop asign user variables -->
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $user) :
                        $user_id = $user["user_id"];
                        $username = $user["username"];
                        $user_firstname = $user["user_firstname"];
                        $user_lastname = $user["user_lastname"];
                        $user_email = $user["user_email"];
                        $user_role = $user["user_role"];
                        $user_date = $user["user_date"];
                    ?>
                        <!-- Displaying data from DB -->
                        <tr>
                            <td><?= $username ?></td>
                            <td><?= $user_firstname ?></td>
                            <td><?= $user_lastname ?></td>
                            <td><?= $user_email ?></td>
                            <td><?= $user_role ?></td>
                            <td><?= $user_date ?></td>
                            <!-- Action butons edit and delete -->
                            <td>
                                <a href="includes/edit_user.php?edit=<?= $user_id ?>" class="default">Edit</a>
                                <a href="javascript:void(0);" onclick="showDeleteModal(<?= $user_id ?>, 'users.php')" class="delete">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <!-- If not user found -->
                    <tr>
                        <td colspan="9">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <span class="close"></span>
        <p class="confirmDeleteparagraph">Are you sure you want to delete this user?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>
<!-- Footer -->
<?php
require "includes/admin_footer.php";
?>