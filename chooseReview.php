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

        <div id="main-container">

        <!-- If there are no records of review, display an alternate message rather than a table -->
            <?php
            if ($rs->num_rows) : ?>

                <!-- changing while loop to foreach, sourced from https://stackoverflow.com/questions/63790018/how-to-user-fetch-assoc-twice-in-a-single-php-file/63791411#63791411-->
                
                <table>
                    <tr><thead><th>Outstanding</th></thead></tr>

                    <!-- For Current (non-complete reviews), only display data if it exist -->
                    <?php foreach ($rs as $row) :
                        $recordCounter = 0; ?>
                        <tr>
                            <?php if ($row["completed"] == "N") :
                                $recordCounter++;  ?>

                                <td>
                                    <?php echo "Year Review: " . $row["review_year"] . "<br>"; ?>
                                    <a href="viewReview.php?review_id=<?php echo $row["review_id"]; ?>">Process</a>
                                </td>
                            <?php endif; ?>
                        </tr>

                    <?php endforeach; ?>
                    <!-- Otherwise, display an alternate message -->
                    <?php if ($recordCounter == 0) echo "<td>You have no outstanding review to process.</td>";  ?>
                </table>

                <table><tr><thead><th>Completed</th></thead></tr>

                    <!-- On the contrary, "Completed" reviews will always come after being in the "Current"
                    table, so the only case where there will be no records, is for DM001 (Michael), and the
                    alternate message will be already captured by the main $rs->num_rows condition -->
                    
                    <?php foreach ($rs as $row) : ?>
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

        <!-- If the logged in user is a supervisor, show the below section -->
        <!-- Supervisor performance reviews to create -->
        <?php if (($userLevel == "DM001") || ($userLevel == "DM002") || ($userLevel == "DMCEO")): ?>
        <div class="container-header">
            <h3>Staff performance review</h3>
        </div>
        <?php endif; ?>

    </div>
    <?php // Close the connection to the database
    $dbConn->close(); ?>

</body>

</html>