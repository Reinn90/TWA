<?php
// ensure the page is not cached
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

// Employee performance review database query
// To do this display the year of review and the date completed for each performance review of the logged-in employee. Display this data
// in reverse order of the year of review so that the most recent year is listed at the top
$sql1 = "SELECT review_id, review_year, date_completed, completed ";
$sql1 .= "FROM review ";
$sql1 .= "WHERE employee_id = '$userLevel' ";
$sql1 .= "ORDER BY review_year DESC ";

//Query the database
$rs1 = $dbConn->query($sql1)
    or die('Problem with query' . $dbConn->error);


// For each performance review that belongs to this logged-in supervisor display the Employee’s Surname, Employee’s Firstname,
// year of review, review id, employee id, completed status, and date completed. Each Employee’s Surname is to be a hypertext link
// to the View Performance Review page. When clicked, the link must pass the review id of the review to the View Performance Review
// page
// Supervisor performance review database query
// Employee’s Surname, Employee’s Firstname,
// year of review, review id, employee id, completed status, and date completed
// Build SQL query
$sql2 = "SELECT surname, firstname, review_year, review_id, employee.employee_id, completed, date_completed, supervisor_id ";
$sql2 .= "FROM review INNER JOIN employee ";
$sql2 .= "ON review.employee_id = employee.employee_id ";
$sql2 .= "WHERE employee.supervisor_id = '$userLevel' ";
$sql2 .= "ORDER BY review_year DESC ";

//Query the database
$rs2 = $dbConn->query($sql2)
    or die('Problem with query' . $dbConn->error);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/projectMaster.css">
    <script src="javascript/projectScript.js" defer></script>
    <title>Performance Review Selection</title>
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
                <li><?php echo "Hi $userName ($userLevel)"; ?><a href="logoff.php">Log Off</a></li>
                <li>Server date: <?php echo $serverDate; ?></li>
            </ul>
        </div>

        <!-- Personal performance review -->
        <div class="container-header">
            <h3>Your performance reviews</h3>
        </div>

        <div id="staff-table-container">

            <!-- If there are no records of review, display an alternate message rather than a table -->
            <?php
            if ($rs1->num_rows) : ?>

                <!-- changing while loop to foreach, sourced from https://stackoverflow.com/questions/63790018/how-to-user-fetch-assoc-twice-in-a-single-php-file/63791411#63791411-->

                <!-- Display current(ie. Outstanding) performance reviews -->
                <table>
                    <tr>
                        <thead>
                            <th class="employee-header">Outstanding</th>
                        </thead>
                    </tr>
                    <?php foreach ($rs1 as $row) : ?>
                        <tr>
                            <?php if ($row["completed"] == "N") : ?>

                                <td>
                                    <?php echo "Year Review: " . $row["review_year"] . "<br>"; ?>
                                    <a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>">Process</a>
                                </td>

                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>

                <!-- Display Completed performance reviews -->
                <table>
                    <tr>
                        <thead>
                            <th class="employee-header">Completed</th>
                        </thead>
                    </tr>
                    <?php foreach ($rs1 as $row) : ?>
                        <tr>
                            <?php if ($row["completed"] == "Y") : ?>

                                <td>
                                    <?php echo "Year Review: " . $row["review_year"] . "<br> Date completed: " . $row["date_completed"] . "<br>"; ?>
                                    <a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>">View</a>
                                </td>

                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>


            <?php else :
                echo "<p>You have no performance reviews to accept or view.</p>";
            endif; ?>
        </div>

        <!-- For each performance review that belongs to this logged-in supervisor display the Employee’s Surname, Employee’s Firstname,
year of review, review id, employee id, completed status, and date completed. Each Employee’s Surname is to be a hypertext link
to the View Performance Review page. When clicked, the link must pass the review id of the review to the View Performance Review
page. -->

        <!-- If the logged in user is a supervisor, show the below section -->
        <!-- Supervisor performance reviews access -->
        <?php if (($userLevel == "DM001") || ($userLevel == "DM002") || ($userLevel == "DMCEO")) : ?>
            <div class="container-header">
                <h3>Staff performance review</h3>
            </div>

            <div id="supervisor-table-container">

                <h4>Outstanding</h4>

                <table>
                    <tr>
                        <thead>
                            <th>Surname</th>
                            <th>First Name</th>
                            <th>Employee ID</th>
                            <th>Year of Review</th>
                            <th>Employee Accepted?</th>
                            <th>Date Completed</th>
                        </thead>

                    </tr>

                    <?php foreach ($rs2 as $row) : ?>

                        <?php if ($row["supervisor_id"] == $userLevel) : ?>
                            <?php if ($row["completed"] == "N") : ?>
                                <tr>

                                    <td><a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>"><?php echo $row["surname"]; ?></a></td>
                                    <td><?php echo $row["firstname"]; ?></td>
                                    <td><?php echo $row["employee_id"]; ?></td>
                                    <td><?php echo $row["review_year"]; ?></td>
                                    <td><?php echo $row["completed"]; ?></td>
                                    <td><?php echo $row["date_completed"]; ?></td>

                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>

                <h4>Completed</h4>
                <table>

                    <tr>
                        <thead>
                            <th>Surname</th>
                            <th>First Name</th>
                            <th>Employee ID</th>
                            <th>Year of Review</th>
                            <th>Employee Accepted?</th>
                            <th>Date Completed</th>
                        </thead>

                    </tr>
                    <?php foreach ($rs2 as $row) : ?>
                        <tr>
                            <?php if ($row["supervisor_id"] == $userLevel) : ?>
                                <?php if ($row["completed"] == "Y") : ?>

                                    <td><a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>"><?php echo $row["surname"]; ?></a></td>
                                    <td><?php echo $row["firstname"]; ?></td>
                                    <td><?php echo $row["employee_id"]; ?></td>
                                    <td><?php echo $row["review_year"]; ?></td>
                                    <td><?php echo $row["completed"]; ?></td>
                                    <td><?php echo $row["date_completed"]; ?></td>

                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div <?php endif; ?> </div>

            <?php // Close the connection to the database
            $dbConn->close(); ?>

</body>

</html>