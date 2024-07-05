<?php
require "includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

$usersSql = "SELECT * FROM users ORDER BY user_id DESC";
$usersQuery = mysqli_query($con_db, $usersSql);

if (!$usersQuery) {
    die("User query failed" . mysqli_error($con_db));
} else {
    $users = mysqli_fetch_all($usersQuery, MYSQLI_ASSOC);
}

$usersPerPage = 3; // Number of user per page
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

// Use the function to get paginated articles if no search query is active
if (!$usersQuery) {
    $result = getPaginatedUsers($con_db, $currentPage, $usersPerPage);
    $users = $result['users'];
    $totalPages = $result['totalPages'];
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
if (isset($_GET["delete"])) {
    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
        die("You do not have permission to delete users.");
    } else {
        $delete_user = mysqli_real_escape_string($con_db, $_GET["delete"]);
        $deleteUserSql = "DELETE FROM users WHERE user_id = $delete_user";
        $deleteUserQuery = mysqli_query($con_db, $deleteUserSql);
        if (!$deleteUserQuery) {
            die("Delete user query failed: " . mysqli_error($con_db));
        } else {
            header("Location: users.php");
            exit();
        }
    }
}
?>


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

    <div class="default-table">
        <table class="default-table__table">
            <thead>
                <tr>
                    <!-- <th>Id</th> -->
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

                <?php foreach ($users as $user) :
                    $user_id = $user["user_id"];
                    $username = $user["username"];
                    $user_firstname = $user["user_firstname"];
                    $user_lastname = $user["user_lastname"];
                    $user_email = $user["user_email"];
                    $user_role = $user["user_role"];
                    $user_date = $user["user_date"];
                ?>

                    <tr>
                        <td><?= $username ?></td>
                        <td><?= $user_firstname ?></td>
                        <td><?= $user_lastname ?></td>
                        <td><?= $user_email ?></td>
                        <td><?= $user_role ?></td>
                        <td><?= $user_date ?></td>
                        <td>
                            <a href="includes/edit_user.php?edit=<?= $user_id ?>" class="default">Edit</a>
                            <a href="javascript:void(0);" onclick="showDeleteModal(<?= $user_id ?>, 'users.php')" class="delete">Delete</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
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
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close"></span>
        <p class="confirmDeleteparagraph">Are you sure you want to delete this user?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>
<?php
require "includes/admin_footer.php";
?>