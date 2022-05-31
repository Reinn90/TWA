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
// Select employee details that belong to the logged-in supervisor
$sql = "SELECT employee_id, surname, firstname, supervisor_id ";
$sql .= "FROM employee ";
$sql .= "WHERE supervisor_id = '$userLevel' ";
$sql .= "ORDER BY surname ";

//Query the database
$rs = $dbConn->query($sql)
    or die('Problem with query' . $dbConn->error);

// check recordset, if no record found, it means a non-supervisor has tried to access the page
// redirect to logoff

if (!$rs->num_rows) {
    // Create a session variable for the index.php error message
    $_SESSION["error"] = "Error. Supervisor access only.";

    //close the database before the redirect
    $dbConn->close();

    header("location: logoff.php");
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

        <!-- Initial page form - selecting staff and date -->
        <div class="review-form-container">
            <form id="newReviewId" onsubmit="return validateForm(this);" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

                <!-- staff selection -->
                <label for "stafflist">Choose staff: </label>
                <select name="stafflist" id="stafflist" size="1">
                    <option value="">Please select a staff.</option>

                    <!-- fill select box with supervisor's direct reports -->
                    <?php foreach ($rs as $row) : ?>
                        <option value="<?php echo $row["employee_id"]; ?>"><?php echo $row["surname"] . ", " . $row["firstname"]; ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="error" id="staff-error-msg"></span>
                    
                <!-- date input -->
                <label for="reviewDateCreation">Enter year of review: </label>
                <input type="text" maxlength="4" size="4" name="reviewDateCreation" id="reviewDateCreation" placeholder="yyyy" onblur="validateNumber(this, getElementById('date-error-msg') );">
                <span class="error" id="date-error-msg"></span>

                <input type="submit" name="createReview" id="createReview" value="Create Review">
            </form>

            <?php if(isset($_POST["createReview"])): ?>
            <?php endif; ?>

        </div>
    </div>

</body>

</html>