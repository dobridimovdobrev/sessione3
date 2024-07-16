<?php
/* Header with navigation and database included */
require "includes/admin_header.php";
$editSuccess = isset($_SESSION['editSuccess']) ? $_SESSION['editSuccess'] : '';

/* Initialize variables */
$username = $first_name = $last_name = $user_email = "";
$firstnameError = $lastnameError = $emailError = $passwordError = $repeatPasswordError = "";

/* Clear the session variable */
unset($_SESSION['editSuccess']);

/* Assign variable */
if (isset($_SESSION['role']) && $_SESSION['role'] === 'subscriber') {
    $editUserId = $_SESSION['id'];

    /* Fetch User single row data from database */
    $user = fetchSingleData($con_db, 'users', "user_id = '$editUserId' ");
    /* Initialize variables with existing user data */
    $username = $user["username"];
    $first_name = $user["user_firstname"];
    $last_name = $user["user_lastname"];
    $user_email = $user["user_email"];
}


/* Update the form to the database */
if (isset($_POST["submit"])) {
    $first_name = trim(mysqli_real_escape_string($con_db, $_POST["first_name"]));
    $last_name = trim(mysqli_real_escape_string($con_db, $_POST["last_name"]));
    $user_email = trim(mysqli_real_escape_string($con_db, $_POST["email"]));
    $password = trim(mysqli_real_escape_string($con_db, $_POST["password"]));
    $repeatPassword = trim(mysqli_real_escape_string($con_db, $_POST["repeat_password"]));

    // Validate first name
    if (empty($first_name)) {
        $firstnameError = "First name is required.";
    }
    // Validate last name
    if (empty($last_name)) {
        $lastnameError = "Last name is required.";
    }
    // Validate email and format
    if (empty($user_email)) {
        $emailError = "Email is required.";
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Email is not valid.";
    }
    // Validate password, repeat password, minimum chars requirements and if match
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $passwordError = "The password must be at least 8 characters.";
        } else {
            if ($password !== $repeatPassword) {
                $repeatPasswordError = "Passwords do not match.";
            } else {
                // Hash the password
                $password = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
            }
        }
    }
    // Check for no validation errors
    if (
        empty($firstnameError) && empty($lastnameError) && empty($emailError)
        && empty($passwordError) && empty($repeatPasswordError)
    ) {
        // Prepare update query
        if (empty($password)) {
            // Update without changing the password
            $updateUsersSql = "UPDATE users SET user_firstname = ?, user_lastname = ?, user_email = ? WHERE user_id = ?";
            $updateUsersStmt = mysqli_prepare($con_db, $updateUsersSql);
            mysqli_stmt_bind_param($updateUsersStmt, "sssi", $first_name, $last_name, $user_email, $editUserId);
        } else {
            // Update if new password
            $updateUsersSql = "UPDATE users SET user_firstname = ?, user_lastname = ?, user_email = ?, password = ? WHERE user_id = ?";
            $updateUsersStmt = mysqli_prepare($con_db, $updateUsersSql);
            mysqli_stmt_bind_param($updateUsersStmt, "ssssi", $first_name, $last_name, $user_email, $password, $editUserId);
        }

        /* Check for query errors */
        if (!errorsQuery($updateUsersStmt)) {
            $updateUserExecute = mysqli_stmt_execute($updateUsersStmt);
        }

        /* Check for execution errors */
        if (!$updateUserExecute) {
            die("Execute failed: " . mysqli_stmt_error($updateUsersStmt));
        } else {
            // Close statement, redirect, and set success message in session 
            mysqli_stmt_close($updateUsersStmt);
            $_SESSION['editSuccess'] = "Edit profile successfully!";
            header("Location: profile.php?id=$editUserId");
            exit();
        }
    }
}
/* Delete query */
if (isset($_GET["delete"]) ) {
    $deleteId = $_SESSION['id'];
    $deleteId = mysqli_real_escape_string($con_db, $_GET["delete"]);
    $deleteSql = "DELETE FROM users WHERE user_id = '$deleteId'";
    $deleteQuery = mysqli_query($con_db, $deleteSql);
    /* Check for query errors */
    if (!errorsQuery($deleteQuery)) {
        session_destroy();
        header("Location: /mysite-mysql/index.php");
        exit();
    } 
}
?>

<!-- Content -->
<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <?php if (isset($_SESSION["role"]) && $_SESSION['role'] === 'subscriber') : ?>
                <h1>Welcome <?= ucfirst($_SESSION["username"]) ?></h1>
            <?php endif; ?>
            <?php if (isset($editUserId)) : ?>
                <a href="javascript:void(0);" onclick="showDeleteModal(<?= $editUserId ?>, 'profile.php')" class="admin-page__crud-link">Delete Account</a>
            <?php endif; ?>
            <span class="form-group__success"><?= $editSuccess ?></span>
        </div>
    </div>
    <!-- Edit profile form -->
    <form id="profileForm" class="max-width-50" method="post">
        <!-- Hidden input for editUserId -->
        <input type="hidden" id="editUserId" name="editUserId" value="<?= $editUserId ?>">
        <!-- First Name -->
        <div class="form-group">
            <label class="form-group__label" for="first_name">First Name</label>
            <input class="form-group__input" type="text" id="first_name" name="first_name" value="<?= $first_name ?>">
            <span id="firstNameError" class="form-group__error"><?= $firstnameError ?></span>
        </div>
        <!-- Last Name -->
        <div class="form-group">
            <label class="form-group__label" for="last_name">Last Name</label>
            <input class="form-group__input" type="text" id="last_name" name="last_name" value="<?= $last_name ?>">
            <span id="lastNameError" class="form-group__error"><?= $lastnameError ?></span>
        </div>
        <!-- Email -->
        <div class="form-group">
            <label class="form-group__label" for="email">Email</label>
            <input class="form-group__input" type="email" id="email" name="email" value="<?= $user_email ?>">
            <span id="emailError" class="form-group__error"><?= $emailError ?></span>
        </div>
        <!-- Password -->
        <div class="form-group">
            <label class="form-group__label" for="password">Password</label>
            <input class="form-group__input" type="password" id="password" name="password">
            <span id="passwordError" class="form-group__error"><?= $passwordError ?></span>
        </div>
        <!-- Repeat password -->
        <div class="form-group">
            <label class="form-group__label" for="repeat_password">Repeat password</label>
            <input class="form-group__input" type="password" id="repeat_password" name="repeat_password">
            <span id="repeatPasswordError" class="form-group__error"><?= $repeatPasswordError ?></span>
        </div>
        <!-- Submit -->
        <input class="form-group__btn-form" type="submit" name="submit" value="Update Profile">
    </form>
</div>
<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <span class="close"></span>
        <p class="confirmDeleteparagraph">Are you sure you want to delete this account?</p>
        <button id="cancelBtn" class="btn">Cancel</button>
        <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
    </div>
</div>
<!-- Footer -->
<?php
require "includes/admin_footer.php";
?>