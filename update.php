<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the AJAX request
    $recordId = $_POST["id"];
    $followUp = $_POST["followUp"];
    $status = $_POST["status"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "myweb";

    // Create a connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update the Follow-up and Status data in the database
    $updateQuery = "UPDATE returns SET followUp=?, status=? WHERE id=?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssi", $followUp, $status, $recordId);

    if ($updateStmt->execute()) {
        // Update was successful
        echo json_encode(["message" => "Data updated successfully"]);
    } else {
        // Update failed
        echo json_encode(["error" => "Error updating data: " . $updateStmt->error]);
    }

    $updateStmt->close();
    $conn->close();
}
?>
