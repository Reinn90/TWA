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

// Build SQL query
$sql = "SELECT review_id, review_year, date_completed, completed ";
$sql .= "FROM review ";
$sql .= "WHERE employee_id = '$userLevel' ";

//Query the database
$rs = $dbConn->query($sql)
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

        <div id="main-container">

            <?php
            if ($rs->num_rows) : ?>

                <table>
                    <tr>
                        <th>Current</th>
                        <th>Completed</th>
                    </tr>

                    <tr>
                        <?php while ($row = $rs->fetch_assoc()) : ?>

                            <!-- Current performance reviews -->

                            <?php if ($row["completed"] == "N") : ?>
                                <td>
                                    <?php echo "Year Review: " . $row["review_year"] . "<br>"; ?>
                                    <a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>">Process</a>
                                
                                </td>
                            <?php endif; ?>



                            <!-- Completed performance reviews -->

                            <?php if ($row["completed"] == "Y") : ?>
                                <td>
                                    <?php echo "Year Review: " . $row["review_year"] . "<br> Date completed: " . $row["date_completed"] . "<br>"; ?>
                                    <a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>">Process</a>


                                </td>
                            <?php endif; ?>


                        <?php endwhile; ?>
                    </tr>

                </table>

            <?php else :
                echo "<p>You have no performance reviews to accept or view.</p>";
            endif; ?>
        </div>

        <!-- Supervisor performance reviews to create -->


    </div>
    <?php // Close the connection to the database
    $dbConn->close(); ?>

</body>

</html>