<?php
header('Content-Type: application/json');

// Connect to the database
$connection = new mysqli('localhost', 'root', '', 'tms');

// Check connection
if ($connection->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if (isset($_GET['id'])) {
    $account_id = intval($_GET['id']);

    // Fetch account details
    $query = "SELECT * FROM tbl_accounts WHERE account_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $account_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'firstname' => $row['firstname'], 'middlename' => $row['middlename'], 'lastname' => $row['lastname'], 'email' => $row['email'], 'username' => $row['username'], 'role' => $row['role'], 'phonenum' => $row['phonenum'], 'dateofbirth' => $row['dateofbirth'], 'age' => $row['age'], 'sex' => $row['sex'], 'address' => $row['address']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Account not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Account ID is required']);
}

$connection->close();
?>
