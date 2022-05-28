<!--Nowell Reyes - 20658133 - Thursday 9am Online Tutorial  -->
<!--sourced from TWA lecture slides by Paul Davies -->
<?php
   // ensure the page is not cached - sourced from TWA lecture slides by Paul Davies
   require_once("nocache.php");

   // get access to the session variables
   session_start();

   // If the retricted access variable has been flagged, do not destroy the session variable
   if (!isset($_SESSION["error"])){
        session_destroy();
   }


   // Redirect the user to the starting page (login.php)
   header("location: index1.php");
?>