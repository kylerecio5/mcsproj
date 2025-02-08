<?php
// Include the database connection file
include("db_connection.php");

// Check if the booking number is provided through GET
if (isset($_GET['booking_no'])) {
    $booking_no = $_GET['booking_no'];

    // Prepare the SQL query to fetch the booking data
    $sql = "SELECT * FROM tbl_reservations WHERE Booking_no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_no);  // "i" denotes that the parameter is an integer

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the booking exists
    if ($result->num_rows > 0) {
        // Fetch the data from the row
        $row = $result->fetch_assoc();
        $name = $row['F_name'] . ' ' . $row['L_name'];
        $date_time = $row['date'] . ', ' . $row['time'];
        $amenity = $row['amenities'];
        $additionals = "Tables: ". $row ['table'] . " | Chairs: " . $row['chair'] . " | Karaoke: " . $row['karaoke'];
        $note = $row['Note'];
        $amount = $row['Total_Price'];
        $status = $row['client_type']; // Assuming "client_type" stores the booking status
    } else {
        // Handle case where no booking is found
        $error_message = "Booking not found!";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="status.js"></script>
    <link rel="stylesheet" href="status.css">
</head>
<body>
    <div class="topnav" id="myTopnav">
        <a href="index.html" class="active">
            <img src="citation_logo.png" alt="Home" class="logo-icon">
        </a>
        <a href="check_md.php">Check MD</a>
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

    <h1>STATUS</h1>

    <div class="gap">
    <form method="GET" action="status.php">
        <div class="search">
            <div class="searchbox_background">
                <input type="text" placeholder="Enter booking number" name="booking_no" class="searchbox">
            </div>
            <div class="searchbtn_background">
                <button type="submit" class="searchbtn">CHECK</button>
            </div>
        </div>
    </form>
    </div>

    <?php if (isset($name)): ?>
        <div class="gap1">
            <div class="status_bg">
              <img id="logo" src="citation_logo_big.png" width="80">
              <h2 id="booking_number">BOOKING NUMBER: #<?php echo htmlspecialchars($booking_no); ?></h2>
              <h2 id="b_status">Booking status: <?php echo htmlspecialchars($status); ?></h2>

              <div class="details">
                <div class="details-left">
                    <div class="detail-row">
                        <label>Name:</label>
                        <input type="text" value="<?php echo htmlspecialchars($name); ?>" readonly>
                    </div>
                    <div class="detail-row">
                        <label>Date/Time:</label>
                        <input type="text" value="<?php echo htmlspecialchars($date_time); ?>" readonly>
                    </div>
                    <div class="detail-row">
                        <label>Amenity:</label>
                        <input type="text" value="<?php echo htmlspecialchars($amenity); ?>" readonly>
                    </div>
                    <div class="detail-row">
                        <label>Additionals:</label>
                        <input type="text" value="<?php echo htmlspecialchars($additionals); ?>" readonly>
                    </div>
                </div>
              
                <div class="details-right">
                    <div class="detail-row">
                        <label>Note:</label>
                        <textarea readonly><?php echo htmlspecialchars($note); ?></textarea>
                    </div>
                    <div class="detail-row">
                        <label>Amount:</label>
                        <input type="text" value="₱<?php echo number_format($amount, 2); ?>" readonly>
                    </div>
                </div>
              </div>
              
              <!-- New container for proof and receipt section -->
              <div class="proof-receipt-container">
                <div class="proof-section">
                  <label>Upload Your Proof of Payment:</label>
                  <div class="proof-image">
                    <img src="cloud_uploadd.png" alt="Upload Proof" id="proof-image">
                    <button class="close-btn">✖</button>
                  </div>
                  <div class="proof-buttons">
                    <button class="upload-btn">UPLOAD</button>
                    <button class="save-btn">SAVE</button>
                  </div>
                </div>
              
                <div class="receipt-section">
                  <label>Receipt:</label>
                  <img src="folderfile.png" alt="Receipt" id="receipt-image">
                </div>
              </div>
              
            </div>
        </div>  
    <?php elseif (isset($error_message)): ?>
        <div class="gap1">
            <div class="status_bg">
                <h2 style="color:red;"><?php echo htmlspecialchars($error_message); ?></h2>
            </div>
        </div>
    <?php endif; ?>

    <div class="footer">
        ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
        <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> | 
        <a href="#">Follow us</a>
    </div>
</body>
</html>
