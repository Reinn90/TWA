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
$serverDate = date("d-m-Y");

// Connect to the database to retrieve performance reviews
require_once("conn.php");

// retrieve the review_id from the clicked hyperlink
$review = $dbConn->escape_string($_GET["review_id"]);

// Build the SQL query
// Employee information section - also retrive supervisor id to match with the logged in supervisor
$sql1 = "SELECT review.employee_id, surname, firstname, review_year, supervisor_id, ";

// Ratings information section
$sql1 .= "job_knowledge, work_quality, initiative, communication, dependability, ";

// Evaluation section
$sql1 .= "additional_comment, date_completed, accepted ";

// Inner join employee and review tables
$sql1 .= "FROM review INNER JOIN employee ON review.employee_id = employee.employee_id ";

// Data to match the hyperlink
$sql1 .= "WHERE review_id = '$review' ";

// Query the database
$rs1 = $dbConn->query($sql1)
    or die('Problem with query' . $dbConn->error);

// If the logged in user is not the owner of the review, or not the supervisor of the employee review,
// redirect to logoff page
foreach ($rs1 as $row) {

    if (($userLevel != $row["supervisor_id"]) && ($userLevel != $row["employee_id"])) {

        // Create a session variable for the index.php error message
        $_SESSION["error"] = "Error. User did not match review owner. Please log in.";

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
    <title>Performance Review Details</title>
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

        <!-- Performance review title -->
        <div class="container-header">
            <h3>Review Details</h3>
        </div>

        <div id="review-detail">


            <div id="employee-section">
                <h4>Employee Details</h4>
                <table>
                    <tr>
                        <th>Employee ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Review Year</th>
                    </tr>
                    <tr>
                        <?php foreach ($rs1 as $row) : ?>
                            <td><?php echo $row["employee_id"]; ?></td>
                            <td><?php echo $row["firstname"]; ?></td>
                            <td><?php echo $row["surname"]; ?></td>
                            <td><?php echo $row["review_year"]; ?></td>
                        <?php endforeach; ?>
                    </tr>
                </table>
            </div>

            <div id="employee-rating">
                <h4>Evaluation</h4>

                <table>
                    <tr>
                        <th>Job Knowledge</th>
                        <th>Work Quality</th>
                        <th>Initiative</th>
                        <th>Communication</th>
                        <th>Dependability</th>
                    </tr>

                    <tr>
                        <?php foreach ($rs1 as $row) : ?>
                            <td><?php echo $row["job_knowledge"]; ?></td>
                            <td><?php echo $row["work_quality"]; ?></td>
                            <td><?php echo $row["initiative"]; ?></td>
                            <td><?php echo $row["communication"]; ?></td>
                            <td><?php echo $row["dependability"]; ?></td>
                        <?php endforeach; ?>

                    </tr>
                </table>
            </div>

            <div id="additional-comments">
                <h4>Comments</h4>
                <table id="comments-box">
                    <tr>
                        <th>Review completed: <?php echo $row["date_completed"]; ?></th>
                    </tr>
                    <tr>
                        <td>Additional Comments: <br><br> <?php echo $row["additional_comment"]; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div id="acknowledgement">
            <h3>Acknowledgement</h3>
            <form>
                <p>Thank you for taking part in your Dunder Mifflin Performance Review. This review is an important
                    aspect of the development of our organisation and its profits and of you as a valued employee.</p>
                <p><strong>By electronically signing this form, you confirm that you have discussed this review in detail
                        with your supervisor.</strong>
                    <small>The fine print: Signing this form does not necessarily indicate that you agree with this evaluation.</small>
                </p>
            </form>
        </div>







    </div>

    <!-- Close the connection to the database -->
    <?php $dbConn->close(); ?>

</body>

</html>