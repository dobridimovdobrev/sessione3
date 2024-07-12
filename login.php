<?php
/* Database */
require "includes/mysql-database.php";
/* include functions */
require "includes/functions.php";

/*head title ,keywords and description for the page */
pageMetaData(
  "Login", 
  "In this page you will find contact form and my contact details, please feel free to contact me.", 
  "login,user account,profile,backend access"
);

/* Menu */
require "includes/header.php";
/* Background image and title */
require "includes/main.php";

// Check if the form is submitted
$usernameError = "";
$passwordError = "";
$loginFailed = "";

if (isset($_POST["submit"])) {
  
  $username = $_POST["username"];
  $password = $_POST["password"];
  
  // Validate username
  if (empty($username)) {
    $usernameError = "Username is required";
  } else {
    $username = mysqli_real_escape_string($con_db, $username);
  }
  
  // Validate password
  if (empty($password)) {
    $passwordError = "Password is required";
  } else {
    $password = mysqli_real_escape_string($con_db, $password);
  }
  /* If no validating errors proceed with database query */
  if (empty($usernameError) && empty($passwordError)) {
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $query = mysqli_query($con_db, $sql);
    /* Fetch data if no query errors */
    if (!errorsQuery($query)) {
      if ($row = mysqli_fetch_assoc($query)) {
        $db_username = $row["username"];
        $db_password = $row["password"];
        $db_firstname = $row["user_firstname"];
        $db_lastname = $row["user_lastname"];
        $db_email = $row["user_email"];
        $db_role = $row["user_role"];
        
        /* If user password is verifyed user can login */
        if (password_verify($password, $db_password)) {
          $_SESSION["username"] = $db_username;
          $_SESSION["firstname"] = $db_firstname;
          $_SESSION["lastname"] = $db_lastname;
          $_SESSION["role"] = $db_role;
          
          /* Redirecting on specified page base on user role */
          if($_SESSION['role'] === 'admin'){
            header("Location: /mysite-mysql/cms_admin/dashboard.php");
            exit();
            /* Subscribers are redirected to profile page */
          }else{
            header("Location: /mysite-mysql/cms_admin/index.php");
            exit();
          }
          
        } else {
          $loginFailed = "Username or Password is incorrect!";
        }
      } else {
        $loginFailed = "Username or Password is incorrect!";
      }
    }
  }
}
?>
<!-- Login section -->
<section class="login-section">
  <div class="container">
    <div class="login-form-box">
      <!-- Contact form -->
      <form id="loginForm" action="login.php" class="login-form" method="post">
        <div class="marbot-5">
          <!-- Form title -->
          <span id="loginFailedError" class="login-form__error"><?= $loginFailed ?></span>
          <h2 class="login-register-heading">Welcome!</h2>
        </div>
        <!-- Username -->
        <div class="login-form__group">
          <input autocomplete="off" type="text" class="login-form__input" placeholder="Username" id="username" name="username">
          <label for="name" class="login-form__label">Username</label>
          <span id="loginUsernameError" class="login-form__error"><?= $usernameError ?></span>
        </div>
        <!-- Password -->
        <div class="login-form__group">
          <input autocomplete="off" type="password" class="login-form__input" placeholder="Password" id="password" name="password">
          <label for="password" class="login-form__label">Password</label>
          <span id="loginPasswordError" class="login-form__error"><?= $passwordError ?></span>
        </div>
        <!-- Login button -->
        <button type="submit" name="submit" class="login-form__btn btn--orange">LOGIN</button>
        <div class="login-form__sign-up-here">
          <!-- Create account link if not registered -->
          <p class="paragraph">Not Registered?</p>
          <a href="register.php" class="login-form__sign-up-link">Create an account</a>
        </div>
      </form>
      <!-- Image -->
      <img src="img/login.png" alt="Login form image" class="login-form-box__img" title="Login form image">
    </div>
  </div>
</section>
<!-- Footer menu -->
<?php
require "includes/footer.php";
?>