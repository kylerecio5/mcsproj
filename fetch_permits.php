<?php
// Include your database connection
include 'db_connection.php';

// Prepare the query to get all permits, including Permit_ID
$sql = "SELECT Permit_ID, Name, PhoneNo, BuildingType, PermitNum, PermitDate, Block, Lot, Street, Amount FROM tbl_construction_permits";

// Execute the query and fetch the results
$result = $conn->query($sql);

// Check if there are any records
if ($result->num_rows > 0) {
    // Create an array to store the results
    $permits = [];
    while($row = $result->fetch_assoc()) {
        $permits[] = $row;
    }
    // Return the data in JSON format
    echo json_encode(["success" => true, "data" => $permits]);
} else {
    // No records found
    echo json_encode(["success" => false, "message" => "No records found"]);
}

// Close the connection
$conn->close();
?>
