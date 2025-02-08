<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include your database connection
include 'db_connection.php';

// Start the session for user identification
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if any required fields are missing
    if (!isset($_POST['first-name']) || !isset($_POST['phone-number']) || !isset($_POST['building-type']) || !isset($_POST['permit-number']) || !isset($_POST['block']) || !isset($_POST['lot']) || !isset($_POST['street']) || !isset($_POST['amount'])) {
        echo json_encode(["success" => false, "message" => "Missing required fields."]);
        exit;
    }

    // Get the form data
    $first_name = $_POST['first-name'];
    $middle_name = $_POST['middle-name'];
    $last_name = $_POST['last-name'];
    $phone_number = $_POST['phone-number'];
    $building_type = $_POST['building-type'];
    $permit_number = $_POST['permit-number'];
    $block = $_POST['block'];
    $lot = $_POST['lot'];
    $street = $_POST['street'];
    $amount = $_POST['amount']; // Ensure this is in the correct format (e.g., '500.00')

    // Concatenate first name, middle name, and last name into a single name field
    $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

    // Prepare the SQL query
    $sql = "INSERT INTO tbl_construction_permits (Name, PhoneNo, BuildingType, PermitNum, PermitDate, Block, Lot, Street, Amount) 
            VALUES (?, ?, ?, ?, CURDATE(), ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Make sure you're binding the parameters in the right order
        $stmt->bind_param("ssssssss", $full_name, $phone_number, $building_type, $permit_number, $block, $lot, $street, $amount);

        // Execute the statement and check for success
        if ($stmt->execute()) {
            // Get the generated Permit_ID (auto-increment value)
            $permit_id = $stmt->insert_id;

            // Log the action of adding a permit (Name, BuildingType, and PermitNum)
            if (isset($_SESSION['user']['account_id'])) {
                $accountId = $_SESSION['user']['account_id']; // Get the logged-in user's account ID
                $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
            } else {
                $accountId = null;
                $user = 'Unknown User';
            }

            // Prepare log description
            $action = 'Add Construction Permit';
            $description = "$user added permit for $full_name (Building Type: $building_type, Permit Number: $permit_number)"; // Log details
            $date = date('Y-m-d');
            $time = date('H:i:s');

            // Insert log into tbl_logs
            $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                       VALUES (?, ?, ?, ?, ?)";
            $logStmt = $conn->prepare($logSql);
            $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
            $logStmt->execute();
            $logStmt->close();

            // Return success response in JSON format
            echo json_encode([
                "success" => true,
                "message" => "Permit saved successfully!",
                "permit_id" => $permit_id // Return the Permit_ID
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Error saving permit: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
    }

    // Close the database connection
    $conn->close();
}
?>
