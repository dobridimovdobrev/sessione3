<?php
/* Header with navigation and database included */
require "includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

$messagesPerPage = 10; // Number of user per page
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$messagesQuery = null;
// Use the function to get paginated messages if no search query is active
if (!$messagesQuery) {
    $messagesPagination = pagination($con_db, 'messages', $currentPage, $messagesPerPage, 'date', ' id DESC');
    $messages = $messagesPagination['data'];
    $totalPages = $messagesPagination['totalPages'];
} else {
    // When there's a search query, paginate the search results
    $totalRecords = mysqli_num_rows($messagesQuery);
    $totalPages = ceil($totalRecords / $messagesPerPage);
    $offset = ($currentPage - 1) * $messagesPerPage;

    $messages = mysqli_fetch_all($usersQuery, MYSQLI_ASSOC);
}

// Delete Query 
deleteQuery($con_db, 'messages', 'id', 'messages.php');
?>


<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">Messages</h1>
            <!-- <a href="includes/add_user.php" class="admin-page__crud-link">New message</a> -->
        </div>
    </div>
    <!--Default table  -->
    <div class="default-table">
        <table class="default-table__table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- For each loop messages -->
                <?php foreach ($messages as $messageData) :
                    $id = $messageData["id"];
                    $subject = $messageData["subject"];
                    $name = $messageData["name"];
                    $email = $messageData["email"];
                    $message = $messageData["message"];
                    $date =  $messageData["date"];
                ?>
                    <tr>
                        <td><?= $subject ?></td>
                        <td><?= $name ?></td>
                        <td><?= $email ?></td>
                        <td><?= substr($message, 0, 80) . "..." ?></td>
                        <td><?= date("Y-m-d H:i", strtotime($date)) ?></td>
                        <!-- Action buttons -->
                        <td>
                            <a href="read_message.php?id=<?= $id ?>" class="default">Read</a>
                            <a href="javascript:void(0);" onclick="showDeleteModal(<?= $id ?>, 'messages.php')" class="delete">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
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
        <p class="confirmDeleteparagraph">Are you sure you want to delete this message?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>
<!-- Footer -->
<?php
require "includes/admin_footer.php";
?>