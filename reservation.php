<?php
session_start(); // Start the session
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

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

// Fetch reservations from the database
$sql = "SELECT reservation_id, F_name, M_name, L_name, phone_number, client_type, date, booking_no FROM tbl_reservations ORDER BY reservation_id DESC";
$result = $conn->query($sql);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="reservation.css">
    <script src="reservationn.js"></script>
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="citation_logo_big.png" alt="Subdivision Logo" class="logo">
                <?php
                // Check if the user is logged in and display their full name
                if (isset($_SESSION['user']['firstname']) && isset($_SESSION['user']['middlename']) && isset($_SESSION['user']['lastname'])) {
                    echo "<p>Hello, " . $_SESSION['user']['firstname'] . " " . $_SESSION['user']['middlename'] . " " . $_SESSION['user']['lastname'] . "!</p>";
                } else {
                    echo "<p>Hello, Guest!</p>";
                }
                ?>
            </div>
            <ul>
                <li><a href="residents.php">Residents</a></li>
                <li><a href="monthly_dues.php">Monthly Dues</a></li>
                <li><a href="stickers.php">Stickers</a></li>
                <li><a href="reservation.php" class="active">Reservations</a></li>
                <li><a href="c_permit.php">C Permit</a></li>
                <li><a href="parking.php">Parking</a></li>
                <li><a href="report.php">Report</a></li>
            </ul>
            <div class="sidebar-footer">
                <a id="to-admin-side" href="accounts.php">Go to Admin Side</a>
                <a id="change_password">Change Password</a>
                <a id="logout" href="#">Logout</a>
            </div> 
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>RESERVATIONS</h1>
            </header>
            <div class="search-sort">
                <!-- Search and Filter Group -->
                <div class="search-filter-group">
                    <input type="text" placeholder="Search" class="search-box">
                    <select>
                        <option>Sort by</option>
                        <!-- Additional sorting options -->
                    </select>
                    <select>
                        <option>Client type</option>
                        <!-- Additional filter options -->
                    </select>
                    <select>
                        <option>Status</option>
                        <!-- Additional filter options -->
                    </select>
                </div>
            </div>            
            <table class="resident-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>BOOKING NO.</th>
                        <th>PHONE NO.</th>
                        <th>CLIENT TYPE</th>
                        <th>RESERVATION DATE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are reservations
                    if ($result->num_rows > 0) {
                        // Loop through each reservation
                        while ($row = $result->fetch_assoc()) {
                            // Format the reservation date (assuming it's stored in 'Y-m-d' format)
                            $formatted_date = date('m/d/Y', strtotime($row['date']));
                            
                            // Hardcode status as "PENDING" for now
                            $status = "PENDING";
                            $status_class = strtolower($status); // Example: "pending"
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['F_name']) . ' ' . htmlspecialchars($row['M_name']) . ' ' . htmlspecialchars($row['L_name']); ?></td>
                                <td>#<?php echo htmlspecialchars($row['booking_no']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['client_type']); ?></td>
                                <td><?php echo $formatted_date; ?></td>
                                <td><span class="status <?php echo $status_class; ?>"><?php echo strtoupper($status); ?></span></td>
                                <td>
                                    <button class="view" data-id="<?php echo $row['reservation_id']; ?>">VIEW</button>
                                    <button class="update" data-id="<?php echo $row['reservation_id']; ?>">UPDATE</button>
                                    <button class="remove" data-id="<?php echo $row['reservation_id']; ?>">REMOVE</button>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7'>No reservations found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>

    <div id="update-reservation-popup" class="popup-overlay hidden">
    <div class="popup">
        <h2>UPDATE RESERVATION</h2>
        <button id="close-update-popup" class="close-btn">&#x2715;</button>
        <div class="status-buttons">
            <button class="status-button" id="status-void">VOID</button>
            <button class="status-button" id="status-paid">PAID</button>
            <button class="status-button" id="status-pending">PENDING</button>
        </div>
        <div class="receipt-section">
            <label for="receipt">Receipt:</label>
            <img id="receipt-image" src="receipt_placeholder.jpg" alt="Receipt Image">
            <div class="upload-save">
                <button id="upload-receipt" class="upload-btn">UPLOAD</button>
                <button id="save-receipt" class="save-btn">SAVE</button>
            </div>
        </div>
    </div>
</div>

   <!-- Reservation Details Popup -->
    <div id="reservation-details-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>RESERVATION DETAILS</h2>
            <div class="form-container">
                <div class="left-fields">
                    <label>Date:</label>
                    <input type="text" name="date" value="05/20/2024" readonly>
                    <label>Time:</label>
                    <input type="text" name="time" value="8:00am - 5:00pm" readonly>
                    <label>Amenity:</label>
                    <input type="text" name="amenity" value="Swimming pool" readonly>
                    <label>Additionals:</label>
                    <textarea name="additionals" readonly>Chair: 5
    Table: 2
    Karaoke: 1</textarea>
                </div>
                <div class="right-fields">
                    <label>Note:</label>
                    <!-- Added name="note" here -->
                    <textarea name="note" readonly>None</textarea>
                    <label>Amount:</label>
                    <input type="text" name="amount" value="₱6,000.00" readonly>
                </div>
            </div>
            <h3>CUSTOMER PROOF:</h3>
            <div class="proof-container">
                <img src="receipt_placeholder.jpg" alt="Customer Proof">
            </div>
            <div class="popup-buttons">
                <button type="button" id="close-reservation-details" class="cancel-btn">CLOSE</button>
                <button type="button" id="print-reservation-details" class="confirm-btn">PRINT</button>
            </div>
        </div>
    </div>

    <!-- Remove Reservation Popup -->
    <div id="remove-account-popup" class="popup-overlay hidden">
        <div class="popup">
            <p>ARE YOU SURE YOU WANT TO REMOVE THIS RESERVATION?</p>
            <p id="account-name"></p>
            <div class="popup-buttons">
                <button id="cancel-remove" class="cancel-btn">CANCEL</button>
                <button id="confirm-remove" class="confirm-btn">CONFIRM</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // View Reservation
        const viewButtons = document.querySelectorAll(".view");
        const reservationDetailsPopup = document.getElementById("reservation-details-popup");
        const closeViewButton = document.getElementById("close-reservation-details");

        viewButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                const reservationId = e.target.getAttribute('data-id');
                console.log(`View Reservation for ID: ${reservationId}`);

                // Fetch reservation details via AJAX
                fetch(`view_reservation.php?reservation_id=${reservationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error); // Handle error
                        } else {
                            // Populate the popup with the reservation data
                            reservationDetailsPopup.querySelector('input[name="date"]').value = data.date;
                            reservationDetailsPopup.querySelector('input[name="time"]').value = data.time;
                            reservationDetailsPopup.querySelector('input[name="amenity"]').value = data.amenity;
                            reservationDetailsPopup.querySelector('textarea[name="additionals"]').value = data.additionals;
                            reservationDetailsPopup.querySelector('textarea[name="note"]').value = data.note;
                            reservationDetailsPopup.querySelector('input[name="amount"]').value = data.amount;
                            reservationDetailsPopup.querySelector('.proof-container img').src = data.receipt_image_url;
                            reservationDetailsPopup.classList.remove("hidden");
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching reservation data:", error);
                    });
            });
        });

        // Close the reservation details popup
        closeViewButton.addEventListener("click", () => {
            reservationDetailsPopup.classList.add("hidden");
        });

        // Update Reservation
        const updateButtons = document.querySelectorAll(".update");
        const updatePopup = document.getElementById("update-reservation-popup");
        const closeUpdatePopupButton = document.getElementById("close-update-popup");

        updateButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                const reservationId = e.target.getAttribute('data-id');
                console.log(`Update Reservation for ID: ${reservationId}`);
                updatePopup.classList.remove("hidden");
            });
        });

        closeUpdatePopupButton.addEventListener("click", () => {
            updatePopup.classList.add("hidden");
        });

        // Handle status change buttons in Update popup
        const statusButtons = document.querySelectorAll(".status-button");
        statusButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                const status = e.target.id.split('-')[1].toUpperCase();
                console.log(`Changing reservation status to ${status}`);
                // Handle the status change logic here
            });
        });

        // Remove Reservation
        const removeButtons = document.querySelectorAll(".remove");
        const removePopup = document.getElementById("remove-account-popup");
        const cancelRemoveButton = document.getElementById("cancel-remove");
        const confirmRemoveButton = document.getElementById("confirm-remove");

        let currentReservationId = null;

        removeButtons.forEach(button => {
            button.addEventListener("click", (e) => {
                currentReservationId = e.target.getAttribute('data-id');
                console.log(`Remove Reservation for ID: ${currentReservationId}`);
                document.getElementById("account-name").innerText = `Reservation #${currentReservationId}`;
                removePopup.classList.remove("hidden");
            });
        });

        cancelRemoveButton.addEventListener("click", () => {
            removePopup.classList.add("hidden");
            currentReservationId = null;
        });

        confirmRemoveButton.addEventListener("click", () => {
            if (!currentReservationId) return;

            console.log(`Confirming Removal for ID: ${currentReservationId}`);

            // Send an AJAX request to delete the reservation
            fetch(`remove_reservation.php?reservation_id=${currentReservationId}`, {
                method: "POST"
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Reservation removed successfully!");
                        location.reload(); // Reload the page to reflect the changes
                    } else {
                        alert(`Failed to remove reservation: ${data.error}`);
                    }
                })
                .catch(error => {
                    console.error("Error removing reservation:", error);
                })
                .finally(() => {
                    removePopup.classList.add("hidden");
                    currentReservationId = null;
                });
        });

        // Print Reservation Details
        document.getElementById("print-reservation-details").addEventListener("click", function() {
        // Open a new window for printing
        var printWindow = window.open('', '', 'height=500, width=800');
        
        // Copy the content of the reservation details popup into the print window
        var content = document.querySelector("#reservation-details-popup .popup").innerHTML;

        // Define the CSS styles for printing
        var printStyles = `
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: white;
                }
                .popup {
                    width: 100%;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ccc;
                    box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
                }
                .popup h2 {
                    text-align: center;
                    font-size: 24px;
                }
                .form-container {
                    display: flex;
                    justify-content: space-between;
                }
                .form-container label {
                    font-weight: bold;
                }
                .form-container input,
                .form-container textarea {
                    width: 45%;
                    margin-bottom: 15px;
                    padding: 8px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }
                .proof-container img {
                    max-width: 100%;
                    height: auto;
                    display: block;
                    margin: 20px auto;
                }
                .popup-buttons {
                    text-align: center;
                }
                .popup-buttons button {
                    margin: 10px;
                    padding: 10px 20px;
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    cursor: pointer;
                    font-size: 16px;
                    border-radius: 5px;
                }
                .popup-buttons button:hover {
                    background-color: #45a049;
                }
            </style>
        `;
        
        // Write the content and styles to the print window
        printWindow.document.write('<html><head><title>Print Reservation Details</title>' + printStyles + '</head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        
        // Close the document to trigger the print dialog
        printWindow.document.close(); 
        
        // Trigger the print dialog
        printWindow.print();
    });

    });
</script>



    <!-- Change Password Self Popup -->
    <div id="change-password-self-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>CHANGE PASSWORD</h2>
            <form id="change-password-self-form">
                <div class="form-container">
                    <label>New Password:</label>
                    <div class="password-container">
                        <input type="password" name="new_password" id="new-password-self" required>
                        <i class="fa fa-eye" id="toggle-password-self" onclick="togglePasswordSelf()"></i>
                    </div>
                    
                    <label>Confirm New Password:</label>
                    <div class="password-container">
                        <input type="password" name="confirm_password" id="confirm-password-self" required>
                        <i class="fa fa-eye" id="toggle-confirm-password-self" onclick="toggleConfirmPasswordSelf()"></i>
                    </div>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-change-password-self" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">SAVE</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Logout Confirmation Popup -->
    <div id="logout-popup" class="popup-overlay hidden">
        <div class="popup">
            <p>ARE YOU SURE YOU WANT TO LOGOUT?</p>
            <div class="popup-buttons">
                <button id="cancel-logout" class="cancel-btn">NO</button>
                <button id="confirm-logout" class="confirm-btn">YES</button>
            </div>
        </div>
    </div>
</body>
</html>
