<!--Nowell Reyes - 20658133 - Thursday 9am Online Tutorial  -->

<?php
// ensure the page is not cached - sourced from TWA lecture slides by Paul Davies
require_once("nocache.php");

// get access to the session variables
session_start();

// check if the user is logged in 
if (!isset($_SESSION["who"])) {

    // Create a session variable that identifies that the user is NOT logged in

    $_SESSION["error"] = "Error. Accessed restricted page. Please log in.";

    header("location: logoff.php");
}

// retrieve session variables
$userName = $_SESSION['who'];     //name of the employee
$userLevel = $_SESSION['level'];  // Employee id



// get Server date
$serverDate = date("Y-m-d");

// Connect to the database to retrieve staff information and review elements
require_once("conn.php");


// Supervisor performance review database query
// Retrieve Employee’s Surname, Employee’s Firstname, year of review, review id, employee id, completed status, date completed and accepted status
$sql = "SELECT surname, firstname, review_year, review_id, employee.employee_id, completed, date_completed, supervisor_id, accepted ";
$sql .= "FROM review INNER JOIN employee ";
$sql .= "ON review.employee_id = employee.employee_id ";
$sql .= "WHERE employee.supervisor_id = 'DM001' ";
$sql .= "ORDER BY review_year DESC ";

//Query the database
$rs = $dbConn->query($sql)
    or die('Problem with query' . $dbConn->error);



// If the logged in user is not a supervisor, redirect to login page
foreach ($rs as $row) {

    if ($row["supervisor_id"] != $userLevel) {
        
        // Create a session variable for the index.php error message
        $_SESSION["error"] = "Error. Supervisor access only.";

        //close the database before the redirect
        $dbConn->close();

        header("location: logoff.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/projectMaster.css">
    <script src="javascript/projectScript.js" defer></script>
    <title>Create Review</title>
</head>

<body>
  

    <div class="container">

        <div>
            <h2>DUNDER MIFFLIN <small>inc.</small></h2>
        </div>
        <!-- Navigation bar -->
        <!-- Display current user and server date -->
        <div class="navigation">
            <ul>
                <li>
                    <?php echo "Hi $userName "; ?>
                    <a href="logoff.php">Log Off</a>
                </li>
                <li>Server date: <?php echo $serverDate; ?>
                    <a href="chooseReview.php">Go back</a>
                </li>
            </ul>
        </div>

        <div class="form-container">
            <form id="newReviewId" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <select name="stafflist" id="stafflist" size="1" required>
                    <option value="">Please select a staff.</option>

                </select>

                <input type="submit" name="submit" value="Save review">
            </form>

        </div>
    </div>

</body>

</html>