<?php
require "includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

$usersSql = "SELECT * FROM messages ORDER BY id DESC";
$usersQuery = mysqli_query($con_db, $usersSql);

if (!$usersQuery) {
    die("Message query failed" . mysqli_error($con_db));
} else {
    $messages = mysqli_fetch_all($usersQuery, MYSQLI_ASSOC);
}

// Handle user deletion
if (isset($_GET["delete"])) {
    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
        die("You do not have permission to delete messages.");
    } else {
        $delete_message = mysqli_real_escape_string($con_db, $_GET["delete"]);
        $deleteMessageSql = "DELETE FROM messages WHERE id = $delete_message";
        $deleteMessageQuery = mysqli_query($con_db, $deleteMessageSql);
        if (!$deleteMessageQuery) {
            die("Delete message query failed: " . mysqli_error($con_db));
        } else {
            header("Location: messages.php");
            exit();
        }
    }
}
?>


<div class="container">
    <div class="admin-page">
    <div class="admin-page__box">
        <h1 class="admin-page__title">Messages</h1>
        <a href="includes/add_user.php" class="admin-page__crud-link">New message</a>
    </div>
    </div>

    <div class="default-table">
        <table class="default-table__table">
            <thead>
                <tr>
                    <!-- <th>Id</th> -->
                    <th>Subject</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

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
                        <td><?= substr($message, 0 , 80) . "..." ?></td>
                        <td><?= date("Y-m-d H:i", strtotime($date)) ?></td>
                        <td>
                            <a href="read_message.php?id=<?= $id ?>" class="default">Read</a>
                            <a href="javascript:void(0);" onclick="showDeleteModal(<?= $id ?>, 'messages.php')" class="delete">Delete</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close"></span>
            <p class="confirmDeleteparagraph">Are you sure you want to delete this message?</p>
            <button id="cancelBtn" class="btn">Cancel</button>
            <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
        </div>
    </div>
<?php
require "includes/admin_footer.php";
?>