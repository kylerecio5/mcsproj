<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); // Start the session
file_put_contents('log.txt', print_r($_POST, true), FILE_APPEND);

// Database connection
$servername = "localhost"; // Change this to your DB server
$username = "root"; // DB username
$password = ""; // DB password
$dbname = "tms"; // DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if session data exists
if (isset($_SESSION['form_data'])) {
    // Retrieve the session data
    $formData = $_SESSION['form_data'];

    // Initialize variables with session data
    $first_name = htmlspecialchars($formData['first-name'] ?? 'Not Provided');
    $middle_name = htmlspecialchars($formData['middle-name'] ?? 'Not Provided');
    $last_name = htmlspecialchars($formData['last-name'] ?? 'Not Provided');
    $phone_number = htmlspecialchars($formData['phone-number'] ?? 'Not Provided');
    $amenity = htmlspecialchars($formData['amenities'] ?? 'Not Selected');
    $client_type = htmlspecialchars($formData['client-type'] ?? 'Not Specified');
    
    // Retrieve the date and time from selected fields
    $selected_date = htmlspecialchars($formData['selected-date'] ?? 'Not Specified');
    $selected_times = htmlspecialchars($formData['selected-times'] ?? 'Not Specified');
    
    // Combine selected date and time
    $date = date('Y-m-d', strtotime($selected_date));
    $time = $selected_times;

    // Retrieve additional details
    $additionals = [
        'table' => htmlspecialchars($formData['table'] ?? 0),
        'chair' => htmlspecialchars($formData['chair'] ?? 0),
        'karaoke' => htmlspecialchars($formData['karaoke'] ?? 0),
    ];
    
    $total_amount = htmlspecialchars($formData['total-amount'] ?? '0.00');
    $note = htmlspecialchars($formData['note'] ?? 'None');

    // Get the random numbers from the POST data and rename it to booking_no
    $booking_no = isset($_POST['random_numbers']) ? $_POST['random_numbers'] : ''; // Handle if random numbers are not passed

    // Prepare SQL query using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO tbl_reservations (amenities, client_type, F_name, M_name, L_name, date, time, phone_number, Total_Price, Note, `table`, chair, karaoke, booking_no)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind the parameters to the SQL query
    $stmt->bind_param("sssssssssssssi", $amenity, $client_type, $first_name, $middle_name, $last_name, $date, $time, $phone_number, $total_amount, $note, $additionals['table'], $additionals['chair'], $additionals['karaoke'], $booking_no);

    // Execute the query and check for success
    if ($stmt->execute()) {
        // Save booking_no into the session for use in payment.php
        $_SESSION['booking_no'] = $booking_no;

        // Success: redirect to payment.php
        header("Location: payment.php");
        exit();
    } else {
        // Error: display error message
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
} else {
    // No session data, redirect back to the booking form
    header("Location: book.php");
    exit();
}

$conn->close();
?>
