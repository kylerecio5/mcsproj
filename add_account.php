<?php
session_start();

// Connect to the database
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'tms';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST values
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? ''; // Optional field
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $sex = $_POST['sex'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $account_type = $_POST['account_type'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($phone_number) || empty($email) || empty($sex) || empty($dob) || empty($address) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit();
    }

    $is_deleted = 0; // New accounts are not deleted by default
    $admin_id = null;

    // Insert into tbl_admin if account type is Admin
    if ($account_type === 'Admin') {
        $adminSql = "INSERT INTO tbl_admin (admin_username, admin_password) VALUES (?, ?)";
        $adminStmt = $conn->prepare($adminSql);
        if (!$adminStmt) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare Admin statement: ' . $conn->error]);
            exit();
        }

        $adminStmt->bind_param('ss', $username, $password);

        if ($adminStmt->execute()) {
            $admin_id = $adminStmt->insert_id; // Get the generated admin_id
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to insert Admin account: ' . $adminStmt->error]);
            exit();
        }
        $adminStmt->close();
    }

    // Insert into tbl_accounts
    $sql = "INSERT INTO tbl_accounts (admin_id, firstname, middlename, lastname, phonenum, email, sex, age, dateofbirth, address, role, username, password, is_deleted) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    // Bind parameters
    $stmt->bind_param(
        'issssssssssssi',
        $admin_id,
        $first_name,
        $middle_name,
        $last_name,
        $phone_number,
        $email,
        $sex,
        $age,
        $dob,
        $address,
        $account_type,
        $username,
        $password,
        $is_deleted
    );

    if ($stmt->execute()) {
        $account_id = $stmt->insert_id; // Get the generated account ID
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert account: ' . $stmt->error]);
        exit();
    }
    $stmt->close();

    // Log the action
    if (isset($_SESSION['user']['firstname']) && isset($_SESSION['user']['middlename']) && isset($_SESSION['user']['lastname'])) {
        $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
    } else {
        // Fallback to 'Unknown User' if session data is incomplete
        $user = 'Unknown User';
    }

    $action = 'Add Account';
    $description = "$user added $account_type account $username";
    $date = date('Y-m-d');
    $time = date('H:i:s');

    $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
            VALUES (?, ?, ?, ?, ?)";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param('issss', $account_id, $action, $description, $date, $time);
    $logStmt->execute();
    $logStmt->close();


    // Response
    echo json_encode(['success' => true, 'account_id' => $account_id]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Close the connection
$conn->close();
?>
