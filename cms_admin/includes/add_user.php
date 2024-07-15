<?php
require "admin_header.php";
/* If access denied if user is not an admin */
checkAdminAccess();
// Initialize variables
$username = $first_name = $last_name = $user_email = $reg_date = $password = $repeatPassword = $role = "";
$usernameError = $passwordError = $repeatPasswordError = $firstnameError = $lastnameError = $emailError = $regDateError = "";
$registrationFailed = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {

    // Sanitize and retrieve form data
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
    /* Validate first name */
    if (empty($first_name)) {
        $firstnameError = "First name is required.";
    }
    /* Validate last name */
    if (empty($last_name)) {
        $lastnameError = "Last name is required.";
    }
    /* Validate email */
    if (empty($user_email)) {
        $emailError = "Email is required.";
        /* Validate email format */
    } elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Email is not valid.";
    }
    /* Validate registration date */
    if (empty($reg_date)) {
        $regDateError = "Registration date is required.";
    }
    /* Validate password */
    if (empty($password)) {
        $passwordError = "Password is required.";
    }
    /* Validate repeat password */
    if (empty($repeatPassword)) {
        $repeatPasswordError = "Repeat password is required.";
        /* Validate password match */
    } elseif ($password !== $repeatPassword) {
        $repeatPasswordError = "Passwords do not match.";
    }

    // Check if there are no validation errors and proceed with inserting new user
    if (
        empty($usernameError) && empty($firstnameError) && empty($lastnameError) && empty($emailError) &&
        empty($regDateError) && empty($passwordError) && empty($repeatPasswordError)
    ) {

        // Prepare SQL statement for inserting user data
        $newUserSql = "INSERT INTO users (username, user_firstname, user_lastname, user_email, user_date, password, user_role)
                         VALUES (?, ?, ?, ?, ?, ?, ?)";

        $newUserStmt = mysqli_prepare($con_db, $newUserSql);
        /* Ckeck for stmt errors */
        if (!errorsQuery($newUserStmt)) {
            // Crypt the password if stmt
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);

            // Bind parameters and execute 
            mysqli_stmt_bind_param($newUserStmt, 'sssssss', $username, $first_name, $last_name, $user_email, $reg_date, $hashedPassword, $role);
            $user_execute = mysqli_stmt_execute($newUserStmt);
        }
        /* Check for execute errors */
        if (!$user_execute) {
            die("Execute statement failed: " . mysqli_stmt_error($newUserStmt));
        }

        mysqli_stmt_close($newUserStmt); // Close statement after execution
        $userId = mysqli_insert_id($con_db);
        /* Redirect to service page after successful insert */
        header("Location: ../users.php");
        exit();
        /* Showing error if form not filled correctly */
    } else {
        $registrationFailed = "Please fill out the form correctly.";
    }
}
?>
<!-- HTML Form -->
<div class="container">
    <div class="admin-page">
        <div class="admin-page__box">
            <h1 class="admin-page__title">New User</h1>
            <span class="form-group__error"><?= $registrationFailed ?></span>
        </div>
    </div>
    <!-- User form -->
    <form id="userForm" class="max-width-50" action="add_user.php" method="post" enctype="multipart/form-data">
        <!-- Username -->
        <div class="form-group">
            <label class="form-group__label" for="username">Username</label>
            <input class="form-group__input" type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>">
            <span id="usernameError" class="form-group__error"><?= $usernameError ?></span>
        </div>
        <!-- First name -->
        <div class="form-group">
            <label class="form-group__label" for="first_name">First Name</label>
            <input class="form-group__input" type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
            <span id="firstNameError" class="form-group__error"><?= $firstnameError ?></span>
        </div>
        <!-- Last name -->
        <div class="form-group">
            <label class="form-group__label" for="last_name">Last Name</label>
            <input class="form-group__input" type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
            <span id="lastNameError" class="form-group__error"><?= $lastnameError ?></span>
        </div>
        <!-- User email -->
        <div class="form-group">
            <label class="form-group__label" for="email">Email</label>
            <input class="form-group__input" type="email" id="email" name="email" value="<?= htmlspecialchars($user_email) ?>">
            <span id="emailError" class="form-group__error"><?= $emailError ?></span>
        </div>
        <!-- Registration Date -->
        <div class="form-group">
            <label class="form-group__label" for="date">Registration Date</label>
            <input class="form-group__input" type="datetime-local" id="date" name="date" value="<?= htmlspecialchars($reg_date) ?>">
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
            <label class="form-group__label" for="repeat_password">Repeat Password</label>
            <input class="form-group__input" type="password" id="repeat_password" name="repeat_password">
            <span id="repeatPasswordError" class="form-group__error"><?= $repeatPasswordError ?></span>
        </div>
        <!-- Select Role -->
        <div class="form-group">
            <label class="form-group__label" for="role">Role</label>
            <select class="form-group__input" name="role" id="role">
                <option value="subscriber" <?= $role === 'subscriber' ? 'selected' : '' ?>>Subscriber</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor</option>
            </select>
        </div>
        <!-- Submit Button -->
        <input class="form-group__btn-form" type="submit" name="submit" value="Add User">
    </form>
</div>
<!-- Footer -->
<?php
require "admin_footer.php";
?>