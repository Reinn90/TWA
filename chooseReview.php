<?php
   // ensure the page is not cached
   require_once("nocache.php");

   // get access to the session variables
   session_start();

   // check if the user is logged in
   if (!$_SESSION["who"]){
     header("location: logoff.php");

    //  change te $accessdenied variable
    
   }

   // retrieve session variables
   $userName = $_SESSION['who'];
   $userLevel = $_SESSION['level'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href = "css/projectMaster.css">
    <script src="javascript/projectScript.js" defer></script>
    <title>Performance Review Selection</title>
</head>
<body>
    
    <div class="container">
        <div><h2>DUNDER MIFFLIN <small>inc.</small></h2></div>
        <div class="navigation">
            <ul>
                <li><?php echo "Hi $userName";?></li>
                <li><a href="logoff.php">Log Off</a></li>   
            </ul>
        </div>

        <div id="main-container">
            <div class="review-containers">
                <h3>Current Reviews</h3>
            </div>
            
            <div class="review-containers">
                <h3>Completed Reviews</h3>
            </div>
        </div>
        
            
        
    </div>
    
    
</body>
</html>