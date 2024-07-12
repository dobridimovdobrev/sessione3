<?php
/* Database */
require "includes/mysql-database.php";
/* include functions */
require "includes/functions.php";

/*head title,keywords and description for the page */
pageMetaData(
   "Message is sent!",
    "This page is for thanks when message is sent.",
   "Successfully sent message"
   );

/* Menu */
require "includes/header.php";
/* Header with background image */
require "includes/main.php";
?>
<!-- Thank you image -->
   <div class="container">
      <img src="uploads/thank-you.jpg" alt="Thank you" title= "Thank you message" class="thx-img">
   </div>
<?php
/* Footer menu */
require "includes/footer.php";

?>