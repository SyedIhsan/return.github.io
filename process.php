<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doNo = $_POST["doNo"];
    $transporter = $_POST["transporter"];
    $reason = $_POST["reason"];
    $date = $_POST["date"];

    // Check if it's an AJAX request (sent by JavaScript)
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Handle AJAX request
        $status = $_POST["status"];
        $followUp = $_POST["followUp"];
    } else {
        // Handle non-AJAX (traditional form submission) request
        $status = "Follow-up";
        $followUp = "";
    }

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

    // Insert data into the 'returns' table
    $stmt = $conn->prepare("INSERT INTO returns (doNo, transporter, reason, date, status, followUp) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $doNo, $transporter, $reason, $date, $status, $followUp);

    if ($stmt->execute()) {
        // Redirect to success.php after successful submission
        header("Location: index.php");
        exit();
    } else {
        echo json_encode(["error" => "Error inserting data: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>