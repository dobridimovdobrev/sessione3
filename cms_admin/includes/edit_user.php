<?php
/* Header with navigation and database included */
require "admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();

/* Initialize variables */
$editSuccess = isset($_SESSION['editSuccess']) ? $_SESSION['editSuccess'] : '';
$repeatPasswordError = "";

/* Clear the session variable */
unset($_SESSION['editSuccess']);

/* Assign variable */
$editUserId = $_GET["edit"];

/* Check if User id exist */
if (!$editUserId) {
    die("<h1 class='echo-errors'>No User found</h1>");
}
/* Fetch User single row data from database */
$users = fetchSingleData($con_db, 'users', "user_id = $editUserId");

/* Initialize variables with existing user data */
$username = $users["username"];
$first_name = $users["user_firstname"];
$last_name = $users["user_lastname"];
$user_email = $users["user_email"];
$reg_date = $users["user_date"];
$role = $users["user_role"];

/* Initialize vaiables */
$usernameError = $passwordError = $repeatPasswordError = $firstnameError =
    $lastnameError = $emailError = $regDateError = "";
/* Update the form to the database */
if (isset($_POST["submit"])) {
    $username = trim(mysqli_real_escape_string($con_db, $_POST["username"]));
    $first_name = trim(mysqli_real_escape_string($con_db, $_POST["first_name"]));
    $last_name = trim(mysqli_real_escape_string($con_db, $_POST["last_name"]));
    $user_email = trim(mysqli_real_escape_string($con_db, $_POST["email"]));
    $reg_date = trim(mysqli_real_escape_string($con_db, $_POST["date"]));
    $password = trim(mysqli_real_escape_string($con_db, $_POST["password"]));
    $repeatPassword = trim(mysqli_real_escape_string($con_db, $_POST["repeat_password"]));
    $role = trim(mysqli_real_escape_string($con_db, $_POST["role"]));

    // Validate form fields
    if (empty($username)) {
        $usernameError = "Username is required.";
    }
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
    // Validate registration date
    if (empty($reg_date)) {
        $regDateError = "Registration date is required.";
    }
    // Validate password,repeat password,minimum chars requirments and if match
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
    // Check if there are no validation errors
    if (
        empty($usernameError) && empty($firstnameError) && empty($lastnameError) && empty($emailError) &&
        empty($regDateError) && empty($passwordError) && empty($repeatPasswordError)
    ) {
        // Prepare  update query
        $updateUsersSql = "UPDATE users SET username = ?, user_firstname = ?, user_lastname = ?, user_email = ?, user_date = ?, password = ?, user_role = ? WHERE user_id = ? ";
        $updateUsersStmt = mysqli_prepare($con_db, $updateUsersSql);
        /* Check for query errors */
        if (!errorsQuery($updateUsersStmt)) {
            //execute the statement
            mysqli_stmt_bind_param($updateUsersStmt, "sssssssi", $username, $first_name, $last_name, $user_email, $reg_date, $password, $role, $editUserId);
            $updateUserExecute = mysqli_stmt_execute($updateUsersStmt);
        }
        /* Check for execut stmt errors */
        if (!$updateUserExecute) {
            die("Execute failed" . mysqli_stmt_error($updateUsersStmt));
        } else {
            // Close statement,redirect and set success message in session 
            mysqli_stmt_close($updateUsersStmt);
            $_SESSION['editSuccess'] = "Edit user successfully !";
            header("Location: edit_user.php?edit=$editUserId");
            exit();
        }
    }
}
?>
<!-- Container -->
<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">Edit User</h1>
            <span class="form-group__success"><?= $editSuccess ?></span>
        </div>
        <!-- <a href="add_user.php" class="admin-page__crud-link">View User</a> -->
    </div>
    <!-- User form -->
    <form id="userForm" class="max-width-50" action="" method="post" enctype="multipart/form-data"> <!-- Ensure action points to the PHP file location if separate -->
        <!-- Hidden input for editUserId -->
        <input type="hidden" id="editUserId" name="editUserId" value="<?= $editUserId ?>">
        <!-- Username -->
        <div class="form-group">
            <label class="form-group__label" for="username">Username</label>
            <input class="form-group__input" type="text" id="username" name="username" value="<?= $username ?>">
            <span id="usernameError" class="form-group__error"><?= $usernameError ?></span>
        </div>
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

        <!-- Registration Date -->
        <div class="form-group">
            <label class="form-group__label" for="date">Date</label>
            <input class="form-group__input" type="date" id="date" name="date" value="<?= $reg_date ?>">
            <span id="regDateError" class="form-group__error"><?= $regDateError ?></span>
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
        <!-- Role of the User -->
        <div class="form-group">
            <label class="form-group__label" for="role">Role</label>
            <select class="form-group__input" name="role" id="role">
                <option selected><?= $role ?></option>
                <?php if ($role == 'admin') : ?>
                    <option value="editor">editor</option>
                    <option value="subscriber">subscriber</option>
                <?php elseif ($role == 'editor') : ?>
                    <option value="admin">admin</option>
                    <option value="subscriber">subscriber</option>
                <?php elseif ($role == 'subscriber') : ?>
                    <option value="admin">admin</option>
                    <option value="editor">editor</option>
                <?php endif; ?>
            </select>
        </div>
        <!-- Submit -->
        <input class="form-group__btn-form" type="submit" name="submit" value="Update User">
    </form>
</div>
<!-- Footer -->
<?php
require "admin_footer.php";
?>