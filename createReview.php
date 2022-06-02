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


// create value variables for staff selection box and date so 1st form postback retains information
$staff = "";
$date = "";
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
                <select name="stafflist" id="stafflist" size="1" onchange="clearErrorMsg(this, getElementById('staff-error-msg') );">
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
        </div>

        <?php if (isset($_POST["createReview"])) :

            // Open connection to the database
            require_once("conn.php");

            // sanitise user input
            $employee = $dbConn->escape_string($_POST["stafflist"]); // Employee ID
            $date = $dbConn->escape_string($_POST["reviewDateCreation"]); // year of review

            // hide the first form
            echo "<script>
                document.getElementById('newReviewId').style.display = 'none'
                </script>";

            // Query the database to retrieve employee information
            $sql = "SELECT employee_id, surname, firstname, job_title, department_name ";

            // combine employee table with job table to retrieve job name info
            $sql .= "FROM employee INNER JOIN job ON employee.job_id = job.job_id ";

            // combine with department table to retrive department name info
            $sql .= "INNER JOIN department ON employee.department_id = department.department_id ";

            // information matching the selected employee from the form
            $sql .= "WHERE employee_id = '$employee' ";


            //Query the database
            $rs = $dbConn->query($sql)
                or die('Problem with query' . $dbConn->error);

        ?>
            <div class="review-form-container">
                <form id="newReviewId2" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateRatingsForm(this);">
                    <!-- Since employee selection comes from the database, there's no need to check for recordset -->

                    <!-- Employee information -->

                    <h4>Employee Details</h4>
                    <table>
                        <tr>
                            <th>Employee ID</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Review Year</th>
                        </tr>
                        <tr>
                            <?php foreach ($rs as $row) : ?>
                                <td><?php echo $row["employee_id"]; ?></td>
                                <td><?php echo $row["surname"]; ?></td>
                                <td><?php echo $row["firstname"]; ?></td>
                                <td><?php echo $row["job_title"]; ?></td>
                                <td><?php echo $row["department_name"]; ?></td>
                                <td><?php echo $date; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </table>

                    <!-- Editable ratings form -->
                    <h4>Ratings Information</h4>
                    <table>
                        <!-- Job Knowledge, Work
Quality, Initiative, Communication, Dependability. -->
                        <tr>
                            <th><label for="jobKnow">Job Knowledge</label></th>
                            <th><label for="workQ">Work Quality</label></th>
                            <th><label for="init">Initiative</label></th>
                            <th><label for="comms">Communication</label></th>
                            <th><label for="depend">Dependability</label></th>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" maxlength="1" name="jobKnow" id="jobKnow" placeholder="Rate between 1-5" onblur="validateRatings(this, getElementById('job-error-msg') );">
                                <small class="error" id="job-error-msg"></small>
                            </td>
                            <td>
                                <input type="text" maxlength="1" name="workQ" id="workQ" placeholder="Rate between 1-5" onblur="validateRatings(this, getElementById('workQ-error-msg') );">
                                <small class="error" id="workQ-error-msg"></small>
                            </td>
                            <td>
                                <input type="text" maxlength="1" name="init" id="init" placeholder="Rate between 1-5" onblur="validateRatings(this, getElementById('init-error-msg') );">
                                <small class="error" id="init-error-msg"></small>
                            </td>
                            <td>
                                <input type="text" maxlength="1" name="comms" id="comms" placeholder="Rate between 1-5" onblur="validateRatings(this, getElementById('comms-error-msg') );">
                                <small class="error" id="comms-error-msg"></small>
                            </td>
                            <td>
                                <input type="text" maxlength="1" name="depend" id="depend" placeholder="Rate between 1-5" onblur="validateRatings(this, getElementById('depend-error-msg') );">
                                <small class="error" id="depend-error-msg"></small>
                            </td>

                        </tr>
                    </table>
                    <div id="comments-box">
                        <label for="addComments">Additional comments:</label>
                        <textarea name="addComments" id="addComments" placeholder="Optional"></textarea>
                    </div>

                    <div class="review-form-container">
                        <input type="checkbox" name="reviewComplete" id="reviewComplete">
                        <label for="reviewComplete"> Review Complete? </label>
                        <input type="submit" name="saveReview" id="saveReview" value="Save Review">
                    </div>
                </form>

                <?php if ( isset($_POST["saveReview"]) ):

                    //retrieve user input from 2nd form
                    $job = $dbConn->escape_string($_POST['jobKnow']);
                    $workQ = $dbConn->escape_string($_POST['workQ']);
                    $init = $dbConn->escape_string($_POST['init']);
                    $comms = $dbConn->escape_string($_POST['comms']);
                    $depend = $dbConn->escape_string($_POST['depend']);

                   
                ?>

                <p><?php echo $job; ?></p>
                <?php endif; ?>


            </div>

        <?php $dbConn->close(); //close the database
    endif; ?>

    </div>

</body>

</html>