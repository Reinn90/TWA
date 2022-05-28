<!--Nowell Reyes - 20658133 - Thursday 9am Online Tutorial  -->
<!-- sourced from TWA lecture slides by Paul Davies -->
<?php
$dbConn = new mysqli('localhost', 'root', '', 'performancereview464');
// change 'root' => twa464, and '' => twa464PV UPON SUBMISSION
if ($dbConn->connect_error) {
    // Exit execution and echo out connection error if it failed
    // to connect to the database     
    exit($dbConn->connect_error);
}
?>