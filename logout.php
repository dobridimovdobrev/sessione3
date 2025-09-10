<?php
/* This is for logout with redirecting after that */
session_start();
session_unset();
session_destroy();
/* Redirect */
header("Location: /index.php"); 
exit();
?>
