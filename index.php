<?php
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $doNo = $_POST["doNo"];
  $transporter = $_POST["transporter"];
  $reason = $_POST["reason"];
  $date = $_POST["date"];
  $status = $_POST["status"];
  $followUp = $_POST["followUp"];
  $idToUpdate = $_POST["id"];

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

    $checkStmt->close();
    $updateStmt->close();
    $insertStmt->close();
    $conn->close();
}
    // Convert the data to JSON format
    $dataJSON = json_encode($data);
    ?>    

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Return</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
  }
  #app {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 20px;
  }
  #return-form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    width: 80%;
    max-width: 400px; /* Adjust the maximum width as needed */
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  #return-form label {
    width: 100%;
  }
  #return-form button[type="submit"] {
    margin-top: 10px;
  }
  #return-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  #return-table th, #return-table td {
    border: 1px solid #000000;
    padding: 10px;
    text-align: left;
  }
  #return-table th {
    background-color: #78b9fd;
  }
  h1, h2 {
    color: #333333;
  }
  label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  select, input[type="text"], input[type="datetime-local"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 10px;
    font-size: 14px;
    background-color: #f9f9f9; /* Adjust the background color */
    color: #333; /* Adjust the text color */
    box-sizing: border-box;
  }
  button[type="submit"] {
    background-color: #007bff;
    color: #ffffff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }
  button[type="submit"]:hover {
  background-color: #102A71; /* Darker green on hover */
  transform: scale(1.05); /* Scale the button slightly on hover */
}
  ul {
    list-style: none;
    padding: 0;
  }
  li {
    margin-bottom: 5px;
  }
  #delay-reason-container {
    display: none;
  }
  .date-input {
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
  }
  .follow-up {
    background-color: yellow;
    color: black;
    padding: 4px 8px;
    border-radius: 4px;
  }

  .completed {
    background-color: green;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
  }

  #return-table select {
    width: 150px; 
    padding: 10px;
    font-size: 16px;
  }
  #delivery-form {
    margin-bottom: 20px;
  }
  #delivery-table {
    width: 80%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  #delivery-table th, #delivery-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  /* Style for the "Delete" button */
button.delete-button {
  background-color: #007bff; /* Red background color */
  color: #fff; /* White text color */
  border: none;
  border-radius: 4px;
  padding: 8px 16px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Hover effect for the "Delete" button */
button.delete-button:hover {
  background-color: #102A71; /* Lighter red on hover */
  transform: scale(1.05); /* Scale the button slightly on hover */
}

/* Style for the "Save" button */
button.save-button {
  background-color: #007bff; /* Green background color */
  color: #fff; /* White text color */
  border: none;
  border-radius: 4px;
  padding: 8px 16px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Hover effect for the "Save" button */
button.save-button:hover {
  background-color: #102A71; /* Darker green on hover */
  transform: scale(1.05); /* Scale the button slightly on hover */
}

</style>
</head>
<body>
<div id="app">
  <h1>Return</h1>
  <form id="return-form" action="process.php" method="post">
    <label for="doNo">DO No.:</label>
    <input type="text" id="doNo" required name="doNo">
    <label for="transporter">Transporter:</label>
    <select id="transporter" required name="transporter">
      <option value="Local 01">Local 01</option>
      <option value="Local 02">Local 02</option>
      <option value="Local 03">Local 03</option>
      <option value="Local 04">Local 04</option>
      <option value="Freight Mark">Freight Mark</option>
      <option value="City-Link">City-Link</option>
      <option value="Tiong Nam">Tiong Nam</option>
      <option value="Pos Laju">Pos Laju</option>
      <option value="Other">Other</option>
    </select>
    <label for="reason">Reason:</label>
    <input type="text" id="reason" required name="reason">
    <label for="date">Date:</label>
    <input type="date" id="date" class="date-input" required name="date">
    <button type="submit">Update</button>
  </form>
  <h2>Return History</h2>
  <table id="return-table">
    <thead>
      <tr>
        <th>DO No.</th>
        <th>Transporter</th>
        <th>Reason</th>
        <th>Date</th>
        <th>Status</th>
        <th>Follow-up</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="return-history"></tbody>
  </table>
</div>
<script>
    const dataFromPHP = <?php echo $dataJSON; ?>;
    const returnHistory = document.getElementById("return-history");
    let returns = dataFromPHP || [];

    function formatDate(dateString) {
      const options = { year: "numeric", month: "short", day: "numeric" };
      return new Date(dateString).toLocaleDateString(undefined, options);
    }
  
    function addRowToTable(returnData, index) {
      const row = returnHistory.insertRow();
      const doNoCell = row.insertCell();
      const transporterCell = row.insertCell();
      const reasonCell = row.insertCell();
      const dateCell = row.insertCell();
      const statusCell = row.insertCell();
      const followUpCell = row.insertCell();
      const actionsCell = row.insertCell();
      row.setAttribute("data-record-id", returnData.id);

      doNoCell.textContent = returnData.doNo;
      transporterCell.textContent = returnData.transporter;
      reasonCell.textContent = returnData.reason;
      dateCell.textContent = formatDate(returnData.date);

      const statusSelect = document.createElement("select");
      const statusOptions = ["-", "Follow-up", "Completed"];
      statusOptions.forEach(option => {
        const statusOption = document.createElement("option");
        statusOption.value = option;
        statusOption.text = option;
        statusSelect.appendChild(statusOption);
      });

      statusSelect.value = returnData.status;
      statusCell.appendChild(statusSelect);

      statusSelect.addEventListener("change", () => {
        const newStatus = statusSelect.value;
        const recordId = row.getAttribute("data-record-id");
        const followUpInput = followUpCell.querySelector("input");
        const followUp = followUpInput.value;
        updateStatusAndFollowUp(recordId, newStatus, followUp);

      if (newStatus === "Completed") {
        followUpInput.value = "Case Closed";
        followUpInput.disabled = true;
        statusSelect.disabled = true;
      } else {
        followUpInput.disabled = false;
        statusSelect.disabled = false;
      }
    });

    // Disable the Status select element when Status is "Completed"
    if (returnData.status === "Completed") {
      statusSelect.disabled = true;
    }

      const followUpInput = document.createElement("input");
      followUpInput.type = "text";
      followUpCell.appendChild(followUpInput);

      followUpInput.value = returnData.followUp || "";
      if (returnData.status === "Completed") {
        followUpInput.value = "Case Closed";
        followUpInput.readOnly = true; // Use readOnly instead of disabled
      } else {
        followUpInput.readOnly = false; // Make sure it's not readOnly when status is not "Completed"
      }

      followUpInput.addEventListener("input", function () {
        returnData.followUp = followUpInput.value; // Update the followUp value
        saveReturnsToLocalStorage();
      });

      function handleStatusChange() {
        returnData.status = statusSelect.value;
        saveReturnsToLocalStorage();

        if (statusSelect.value === "Completed") {
          followUpInput.value = "Case Closed";
          followUpInput.disabled = true;
        } else if (statusSelect.value === "Follow-up") {
          followUpInput.value = returnData.followUp || "";
          followUpInput.disabled = false;
        } else {
          followUpInput.value = "";
          followUpInput.disabled = true;
        }
      }

      statusSelect.addEventListener("change", handleStatusChange);

      const deleteButton = document.createElement("button");
      deleteButton.textContent = "Delete";
      deleteButton.classList.add("delete-button");
      deleteButton.addEventListener("click", () => {
        const recordId = row.getAttribute("data-record-id"); // Get the record ID from the row attribute
        deleteReturn(index, recordId);
    });

    const saveButton = document.createElement("button");
    saveButton.textContent = "Save";
    saveButton.classList.add("save-button");
    saveButton.style.marginLeft = "10px"; // You can adjust the margin as needed
    saveButton.addEventListener("click", () => {
      const followUpInput = followUpCell.querySelector("input");
      const statusSelect = statusCell.querySelector("select");
      const recordId = row.getAttribute("data-record-id");
      const followUp = followUpInput.value;
      const status = statusSelect.value;
      updateFollowUpAndStatus(recordId, followUp, status);

       if (status === "Completed") {
        followUpInput.value = "Case Closed";
        followUpInput.readOnly = true; // Use readOnly instead of disabled
        statusSelect.disabled = true;
      }
    });

    actionsCell.appendChild(deleteButton);
    actionsCell.appendChild(saveButton);
  }

  function updateFollowUpAndStatus(recordId, followUp, status) {
  // Send an AJAX request to a PHP script to update the database
  fetch('update.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id=${recordId}&followUp=${followUp}&status=${status}`,
  })
    .then(response => response.json())
    .then(data => {
      if (data.message === "Data updated successfully") {
        // Optionally, you can update the UI to reflect the saved data
      } else {
        alert('Error saving Follow-up and Status data: ' + data.error);
      }
    })
    .catch(error => console.error('Error saving Follow-up and Status data:', error));
}

    function retrieveAndDisplayData() {
            fetch('retrieve_data.php')
                .then(response => response.json())
                .then(data => {
                    // Call a function to populate the return table with the retrieved data
                    loadReturnsFromData(data);
                })
                .catch(error => console.error('Error fetching data:', error));
        }

        // Call the function to retrieve and display data when the page loads
        retrieveAndDisplayData();

    function loadReturnsFromData(data) {
        // Clear the table first
        returnHistory.innerHTML = "";

        // Loop through the data and add rows to the table
        data.forEach((returnData, index) => {
            addRowToTable(returnData, index);
        });
    }

    loadReturnsFromData(dataFromPHP);

    function deleteReturn(index, recordId) {
    // Send an AJAX request to delete.php
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "delete.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle the server response
            const response = xhr.responseText;
            if (response === "Row deleted successfully") {
                
              const row = returnHistory.rows[index];
              if (row) {
                row.remove();
              } else {
                console.log("Row is undefined for index: " + index);
              }
              
                if (index >= 0 && index < returns.length) {
                    returns.splice(index, 1);
                } else {
                    console.log("Invalid index: " + index);
                }
                loadReturnsFromData(returns);
                saveReturnsToLocalStorage();
            } else {
                alert("Error deleting row: " + response);
            }
        }
    };
    location.reload();
    xhr.send("recordId=" + recordId); // Send the recordId to delete.php
}

  function saveReturnsToLocalStorage() {
    localStorage.setItem("returns", JSON.stringify(returns));
  }

  function addReturnToLocalStorage(returnData) {
    returns.push(returnData); // Add the new returnData to the returns array
    saveReturnsToLocalStorage(); // Update local storage
  }

  returnHistory.addEventListener("keydown", function (event) {
  if (event.key === "Enter" || event.keyCode === 13) {
    event.preventDefault(); // Prevent the default form submission

    // Find the row that contains the focused input element
    const focusedInput = document.activeElement;
    const parentRow = focusedInput.closest("tr");

    if (parentRow) {
      // Check if the focused input is in an editable row
      const recordId = parentRow.getAttribute("data-record-id");
      if (recordId !== null) {
        // The focused input is in an editable row
        const saveButton = parentRow.querySelector(".save-button");
        if (saveButton) {
          // Trigger the "Save" button click event
          saveButton.click();
        }
      }
    }
  }
});
    
    returnHistory.addEventListener("submit", function(event) {
      event.preventDefault();

      const doNoInput = document.getElementById("doNo");
      const transporterInput = document.getElementById("transporter");
      const reasonInput = document.getElementById("reason");
      const dateInput = document.getElementById("date");

      const doNo = doNoInput.value;
      const transporter = transporterInput.value;
      const reason = reasonInput.value;
      const date = dateInput.value;
      const status = "";
      
      if (doNo) {
        const row = returnHistory.insertRow();
        const doNoCell = row.insertCell();
        const transporterCell = row.insertCell();
        const reasonCell = row.insertCell();
        const dateCell = row.insertCell();
        const statusCell = row.insertCell();
        const actionsCell = row.insertCell();

        doNoCell.textContent = doNo;
        transporterCell.textContent = transporter;
        reasonCell.textContent = reason;
        dateCell.textContent = formatDate(date);

        const statusSelect = document.createElement("select");
        const statusOptions = ["-", "Follow-up", "Completed"];
        statusOptions.forEach(option => {
          const statusOption = document.createElement("option");
          statusOption.value = option;
          statusOption.text = option;
          statusSelect.appendChild(statusOption);
        });

        statusCell.appendChild(statusSelect); // Append the statusSelect element to the cell
        statusSelect.value = "-";

        const returnData = {
          doNo,
          transporter,
          reason,
          date,
          status: "-",
          followUp: ''
        };

        addRowToTable(returnData, returns.length);
        addReturnToLocalStorage(returnData);
          
      }
    });
</script>
<head>  
</body>
</html>