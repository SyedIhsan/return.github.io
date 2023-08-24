<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "webdb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$doNo = $_POST['do-no'];
$transporter = $_POST['transporter'];
$reason = $_POST['reason'];
$date = $_POST['date'];
$status = "Follow-up";
$followUp = "";

$insertQuery = "INSERT INTO returns (do_no, transporter, reason, date, status, follow_up)
                VALUES ('$doNo', '$transporter', '$reason', '$date', '$status', '$followUp')";

if ($conn->query($insertQuery) === TRUE) {
    echo "Data inserted successfully";
} else {
    echo "Error inserting data: " . $conn->error;
}

$selectQuery = "SELECT do_no, transporter, reason, date, status, follow_up FROM returns";
$result = $conn->query($selectQuery);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Output or process each row of data
        echo "DO No.: " . $row["do_no"] . "<br>";
        echo "Transporter: " . $row["transporter"] . "<br>";
        echo "Reason: " . $row["reason"] . "<br>";
        echo "Date: " . $row["date"] . "<br>";
        echo "Status: " . $row["Follow-up"] . "<br>";
        echo "FollowUp: " . $row[""] . "<br>";
        // ... (similarly for other columns)
        echo "<br>";
    }
} else {
    echo "No data found";
}

$newStatus = "Completed";
$newFollowUp = "Issue resolved and case closed";

$updateQuery = "UPDATE returns
                SET status = '$newStatus', follow_up = '$newFollowUp'
                WHERE do_no = '$doNo'";

if ($conn->query($updateQuery) === TRUE) {
    echo "Data updated successfully";
} else {
    echo "Error updating data: " . $conn->error;
}

$deleteQuery = "DELETE FROM returns WHERE do_no = '$doNo'";

if ($conn->query($deleteQuery) === TRUE) {
    echo "Data deleted successfully";
} else {
    echo "Error deleting data: " . $conn->error;
}

$conn->close();
?>