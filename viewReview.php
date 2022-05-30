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



// Connect to the database to retrieve performance reviews
require_once("conn.php");

// retrieve the review_id from the clicked hyperlink
$review = $dbConn->escape_string($_GET["review_id"]);


// Build the SQL query
// Employee information section - also retrive supervisor id to match with the logged in supervisor
$sql = "SELECT review.employee_id, surname, firstname, review_year, supervisor_id, completed, ";

// Ratings information section
$sql .= "job_knowledge, work_quality, initiative, communication, dependability, ";

// Evaluation section
$sql .= "additional_comment, date_completed, accepted ";

// Inner join employee and review tables
$sql .= "FROM review INNER JOIN employee ON review.employee_id = employee.employee_id ";

// Data to match the hyperlink
$sql .= "WHERE review_id = '$review' ";

// Query the database
$rs = $dbConn->query($sql)
    or die('Problem with query' . $dbConn->error);

// If the logged in user is not the owner of the review, or not the supervisor of the employee review,
// redirect to logoff page
foreach ($rs as $row) {

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

<!-- Since the Review Id came from the database on chooseReview.php, there will be no need to check for an empty record set -->

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

        <?php foreach ($rs1 as $row) : ?>
            <!-- If the review is 'current' and owned by the employee (ie. not the supervisor viewing it), show the below acknowledgement form -->
            <?php if (($row["completed"] == "N") && ($row["employee_id"] == $userLevel)) : ?>
                <div id="acknowledgement">
                    <h3>Acknowledgement</h3>
                    <form id="iAgreeForm" name="iAgreeForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?review_id=" . $review; ?>">
                        <p>Thank you for taking part in your Dunder Mifflin Performance Review. This review is an important
                            aspect of the development of our organisation and its profits and of you as a valued employee.</p>
                        <p><strong>By electronically signing this form, you confirm that you have discussed this review in detail
                                with your supervisor.</strong>
                            <small>The fine print: Signing this form does not necessarily indicate that you agree with this evaluation.</small>
                        </p>
                        <div>
                            <input type="checkbox" name="iAgree" id="iAgree">
                            <label for="iAgree" id="agreeLabel" >I agree</label><input type="submit" name="submit" id="submit">
                        </div>
                    </form>
                </div>

            <?php endif; ?>
        <?php endforeach; ?>


        <!-- Postback submission of the form to update the database -->
        <?php

        if (isset($_POST["submit"])) {


            // If the acceptance checkbox ticked, change the variable that is to be updated in the database
            if (!empty($_POST["iAgree"])) { //Delete the ECHO statements

                // update 'completed' and 'accepted' column in the database to "Y"
                $updateAccepted = "Y";
            } else {
                $updateAccepted = "N";
            }


            //Build SQL query
            //Update the review table to change the 'accepted' and 'completed' column values

            $sqlUpdate = "UPDATE review ";
            $sqlUpdate .= "SET accepted = '$updateAccepted' ";
            $sqlUpdate .= "WHERE review_id = '$review' ";

            //update database, display message box and disable form elements to show it has been completed.

            if ($dbConn->query($sqlUpdate) === TRUE) {
                echo "<p>Record updated successfully. You may exit this page.</p>";
                echo 
                "<script>
                document.getElementById('submit').style.display = 'none';
                document.getElementById('iAgree').style.display = 'none';
                document.getElementById('agreeLabel').style.display = 'none'
                </script>";
            } else {
                echo "<p>Error updating record: " . $conn->error . "</p>";
            }
        }

        // Close the connection to the database  
        $dbConn->close();

    
        ?>



    </div>



</body>

</html>