<?php
/* Include menu, functions and database */
require "includes/header.php";
/*head title and description for the page */
pageMetaData(
    "Registration",
    "Full stack web developer.
     Greetings! I'm Dobri Dobrev, a passionate and innovative web developer
     with a knack for turning ideas into digital reality. 
     Let me take you on a journey through my professional story."
);
/* Default section with the image after navigation  */
require "includes/main.php";

// Check if the form is submitted
$username = $first_name = $last_name = $user_email = $password = $repeatPassword = $terms = "";
$usernameError = $passwordError = $repeatPasswordError = $firstnameError = $lastnameError = $emailError = $termsError = "";
$registrationFailed = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
    // trim inputs
    $username = trim(mysqli_real_escape_string($con_db, $_POST["username"]));
    $first_name = trim(mysqli_real_escape_string($con_db, $_POST["first_name"]));
    $last_name = trim(mysqli_real_escape_string($con_db, $_POST["last_name"]));
    $user_email = trim(mysqli_real_escape_string($con_db, $_POST["email"]));
    $password = trim(mysqli_real_escape_string($con_db, $_POST["password"]));
    $repeatPassword = trim(mysqli_real_escape_string($con_db, $_POST["repeat_password"]));
    $terms = isset($_POST["terms"]);
    $role = "subscriber";

    // Validate username
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
    /* Validate password */
    if (empty($password)) {
        $passwordError = "Password is required.";
    }
    /*  Validate repeat password */
    if (empty($repeatPassword)) {
        $repeatPasswordError = "Repeat the password.";
    /* Validate password if match with repeat password */
    } elseif ($password !== $repeatPassword) {
        $repeatPasswordError = "Passwords do not match.";
    }
    /* Validate terms and conditions */
    if(empty($terms)){
        $termsError = "You must agree to the terms and conditions.";
    }
    /* If no form errors can proceed with inserting data into the database */
    if (empty($usernameError) && empty($firstnameError) && empty($lastnameError) && empty($emailError) && empty($passwordError) && empty($repeatPasswordError) && empty($termsError)) {
        // Prepared statement to insert user
        $newUserSql = "INSERT INTO users (username, user_firstname, user_lastname, user_email, password, user_role, user_date)
                       VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $newUserStmt = mysqli_prepare($con_db, $newUserSql);
        /* Check for errors */
        if (!errorsQuery($newUserStmt)) {
            /* If no statement errors password will be crypt */
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
            mysqli_stmt_bind_param($newUserStmt, 'ssssss', $username, $first_name, $last_name, $user_email, $hashedPassword, $role);
            $user_execute = mysqli_stmt_execute($newUserStmt);    
        } 
        
        /* Check for execute stmt errors */
        if (!$user_execute) {
            die("Error inserting user: " . mysqli_stmt_error($newUserStmt));
        } else {
            /* If no errors proceed iìwith inserting new id into the database, closing stmt and redirect to login page */
            $userId = mysqli_insert_id($con_db);
            mysqli_stmt_close($newUserStmt);
            header("Location: login.php");
            exit();
        }
        /* Error message if form is not filled correctly */
    } else {
        $registrationFailed = "Please fill out the form correctly.";
    }
}
?>
<!-- Register section -->
<section class="register-section">
    <div class="container">
        <div class="register-form-box">
            <!-- Register form -->
            <form id="registerForm" class="register-form" action="register.php" method="post">
                <div class="marbot-5">
                    <span class="login-form__error"><?= $registrationFailed ?></span>
                    <h2 class="login-register-heading">Create an account</h2>
                </div>
                <!-- Username -->
                <div class="register-form__group">
                    <label class="register-form__label" for="username">Username</label>
                    <input class="register-form__input" type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>">
                    <span id="registrationUsernameError" class="form__error"><?= $usernameError ?></span>
                </div>
                <!-- First name -->
                <div class="register-form__group">
                    <label class="register-form__label" for="first_name">First Name</label>
                    <input class="register-form__input" type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>">
                    <span id="registrationFirstNameError" class="form__error"><?= $firstnameError ?></span>
                </div>
                <!-- Last name -->
                <div class="register-form__group">
                    <label class="register-form__label" for="last_name">Last Name</label>
                    <input class="register-form__input" type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>">
                    <span id="registrationLastNameError" class="form__error"><?= $lastnameError ?></span>
                </div>
                <!-- User email -->
                <div class="register-form__group">
                    <label class="register-form__label" for="email">Email</label>
                    <input class="register-form__input" type="email" id="email" name="email" value="<?= htmlspecialchars($user_email) ?>">
                    <span id="registrationEmailError" class="form__error"><?= $emailError ?></span>
                </div>
                <!-- Password -->
                <div class="register-form__group">
                    <label class="register-form__label" for="password">Password</label>
                    <input class="register-form__input" type="password" id="password" name="password">
                    <span id="registrationPasswordError" class="form__error"><?= $passwordError ?></span>
                </div>
                <!-- Repeat Password -->
                <div class="register-form__group">
                    <label class="register-form__label" for="repeat_password">Repeat Password</label>
                    <input class="register-form__input" type="password" id="repeat_password" name="repeat_password">
                    <span id="registrationRepeatPasswordError" class="form__error"><?= $repeatPasswordError ?></span>
                </div>
                <!-- Privacy and Terms -->
                <div class="register-form__check">
                    <input class="register-form__input" type="checkbox" id="terms" name="terms">
                    <label class="register-form__label" for="terms">I agree to the <a href="terms.php" target="_blank"> Terms and Conditions</a></label>
                </div>
                <!-- If not checked terms and conditions showing error -->
                <span id="registrationTermsError" class="form__error"><?= $termsError ?></span>
                <button class="register-form__btn btn--orange" type="submit" name="submit">Register</button>
            </form>
        </div>
    </div>
</section>
<!-- Footer menu -->
<?php
require "includes/footer.php";
?>
