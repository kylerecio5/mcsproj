<?php
session_start(); // Start the session
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
// Include your database connection
include 'db_connection.php';

// Fetch stickers data from the database
$query = "SELECT * FROM tbl_stickers";
$result = $conn->query($query);

// Check for any error in the query
if (!$result) {
    die("Error fetching stickers: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stickers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="stickers.css">
    
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
                <li><a href="stickers.php" class="active">Stickers</a></li>
                <li><a href="reservation.php">Reservations</a></li>
                <li><a href="c_permit.php">C Permit</a></li>
                <li><a href="parking.php">Parking</a></li>
                <li><a href="report.php">Report</a></li>
            </ul>
            <div class="sidebar-footer">
                <?php
                // Check if the role is 'Admin', show the link if true
                if ($_SESSION['user']['role'] == 'Admin') {
                    echo '<a id="to-admin-side" href="accounts.php">Go to Admin Side</a>';
                }
                ?>
                <a id="change_password">Change Password</a>
                <a id="logout" href="#">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>STICKERS</h1>
            </header>
            <div class="search-sort">
                <!-- JavaScript -->
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const searchBox = document.querySelector(".search-box");
                        const tableBody = document.querySelector(".resident-table tbody");

                        // Search functionality
                        searchBox.addEventListener("input", function () {
                            const searchTerm = searchBox.value.toLowerCase();
                            const rows = tableBody.querySelectorAll("tr");

                            rows.forEach(row => {
                                const rowText = row.innerText.toLowerCase();
                                if (rowText.includes(searchTerm)) {
                                    row.style.display = ""; // Show row
                                } else {
                                    row.style.display = "none"; // Hide row
                                }
                            });
                        });
                    });
                </script>

                <!-- Search and Filter Group -->
                <div class="search-filter-group">
                    <input type="text" placeholder="Search" class="search-box">
                    <select>
                        <option value="">Sort by</option>
                        <option value="NAME">NAME</option>
                        <option value="PHONE NO.">PHONE NO.</option>
                        <option value="DATE">DATE</option>
                        <option value="VEHICLE TYPE">VEHICLE TYPE</option>
                        <option value="PLATE NO.">PLATE NO.</option>
                        <option value="STICKER NO.">STICKER NO.</option>
                        <option value="AMOUNT">AMOUNT</option>
                    </select>
                    <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const sortByDropdown = document.querySelector('select:nth-of-type(1)');
                        const tableBody = document.querySelector(".resident-table tbody");

                        sortByDropdown.addEventListener("change", function () {
                            const sortByValue = sortByDropdown.value;
                            const rows = Array.from(tableBody.querySelectorAll("tr"));

                            if (sortByValue) {
                                rows.sort((rowA, rowB) => {
                                    const cellA = rowA.querySelector(`td:nth-child(${getColumnIndex(sortByValue)})`).innerText.trim();
                                    const cellB = rowB.querySelector(`td:nth-child(${getColumnIndex(sortByValue)})`).innerText.trim();

                                    if (sortByValue === "NAME" || sortByValue === "PHONE NO." || sortByValue === "VEHICLE TYPE" || sortByValue === "PLATE NO.") {
                                        return cellA.localeCompare(cellB); // For textual sorting
                                    } else if (sortByValue === "AMOUNT") {
                                        return parseFloat(cellA.replace('₱', '').replace(',', '')) - parseFloat(cellB.replace('₱', '').replace(',', '')); // For numerical sorting
                                    }
                                    return 0;
                                });

                                // Clear the table body and append sorted rows
                                tableBody.innerHTML = "";
                                rows.forEach(row => tableBody.appendChild(row));
                            }
                        });

                        // Function to get the column index based on the sort value
                        function getColumnIndex(sortBy) {
                            switch (sortBy) {
                                case "NAME":
                                    return 1;  // Name is in the 1st column (td:nth-child(1))
                                case "PHONE NO.":
                                    return 2;  // Phone No. is in the 2nd column (td:nth-child(2))
                                case "DATE":
                                    return 3;  // Date is in the 3rd column (td:nth-child(3))
                                case "VEHICLE TYPE":
                                    return 4;  // Vehicle Type is in the 4th column (td:nth-child(4))
                                case "PLATE NO.":
                                    return 5;  // Plate No. is in the 5th column (td:nth-child(5))
                                case "STICKER NO.":
                                    return 6;  // Sticker No. is in the 6th column (td:nth-child(6))
                                case "AMOUNT":
                                    return 7;  // Amount is in the 7th column (td:nth-child(7))
                                default:
                                    return 0;
                            }
                        }
                    });
                    </script>


                    <select id="vehicleTypeFilter">
                        <option value="">Vehicle Type</option>
                        <option value="SUV">SUV</option>
                        <option value="Sedan">Sedan</option>
                        <option value="Truck">Truck</option>
                        <option value="Motorcycle">Motorcycle</option>
                    </select>
                    <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const vehicleTypeFilter = document.getElementById("vehicleTypeFilter");
                        const tableBody = document.querySelector(".resident-table tbody");

                        // Vehicle Type filter functionality
                        vehicleTypeFilter.addEventListener("change", function () {
                            const selectedVehicleType = vehicleTypeFilter.value.toLowerCase(); // Get selected vehicle type
                            const rows = tableBody.querySelectorAll("tr");

                            rows.forEach(row => {
                                const vehicleTypeCell = row.querySelector("td:nth-child(4)").innerText.trim().toLowerCase(); // Vehicle Type column (4th column)
                                
                                // Check if the row's vehicle type matches the selected filter or if the filter is empty
                                if (selectedVehicleType === "" || vehicleTypeCell === selectedVehicleType) {
                                    row.style.display = ""; // Show row
                                } else {
                                    row.style.display = "none"; // Hide row
                                }
                            });
                        });
                    });
                    </script>

                </div>
            
                <!-- Add Button -->
                <button class="add-button">ADD +</button>
            </div>            
            <table class="resident-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PHONE NO.</th>
                        <th>DATE</th>
                        <th>VEHICLE TYPE</th>
                        <th>PLATE NO.</th>
                        <th>STICKER NO.</th>
                        <th>AMOUNT</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) {
                        // Split the NAME into first, middle, and last names
                        $nameParts = explode(" ", $row['NAME']);
                        $firstName = isset($nameParts[0]) ? $nameParts[0] : '';
                        $middleName = isset($nameParts[1]) ? $nameParts[1] : '';
                        $lastName = isset($nameParts[2]) ? $nameParts[2] : '';
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['NAME']); ?></td>
                            <td><?php echo htmlspecialchars($row['PHONE_NO']); ?></td>
                            <td><?php echo htmlspecialchars($row['DATE']); ?></td>
                            <td><?php echo htmlspecialchars($row['VehicleType']); ?></td>
                            <td><?php echo htmlspecialchars($row['PlateNum']); ?></td>
                            <td><?php echo htmlspecialchars($row['StickerNum']); ?></td>
                            <td>₱<?php echo number_format($row['Amount'], 2); ?></td>
                            <td>
                                <!-- Pass the first, middle, and last name parts as data attributes -->
                                <button class="edit" data-id="<?php echo htmlspecialchars($row['Sticker_ID']); ?>"
                                        data-first-name="<?php echo htmlspecialchars($firstName); ?>"
                                        data-middle-name="<?php echo htmlspecialchars($middleName); ?>"
                                        data-last-name="<?php echo htmlspecialchars($lastName); ?>"
                                        data-phone="<?php echo htmlspecialchars($row['PHONE_NO']); ?>"
                                        data-date="<?php echo htmlspecialchars($row['DATE']); ?>"
                                        data-vehicle-type="<?php echo htmlspecialchars($row['VehicleType']); ?>"
                                        data-plate-num="<?php echo htmlspecialchars($row['PlateNum']); ?>"
                                        data-sticker-num="<?php echo htmlspecialchars($row['StickerNum']); ?>"
                                        data-amount="<?php echo htmlspecialchars($row['Amount']); ?>">EDIT</button>
                                <!-- Remove button -->
                                <button class="remove" data-id="<?php echo htmlspecialchars($row['Sticker_ID']); ?>">ARCHIVE</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Add Sticker Popup -->
    <div id="add-sticker-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>ADD STICKER</h2>
            <form id="add-sticker-form">
                <div class="form-container">
                    <!-- Left Side Fields -->
                    <div class="left-fields">
                        <label>First Name:</label>
                        <input type="text" name="first_name" required>
                        
                        <label>Middle Name:</label>
                        <input type="text" name="middle_name">
                        
                        <label>Last Name:</label>
                        <input type="text" name="last_name" required>
                        
                        <label>Phone Number:</label>
                        <input type="text" name="phone_number" required>
                    </div>

                    <!-- Right Side Fields -->
                    <div class="right-fields">
                        <label>Vehicle Type:</label>
                        <select name="vehicle_type" required>
                            <option value="">Select...</option>
                            <option value="SUV">SUV</option>
                            <option value="Sedan">Sedan</option>
                            <option value="Truck">Truck</option>
                            <option value="Motorcycle">Motorcycle</option>
                        </select>
                        
                        <label>Plate Number:</label>
                        <input type="text" name="plate_number" required>

                        <label>Sticker Number:</label>
                        <input type="text" name="sticker_number" required>

                        <label>Amount:</label>
                        <input type="text" name="amount" required>
                    </div>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-add-sticker" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Sticker Popup -->
<div id="edit-sticker-popup" class="popup-overlay hidden">
    <div class="popup">
        <h2>EDIT STICKER</h2>
        <form id="edit-sticker-form">
            <div class="form-container">
                <!-- Hidden Sticker ID -->
                <input type="hidden" name="sticker_id" id="sticker_id">

                <!-- Left Side Fields -->
                <div class="left-fields">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>

                    <label>Middle Name:</label>
                    <input type="text" name="middle_name">

                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>

                    <label>Phone Number:</label>
                    <input type="text" name="phone_number" required>

                    <label>Date:</label>
                    <input type="date" name="date" required>
                </div>

                <!-- Right Side Fields -->
                <div class="right-fields">
                    <label>Vehicle Type:</label>
                    <select name="vehicle_type" required>
                        <option value="SUV">SUV</option>
                        <option value="Sedan">Sedan</option>
                        <option value="Truck">Truck</option>
                        <option value="Motorcycle">Motorcycle</option>
                    </select>

                    <label>Plate Number:</label>
                    <input type="text" name="plate_number" required>

                    <label>Sticker Number:</label>
                    <input type="text" name="sticker_number" required>

                    <label>Amount:</label>
                    <input type="text" name="amount" required>
                </div>
            </div>
            <div class="popup-buttons">
                <button type="button" id="cancel-edit-sticker" class="cancel-btn">CANCEL</button>
                <button type="submit" class="confirm-btn">SAVE</button>
            </div>
        </form>
    </div>
</div>

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

    <div id="logout-popup" class="popup-overlay hidden">
        <div class="popup">
            <p>ARE YOU SURE YOU WANT TO LOGOUT?</p>
            <div class="popup-buttons">
                <button id="cancel-logout" class="cancel-btn">NO</button>
                <button id="confirm-logout" class="confirm-btn">YES</button>
            </div>
        </div>
    </div> 
    <script src="stickers.js"></script>
</body>
</html>