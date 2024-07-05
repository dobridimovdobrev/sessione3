<?php
$headTitle = "Contact Us";
$headDescription = "In this page you will find contact form and my contact details, please feel free to contact me.";

require "includes/header.php";
$pageTitle = "Contact Us"; // Title for the contact page
require "includes/main.php";

$subject = $name = $email = $message = "";
$subjectError = $nameError = $emailError = $messageError = "";


// Check if the form is submited
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {


  // Validate subject
  if (empty($_POST["subject"])) {
    $subjectError = "Subject is required";
  } else {
    $subject = trim(mysqli_real_escape_string($con_db, $_POST["subject"]));
  }
  // Validate name
  if (empty($_POST["name"])) {
    $nameError = "Name is required";
  } else {
    $name = trim(mysqli_real_escape_string($con_db, $_POST["name"]));
  }

  // Validate email
  if (empty($_POST["email"])) {
    $emailError = "Email is required";
  } else {
    $email = trim(mysqli_real_escape_string($con_db, $_POST["email"]));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailError = "Invalid email format";
    }
  }

  // Validate message
  if (empty($_POST["message"])) {
    $messageError = "Message is required";
  } else {
    $message = trim(mysqli_real_escape_string($con_db, $_POST["message"]));
  }

  if (empty($subjectError) && empty($nameError) && empty($emailError) && empty($messageError)) {

    $sql = "INSERT INTO messages (subject, name, email, message , date)
              VALUES (?,?,?,?,NOW()) ";

    $prepare_stmt = mysqli_prepare($con_db, $sql);
    /* Handling prepare statement errors */
    if (!$prepare_stmt) {
      die("Message failed" . mysqli_error($con_db));
    } else {
      /* Executing */
      mysqli_stmt_bind_param($prepare_stmt, "ssss", $subject, $name, $email, $message);
      $executeMessage = mysqli_stmt_execute($prepare_stmt);
      /* Handle executing errors */
      if (!$executeMessage) {
        die("Executing message failed" . mysqli_stmt_error($prepare_stmt));
      } else {
        mysqli_insert_id($con_db);
        mysqli_stmt_close($prepare_stmt);
        // Reindirizza alla pagina di ringraziamento o altra destinazione
        header("Location: message-thank-you.php");
        exit();
      }
    }
  }
}
?>
<section class="contact-section">
  <div class="container">

    <!-- Contact form -->
    <div class="form-box">

    <form id="contactForm" action="contacts.php" class="form" method="post">
    <div class="marbot-5">
        <h2 class="secondary-heading">Get In Touch</h2>
    </div>
    <div class="form__group">
        <input type="text" class="form__input" placeholder="Subject" id="contactSubject" name="subject" value="<?= htmlspecialchars($subject) ?>">
        <label for="subject" class="form__label">Subject</label>
        <span id="contactSubjectError" class="form__error"><?= htmlspecialchars($subjectError) ?></span>
    </div>
    <div class="form__group">
        <input type="text" class="form__input" placeholder="Full name" id="contactName" name="name" value="<?= htmlspecialchars($name) ?>">
        <label for="name" class="form__label">Full name</label>
        <span id="contactNameError" class="form__error"><?= htmlspecialchars($nameError) ?></span>
    </div>
    <div class="form__group">
        <input type="email" class="form__input" placeholder="Email address" id="contactEmail" name="email" value="<?= htmlspecialchars($email) ?>">
        <label for="email" class="form__label">Email address</label>
        <span id="contactEmailError" class="form__error"><?= htmlspecialchars($emailError) ?></span>
    </div>
    <div class="form__group">
        <textarea id="contactMessage" class="form__textarea" placeholder="Message" rows="10" name="message"><?= htmlspecialchars($message) ?></textarea>
        <label for="message" class="form__label">Message</label>
        <span id="contactMessageError" class="form__error"><?= htmlspecialchars($messageError) ?></span>
    </div>
    <button type="submit" name="submit" class="btn btn--orange">Submit</button>
</form>

      <iframe width="100%" height="375" title="Google map" class="google-map" src="https://maps.google.com/maps?q=2880%20Broadway,%20New%20York&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0">
      </iframe>

    </div>

  </div>
</section>

<!-- Footer -->
<?php
require "includes/footer.php";
?>