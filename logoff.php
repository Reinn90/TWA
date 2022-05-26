<?php
   // ensure the page is not cached
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