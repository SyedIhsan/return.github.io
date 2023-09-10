<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recordId = $_POST["recordId"]; // Change "id" to "recordId"

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

    // Prepare and execute the DELETE statement
    $stmt = $conn->prepare("DELETE FROM returns WHERE id = ?");
    $stmt->bind_param("i", $recordId); // Change "id" to "recordId"

    if ($stmt->execute()) {
        echo "Row deleted successfully";
    } else {
        echo "Error deleting row: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
