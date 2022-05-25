<?php 
// Ensure the page is not cached
require_once("nocache.php");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href = "css/projectMaster.css">
        <script src="javascript/projectScript.js" defer></script>
        <title>Dunder Mifflin Performance Review Login</title>
    </head>
    <body>
    <?php
    
        // Initialise error messages
        $loginError = "";
        $error = "";
        

        // check if the form has been submitted
        if (isset($_POST["submit"])){

            //check that both input forms are filled
            if ( empty($_POST["empId"]) || empty($_POST["empPwd"])){
                $error = "<p class='error'> Both employee ID and password are required</p>";
            } 
            else {

                //Connect to the database
                require_once("conn.php");

                // Retrieve user input from the form with sanitisation
                $userId = $dbConn->escape_string($_POST["empId"]);
                $userPwd = $dbConn->escape_string($_POST["empPwd"]);
                
            
                // hash the password for database comparison
                $hashedPassword = hash('sha256', $userPwd);

                // query the database
                $sql = "SELECT employee_id, firstname ";
                $sql .= "FROM employee "; 
                $sql .= "WHERE employee_id = '$userId' ";
                $sql .= "AND password = '$hashedPassword'";

                $rs = $dbConn->query($sql);
                
                // I have added a feature
                // Validate login details against database
                if($rs->num_rows) {

                    // Start a new session for the user
                    session_start();
                    $accessdenied = "";

                    // Store the user details in session variables
                    $user = $rs->fetch_assoc();

                    $_SESSION['level'] = $user['employee_id'];
                    $_SESSION['who'] = $user['firstname'];
                    
                    // Close the connection to the database
                    $dbConn->close();
                    
                    // Redirect the user to the secure page
                    header('Location: chooseReview.php');

                } else {
                    $loginError = "<p class='error'>Invalid Username or Password</p>";
                }

                
            }

        }

    ?>

    <!-- Login form -->
    <div class="form-container">
        <div><h2>DUNDER MIFFLIN <small>inc.</small></h2></div>
       
        <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
             <!-- If other pages are accessed without logging in first, display error -->
            <p class="error"><?php echo $accessdenied;?></p>

            <!-- Login form -->
            <div class="form-control">
                <label for="empId">Employee ID</label>
                <input type="text" name="empId" id="empId" maxlength="10">
            </div>
           
            <div class="form-control">
                <label for="empPwd">Password</label>
                <input type="password" name="empPwd" id="empPwd">
            </div>
            
            <!-- php login validation error messages -->
            <?php echo $loginError; ?>
            <?php echo $error; ?>
            

            <input type="submit" name="submit" id="submit-button" value="Login">
            
        
            <small>
                <p>The Dunder Mifflin performance planning and review process is intended to assist
            supervisors to review the performance of staff annually and develop agreed performance
            plans based on workload agreements and the strategic direction of Dunder Mifflin.</p>
                <p>The Performance Planning and Review system covers both results (what was accomplished), and
            behaviours (how those results were achieved). The most important aspect is what will be
            accomplished in the future and how this will be achieved within a defined period. The process
            is continually working towards creating improved performance and behaviours that align and
            contribute to the mission and values of Dunder Mifflin.</p>
            </small>
        </form>
    
    </div>
    
  


    </body>

</html>