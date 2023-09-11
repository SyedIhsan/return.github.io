<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doNo = $_POST["doNo"];
    $transporter = $_POST["transporter"];
    $reason = $_POST["reason"];
    $date = $_POST["date"];
    $status = $_POST["status"];
    $followUp = $_POST["followUp"];
    $idToUpdate = $_POST["id"];

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

    // Check if the record with the given ID exists
    $checkRecordQuery = "SELECT * FROM returns WHERE id = ?";
    $checkStmt = $conn->prepare($checkRecordQuery);
    $checkStmt->bind_param("i", $idToUpdate);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // The record exists, perform an update
        $updateQuery = "UPDATE returns SET doNo=?, transporter=?, reason=?, date=?, status=?, followUp=? WHERE id=?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssssi", $doNo, $transporter, $reason, $date, $status, $followUp, $idToUpdate);

        if ($updateStmt->execute()) {
            // Update was successful
            echo json_encode(["message" => "Data updated successfully", "id" => $idToUpdate]);
        } else {
            // Update failed
            echo json_encode(["error" => "Error updating data: " . $updateStmt->error]);
        }
    } else {
        // The record does not exist, perform an insert
        $insertQuery = "INSERT INTO returns (doNo, transporter, reason, date, status, followUp) VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("ssssss", $doNo, $transporter, $reason, $date, $status, $followUp);

        if ($insertStmt->execute()) {
            // Get the ID of the last inserted row
            $lastInsertedId = $conn->insert_id;

            // Fetch the data of the newly inserted row
            $fetchQuery = "SELECT * FROM returns WHERE id = ?";
            $fetchStmt = $conn->prepare($fetchQuery);
            $fetchStmt->bind_param("i", $lastInsertedId);
            $fetchStmt->execute();
            $fetchResult = $fetchStmt->get_result();

            if ($fetchResult->num_rows > 0) {
                $row = $fetchResult->fetch_assoc();
                $data[] = $row;
            }

            echo json_encode(["message" => "Data inserted successfully", "id" => $lastInsertedId]);
        } else {
            echo json_encode(["error" => "Error inserting data: " . $insertStmt->error]);
        }
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

    $checkStmt->close();
    $updateStmt->close();
    $insertStmt->close();
    $stmt->close();
    $conn->close();
}
    // Convert the data to JSON format
    $dataJSON = json_encode($data);
?>
