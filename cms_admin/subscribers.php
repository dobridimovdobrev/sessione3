<?php
require "includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

$subscriberSql = "SELECT * FROM subscribers ORDER BY id DESC";
$subscriberQuery = mysqli_query($con_db, $subscriberSql);

if (!$subscriberQuery) {
    die("Subscriber query failed" . mysqli_error($con_db));
} else {
    $subscribers = mysqli_fetch_all($subscriberQuery, MYSQLI_ASSOC);
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