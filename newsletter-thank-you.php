<?php
/* Menu, functions, database */
require "includes/header.php";
/* Head title, page title, head description */
pageMetaData("You are subscribed!", "This page is for thanks when user is subscribed on my newsletter form." );
/* Header with background image */
require "includes/main.php";
?>
<!-- Thanks image -->
   <div class="container">
      <img src="uploads/thank-you.jpg" alt="Thank you image" title="Thank you subscriber" class="thx-img">
   </div>
<!-- Footer menu -->
<?php
require "includes/footer.php";
?>