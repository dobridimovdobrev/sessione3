<?php
/* Database info */
$db_host = "localhost";
$db_user = "dobri2";
$db_pass = "Dobri123!";
$db_name = "cms";
// connecting to mysql database. The ordrer for host,name, password and database name is !important
$con_db = mysqli_connect($db_host , $db_user , $db_pass ,$db_name ); 

 
// check control for errors with the connection
if (mysqli_connect_error()) {
    die("Connection failed " . mysqli_connect_error($con_db)); 
    exit();
}/* else{
    echo " you are connected";
}
 */
?>