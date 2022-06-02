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

// Retrieve session variables
$userName = $_SESSION['who'];     // Name of the employee
$userLevel = $_SESSION['level'];  // Employee id

// get Server date
$serverDate = date("Y-m-d");

// Connect to the database to retrieve performance reviews
require_once("conn.php");

// Employee performance review database query
// Retrieve review_year, date_completed and display in reverse order
$sql1 = "SELECT review_id, review_year, date_completed, completed ";
$sql1 .= "FROM review ";
$sql1 .= "WHERE employee_id = '$userLevel' ";
$sql1 .= "ORDER BY review_year DESC ";

// Query the database
$rs1 = $dbConn->query($sql1)
    or die('Problem with query' . $dbConn->error);


// Supervisor performance review database query
// Retrieve Employee’s Surname, Employee’s Firstname, year of review, review id, employee id, completed status, date completed and accepted status
$sql2 = "SELECT surname, firstname, review_year, review_id, employee.employee_id, completed, date_completed, supervisor_id, accepted ";
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
                <li><?php echo "Hi $userName "; ?><a href="logoff.php">Log Off</a></li>
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
                            <!-- Condition to determine whether a review is Current or Completed -->
                            <?php if ($row["completed"] == "N") : ?>

                                <td>
                                    <?php echo "Year Review: " . $row["review_year"] . "<br><br>"; ?>
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
                                    <?php echo "Year Review: " . $row["review_year"] . "<br> Date completed: " . $row["date_completed"] . "<br><br>"; ?>
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


        <!-- If the logged in user is a supervisor, show the below section -->
        <!-- Supervisor performance reviews access -->
        <?php if (($userLevel == "DM001") || ($userLevel == "DM002") || ($userLevel == "DMCEO")) : ?>
            

            <div class="container-header">
                <h3>Staff performance review</h3>
            </div>

            <!-- Create a new review -->
            <div id="createNewReview">
                <a href="createReview.php">Create a new review</a>
            </div>

            <div id="supervisor-table-container">
                <!-- Check for an empty record set -->
                <?php if ($rs2->num_rows) : ?>

                    <!-- Display Outstanding reviews in a table -->
                    <h4>Outstanding</h4>

                    <!-- Check whether there are incomplete reviews -->
                    <?php
                    $currentReviewCounter = 0; // initialise variable

                    // loop though the database to see if ther are any incomplete reviews
                    foreach ($rs2 as $row) {
                        if ($row["completed"] == "N") $currentReviewCounter++;
                    }

                    //If there are no incomplete reviews, show a different message 
                    if ($currentReviewCounter != 0) :  ?>

                        <table>
                            <tr>
                                <thead>
                                    <th>Surname</th>
                                    <th>First Name</th>
                                    <th>Employee ID</th>
                                    <th>Year of Review</th>
                                    <th>Review Complete?</th>
                                    <th>Date Completed</th>
                                    <th>Employee Accepted?</th>
                                </thead>

                            </tr>

                            <?php foreach ($rs2 as $row) : ?>

                                <!-- Show only employee reviews to their immediate supervisor -->
                                <?php if ($row["supervisor_id"] == $userLevel) : ?>

                                    <!-- Show outstanding, i.e. incomplete reviews -->
                                    <?php if ($row["completed"] == "N") : ?>
                                        <tr>

                                            <td><a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>"><?php echo $row["surname"]; ?></a></td>
                                            <td><?php echo $row["firstname"]; ?></td>
                                            <td><?php echo $row["employee_id"]; ?></td>
                                            <td><?php echo $row["review_year"]; ?></td>
                                            <td><?php echo $row["completed"]; ?></td>
                                            <td><?php echo $row["date_completed"]; ?></td>
                                            <td><?php echo $row["accepted"]; ?></td>


                                        </tr>
                                    <?php endif; ?>
                                <?php endif; // Supervisor check condition?> 
                            <?php endforeach; ?>
                        </table>
                    <?php else : echo "<p>You have no outstanding staff review.</p>";
                    endif; ?>

                    <!-- Display Completed reviews -->
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
                                        <td><?php echo $row["accepted"]; ?></td>
                                        <td><?php echo $row["date_completed"]; ?></td>

                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else : echo "<p>You have no staff performance review history.</p>"; ?>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>

    <?php // Close the connection to the database
    $dbConn->close(); ?>

</body>

</html>