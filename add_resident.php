<?php
// Start the session
session_start();

// Database connection
$host = "localhost";
$dbname = "tms";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieving form data
    $first_name = $_POST['first-name'];
    $middle_name = $_POST['middle-name'];
    $last_name = $_POST['last-name'];
    $phone_number = $_POST['phone-number'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $member_type = $_POST['member-type'];
    $membership_fee = $_POST['membership-fee'];
    $block = $_POST['block'];
    $lot = $_POST['lot'];
    $street = $_POST['street'];

    // Generate random 4-digit resident code
    $residentcode = rand(1000, 9999); // Generates a 4-digit random number

    // Prepare SQL query to insert data along with resident code
    $sql = "INSERT INTO tbl_residents (F_name, M_name, L_name, PhoneNum, Age, Sex, MemberType, Membership, Block, Lot, Street, ResidentCode)
            VALUES ('$first_name', '$middle_name', '$last_name', '$phone_number', '$age', '$sex', '$member_type', '$membership_fee', '$block', '$lot', '$street', '$residentcode')";

    if ($conn->query($sql) === TRUE) {
        // Log the action (Adding resident)
        if (isset($_SESSION['user']['firstname'], $_SESSION['user']['middlename'], $_SESSION['user']['lastname'])) {
            // Get the full name of the logged-in user
            $user = $_SESSION['user']['firstname'] . ' ' . $_SESSION['user']['middlename'] . ' ' . $_SESSION['user']['lastname'];
        } else {
            // Fallback if session variables are incomplete
            $user = 'Unknown User';
        }

        $action = 'Add Resident';
        $description = "$user added a new resident: $first_name $last_name"; // Log description with user's full name
        $date = date('Y-m-d');
        $time = date('H:i:s');

        // Insert log into tbl_logs
        $logSql = "INSERT INTO tbl_logs (account_id, action, description, Date, Time) 
                   VALUES (?, ?, ?, ?, ?)";
        $logStmt = $conn->prepare($logSql);
        $logStmt->bind_param('issss', $_SESSION['user']['account_id'], $action, $description, $date, $time);
        $logStmt->execute();
        $logStmt->close();

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => $conn->error]);
    }

    $conn->close();
}
?>
