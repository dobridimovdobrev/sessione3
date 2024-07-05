<?php
require "includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

/* Pagination and fetch data */
$subscribersPerPage = 10; // Number of user per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$subscribersQuery = null;
// Use the function to get paginated subscribers if no search query is active
if (!$subscribersQuery) {
    $subscribersPagination = pagination($con_db, 'subscribers', $currentPage, $subscribersPerPage, 'date', ' id DESC');
    $subscribers = $subscribersPagination['data'];
    $totalPages = $subscribersPagination['totalPages'];
} else {
    // When there's a search query, paginate the search results
    $totalRecords = mysqli_num_rows($subscribersQuery);
    $totalPages = ceil($totalRecords / $subscribersPerPage);
    $offset = ($currentPage - 1) * $subscribersPerPage;

    $subscribers = mysqli_fetch_all($usersQuery, MYSQLI_ASSOC);
}

// Handle user deletion
if (isset($_GET["delete"])) {
    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
        die("You do not have permission to delete subscribers.");
    } else {
        $delete_subscriber = mysqli_real_escape_string($con_db, $_GET["delete"]);
        $deleteSubscriberSql = "DELETE FROM subscribers WHERE id = $delete_subscriber";
        $deleteSubscriberQuery = mysqli_query($con_db, $deleteSubscriberSql);
        if (!$deleteSubscriberQuery) {
            die("Delete subscriber query failed: " . mysqli_error($con_db));
        } else {
            header("Location: subscribers.php");
            exit();
        }
    }
}
?>


<div class="container">
    <div class="admin-page">
        <h1 class="admin-page__title">Subscribers</h1>
    </div>

    <div class="default-table">
        <table class="default-table__table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Where did they here for us?</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($subscribers as $subscriber) :
                    $id = $subscriber["id"];
                    $name = $subscriber["name"];
                    $email = $subscriber["email"];
                    $origin = $subscriber["origin"];
                    $date =  $subscriber["date"];
                ?>

                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $email ?></td>
                        <td><?= $origin ?></td>
                        <td><?= date("Y-m-d H:i", strtotime($date)) ?></td>
                        <td>
                            <a href="javascript:void(0);" onclick="showDeleteModal(<?= $id ?>, 'subscribers.php')" class="delete">Delete</a>
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
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close"></span>
        <p class="confirmDeleteparagraph">Are you sure you want to delete this subscriber?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>
<?php
require "includes/admin_footer.php";
?>