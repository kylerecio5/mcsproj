<?php
// db_connection.php (make sure to include your database connection)
include('db_connection.php');

// Fetch data from the tbl_parking table
$query = "SELECT * FROM tbl_parking";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'Name' => $row['NAME'],
            'PhoneNo' => $row['PHONE_NO'],
            'ParkingType' => $row['PARKING_TYPE'],
            'PlateNo' => $row['PLATE_NO'],
            'VehicleType' => $row['VEHICLE_TYPE'],
            'Date' => $row['DATE'],
            'Amount' => $row['AMOUNT']
        ];
    }

    // Return the data as JSON
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    // Return an error if no data is found
    echo json_encode(['success' => false, 'message' => 'No parking data found']);
}
?>
