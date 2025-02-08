<?php
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

    $sql = "INSERT INTO tbl_accounts (firstname, middlename, lastname, phonenum, email, sex, age, dateofbirth, address, role, username, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
        exit();
    }

    $stmt->bind_param(
        'ssssssssssss',
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
        $password
    );

    if ($stmt->execute()) {
        $account_id = $stmt->insert_id;

        // Log action in tbl_logs
        $user = $_SESSION['user']; // Get the user from session
        $action = 'add account';
        $description = "Added account named $username ($account_type)";
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                   VALUES (?, ?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param('issss', $account_id, $action, $description, $date, $time);
        $logStmt->execute();
        $logStmt->close();

        echo json_encode(['success' => true, 'account_id' => $account_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to insert account into the database.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
