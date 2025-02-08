<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

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

// Assuming the booking number is stored in session after successful reservation
if (isset($_SESSION['form_data'])) {
    // Retrieve the session data
    $formData = $_SESSION['form_data'];

    // Initialize variables with session data
    $first_name = htmlspecialchars($formData['first-name'] ?? 'Not Provided');
    $last_name = htmlspecialchars($formData['last-name'] ?? 'Not Provided');
    $phone_number = htmlspecialchars($formData['phone-number'] ?? 'Not Provided');
    $amenity = htmlspecialchars($formData['amenities'] ?? 'Not Selected');
    $client_type = htmlspecialchars($formData['client-type'] ?? 'Not Specified');
    $datetime = htmlspecialchars($formData['datetime'] ?? 'Not Specified');
    $additionals = [
        'table' => htmlspecialchars($formData['table'] ?? 0),
        'chair' => htmlspecialchars($formData['chair'] ?? 0),
        'karaoke' => htmlspecialchars($formData['karaoke'] ?? 0),
    ];
    $total_amount = htmlspecialchars($formData['total-amount'] ?? '0.00');
    $note = htmlspecialchars($formData['note'] ?? 'None');

    // Retrieve the most recent booking number based on the reservation_id (highest value)
    $stmt = $conn->prepare("SELECT booking_no FROM tbl_reservations ORDER BY reservation_id DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($booking_no);
    $stmt->fetch();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script src="payments.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="payment.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="topnav" id="myTopnav">
        <a href="index.html" class="active">
            <img src="citation_logo.png" alt="Home" class="logo-icon">
        </a>
        <a href="status.php">Status</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact Us</a>
        <a href="book.php">Book</a>
        <a href="price.php">Price List</a>
        <a href="index.php">Home</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
        </a>
    </div>

    <h1>BOOK NOW!</h1>

    <div class="pad">
        <div class="form_background">
            <form class="booking-form">
                <img id="logo" src="citation_logo_big.png" width="80">
                <h2 class="blabel">PAYMENT DETAILS</h2>
          
                <div class="details">
                    <h2 id="booking_number">Booking number: <span>#<?php echo htmlspecialchars($booking_no); ?></span></h2>
                    <h2 id="ps">Payment settlement: Clubhouse office.</h2>
                
                    <h2 id="note">
                        Note: You have until <b>24 hrs to settle the payment amount of ₱5,000.00 or pay 50% downpayment (₱2,500.00) to confirm the slot booking</b>, failure to settle the payment within the time frame will result in voiding of the slot for booking. Kindly screenshot this page and show the booking number to confirm the booking.
                    </h2>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
        <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> | 
        <a href="#">Follow us</a>
    </div>
</body>
</html>
