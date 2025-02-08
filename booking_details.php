<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); // Start the session

// Check if session data exists
if (isset($_SESSION['form_data'])) {
    // Retrieve the session data
    $formData = $_SESSION['form_data'];

    // Initialize variables with session data or default values
    $name = isset($formData['first-name']) && isset($formData['middle-name']) && isset($formData['last-name']) 
        ? htmlspecialchars($formData['first-name'] . ' ' . $formData['middle-name'] . ' ' . $formData['last-name']) 
        : 'Not Provided';
    $phone_number = isset($formData['phone-number']) ? htmlspecialchars($formData['phone-number']) : 'Not Provided';
    
    $amenity = isset($formData['amenities']) ? htmlspecialchars($formData['amenities']) : 'Not Selected';
    $client_type = isset($formData['client-type']) ? htmlspecialchars($formData['client-type']) : 'Not Specified';
    $selected_date = isset($formData['selected-date']) ? htmlspecialchars($formData['selected-date']) : 'Not Specified';
    $selected_times = isset($formData['selected-times']) ? htmlspecialchars($formData['selected-times']) : 'Not Specified';
    $additionals = [
        'table' => isset($formData['table']) ? htmlspecialchars($formData['table']) : 'None',
        'chair' => isset($formData['chair']) ? htmlspecialchars($formData['chair']) : 'None',
        'karaoke' => isset($formData['karaoke']) ? htmlspecialchars($formData['karaoke']) : 'None'
    ];
    $total_amount = isset($formData['total-amount']) ? htmlspecialchars($formData['total-amount']) : '0.00';
    $note = isset($formData['note']) ? htmlspecialchars($formData['note']) : 'None';
} else {
    // Default values if session data is not set
    $name = 'Not Provided';
    $phone_number = 'Not Provided';
    $amenity = 'Not Selected';
    $client_type = 'Not Specified';
    $selected_date = 'Not Specified';
    $selected_times = 'Not Specified';
    $additionals = ['table' => 'None', 'chair' => 'None', 'karaoke' => 'None'];
    $total_amount = '0.00';
    $note = 'None';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <script src="booking_details.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="booking_details.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Function to generate 5 random numbers
        function generateRandomNumbers() {
            const randomNumbers = [];
            for (let i = 0; i < 5; i++) {
                randomNumbers.push(Math.floor(Math.random() * 10000)); // Random number between 0 and 9999
            }
            return randomNumbers;
        }

        // Function to add random numbers to the form before submission
        function addRandomNumbers() {
            const randomNumbers = generateRandomNumbers();
            document.getElementById('random_numbers').value = randomNumbers.join(','); // Store them in a hidden field
        }
    </script>
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
    </div>

    <!-- Booking Details Section -->
    <h1>BOOK NOW!</h1>
    <div class="pad">
        <div class="form_background">
            <form action="submit_reservation.php" method="POST" class="booking-form" onsubmit="addRandomNumbers()">
                <h2 class="blabel">BOOKING DETAILS</h2>

                <div class="details">
                    <h2 id="namel">Name:</h2>
                    <h2 id="name"><?php echo $name; ?></h2>

                    <h2 id="numberl">Phone number:</h2>
                    <h2 id="number"><?php echo $phone_number; ?></h2>

                    <h2 id="amenityl">Amenity:</h2>
                    <h2 id="amenity"><?php echo $amenity; ?></h2>

                    <h2 id="clientl">Client type:</h2>
                    <h2 id="client"><?php echo $client_type; ?></h2>

                    <h2 id="datel">Selected Date:</h2>
                    <h2 id="date"><?php echo $selected_date; ?></h2>

                    <h2 id="timel">Selected Times:</h2>
                    <h2 id="time"><?php echo $selected_times; ?></h2>

                    <h2 id="addl">Additionals:</h2>
                    <h2 id="add">
                        Table: <?php echo $additionals['table']; ?>, 
                        Chair: <?php echo $additionals['chair']; ?>, 
                        Karaoke: <?php echo $additionals['karaoke']; ?>
                    </h2>

                    <h2 id="notesl">Notes:</h2>
                    <h2 id="notes"><?php echo $note; ?></h2>
                </div>

                <h3>TOTAL AMOUNT BREAKDOWN:</h3>
                <div id="breakdown-container">
                    <!-- Breakdown details can be added here -->
                    <div class="breakdown-item"><span>Selected Date:</span><span><?php echo $selected_date; ?></span></div>
                    <div class="breakdown-item"><span>Times:</span><span><?php echo $selected_times; ?></span></div>
                    <div class="breakdown-item"><span>Amenity:</span><span><?php echo $amenity; ?></span></div>
                    <div class="breakdown-item"><span>Total:</span><span>₱<?php echo $total_amount; ?></span></div>
                </div>

                <div class="total-row">
                    <span>Total:</span>
                    <span id="total-amount">₱<?php echo $total_amount; ?></span>
                </div>

                <!-- Hidden input to store random numbers -->
                <input type="hidden" name="random_numbers" id="random_numbers">

                <div class="form-bottom1">
                    <button type="reset" class="edit-btn">EDIT</button>
                    <button type="submit" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
        <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> | 
        <a href="#">Follow us</a>
    </div>
</body>
</html>
