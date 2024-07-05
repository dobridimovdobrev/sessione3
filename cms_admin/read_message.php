<?php
require "includes/admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

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

/* I set a variable message query to avoid getting error of undefinded variable */
$messageQuery = null;
if (isset($_GET["id"])) {
    /*  type casting (int) for integer and security against sql inj attacks */
    $messageId = (int)$_GET["id"];

    if (!$messageId) {
        die("<h1 class='echo-errors'>No message found</h1>");
    } else {
        $messageSql = "SELECT * FROM messages WHERE id = $messageId";
        $messageQuery = mysqli_query($con_db, $messageSql);
    }

    if (!$messageQuery) {
        die("Message query failed: " . mysqli_error($con_db));
    } 
}

?>

<div class="container">
    <div class="admin-page">
    <div class="admin-page__box">
        <h1 class="admin-page__title">Inbox</h1>
        <?php if (isset($messageId)): ?>
            <a href="javascript:void(0);" onclick="showDeleteModal(<?= $messageId ?>, 'read_message.php')" class="admin-page__crud-link">Delete</a>
        <?php endif; ?>
    </div>
    </div>
    <!-- Message  -->
    <div class="inbox">
        <?php if ($messages = mysqli_fetch_assoc($messageQuery)) :
        $messageId = $messages["message"];
        $subject = $messages["subject"];
        $name = $messages["name"];
        $email = $messages["email"];
        $content = $messages["message"];
        $date = $messages["date"];
        ?>
            <!-- Date -->
            <div class="read-message">
                <span class="form-group__label">Date:</span>
                <p class="paragraph"><?= $date ?></p>
            </div>
            <!-- Name -->
            <div class="read-message">
                <span class="form-group__label">From:</span>
                <p class="paragraph"><?= $name ?></p>
            </div>
            <!-- Subject -->
            <div class="read-message">
                <span class="form-group__label">Subject:</span>
                <p class="paragraph"><?= $subject ?></p>
            </div>

            <!-- Email -->
            <div class="read-message">
                <span class="form-group__label">Email:</span>
                <p class="paragraph"><?= $email ?></p>
            </div>
            <!-- Message -->
            <div class="read-message">
                <span class="form-group__label">Message:</span>
                <p class="paragraph"><?= $content ?></p>
            </div>
            <?php endif; ?>
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