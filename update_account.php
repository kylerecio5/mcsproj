<?php
session_start();
// Establish connection to your database
$servername = "localhost"; // Use your server name
$username = "root"; // Use your database username
$password = ""; // Use your database password
$dbname = "tms"; // Use your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 1: Check if the account_id is set and handle the form data
if (!isset($_POST['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Account ID is missing']);
    exit;
}

$accountId = $_POST['account_id']; // Get the account_id from the form
$username = $_POST['username']; // Get the username from the form (this allows editing)
$firstName = $_POST['first_name'];
$middleName = $_POST['middle_name'];
$lastName = $_POST['last_name'];
$phoneNumber = $_POST['phone_number'];
$email = $_POST['email'];
$sex = $_POST['sex'];
$age = $_POST['age'];
$dob = $_POST['dob'];
$address = $_POST['address'];
$accountType = $_POST['account_type'];

// Step 2: Prepare SQL query to update the account details (including the username)
$sql = "UPDATE tbl_accounts SET
            username = ?, 
            firstname = ?, 
            middlename = ?, 
            lastname = ?, 
            phonenum = ?, 
            email = ?, 
            sex = ?, 
            age = ?, 
            dateofbirth = ?, 
            address = ?, 
            role = ? 
        WHERE account_id = ?";

// Step 3: Prepare and bind the SQL statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssi", $username, $firstName, $middleName, $lastName, $phoneNumber, $email, $sex, $age, $dob, $address, $accountType, $accountId);

// Step 4: Execute the statement and check if it was successful
if ($stmt->execute()) {
    // Step 5: Log the action with the name of the user performing it
    // Assuming the session contains the logged-in user's info
    if (isset($_SESSION['user']['firstname']) && isset($_SESSION['user']['middlename']) && isset($_SESSION['user']['lastname'])) {
        $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
    } else {
        // Fallback in case session variables are incomplete
        $user = 'Unknown User';
    }

    $action = 'Edit Account';
    $description = "$user edited account $username"; // Updated log description with full name
    $date = date('Y-m-d');
    $time = date('H:i:s');

    // Insert log into tbl_logs
    $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
               VALUES (?, ?, ?, ?, ?)";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param('issss', $accountId, $action, $description, $date, $time);
    $logStmt->execute();
    $logStmt->close();

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Account updated successfully!']);
} else {
    // Return failure response with error message
    echo json_encode(['success' => false, 'message' => 'Error updating account: ' . $stmt->error]);
}


// Close the statement and connection
$stmt->close();
$conn->close();
?>
