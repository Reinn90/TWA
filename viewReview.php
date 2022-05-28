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
// Employee information section
$sql1 = "SELECT review.employee_id, surname, firstname, review_year, ";

// Ratings information section
$sql1 .= "job_knowledge, work_quality, initiative, communication, dependability, ";

// Evaluation section
$sql1 .= "additional_comment, date_completed, accepted ";

// Inner join employee and review tables
$sql1 .= "FROM review INNER JOIN employee ON employee.employee_id = review.employee_id ";

// Data to match the hyperlink
$sql1 .= "WHERE review_id = '$review' ";

// Query the database
$rs1 = $dbConn->query($sql1)
    or die('Problem with query' . $dbConn->error);

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
            <h3>Reviews Details</h3>
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
                <table>
                    <tr>Review completed: <?php echo $row["date_completed"];?></tr>
                    <tr><td>Comments: <?php echo $row["additional_comment"];?></td></tr>
                </table>
            </div>
        </div>







    </div>

    <!-- Close the connection to the database -->
    <?php $dbConn->close(); ?>

</body>

</html>