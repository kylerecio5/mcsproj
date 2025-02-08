<?php
session_start(); // Start the session
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
// Include the database connection
include('db_connection.php');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="parking.css">
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
                <li><a href="reservation.php">Reservations</a></li>
                <li><a href="c_permit.php">C Permit</a></li>
                <li><a href="parking.php" class="active">Parking</a></li>
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
                <h1>PARKING</h1>
            </header>
            <div class="search-sort">
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const searchBox = document.querySelector(".search-box"); // Select the search input field
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

                <div class="search-filter-group">
                    <input type="text" placeholder="Search" class="search-box">
                    <select id="sort-by">
                        <option>Sort by</option>
                        <option value="name">Name</option>
                        <option value="phone">Phone No.</option>
                        <option value="parkingType">Parking Type</option>
                        <option value="plateNo">Plate No.</option>
                        <option value="vehicleType">Vehicle Type</option>
                        <option value="date">Date</option>
                        <option value="amount">Amount</option>
                    </select>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                        const sortBySelect = document.getElementById("sort-by"); // Select the sort by dropdown
                        const tableBody = document.querySelector(".resident-table tbody"); // Select the table body

                        // Sort functionality
                        sortBySelect.addEventListener("change", function () {
                            const sortBy = sortBySelect.value; // Get selected sort option
                            const rows = Array.from(tableBody.querySelectorAll("tr")); // Get all table rows
                            let sortedRows;

                            switch (sortBy) {
                                case "name":
                                    sortedRows = rows.sort((a, b) => {
                                        const nameA = a.cells[0].textContent.trim().toLowerCase();
                                        const nameB = b.cells[0].textContent.trim().toLowerCase();
                                        return nameA.localeCompare(nameB);
                                    });
                                    break;
                                case "phone":
                                    sortedRows = rows.sort((a, b) => {
                                        const phoneA = a.cells[1].textContent.trim();
                                        const phoneB = b.cells[1].textContent.trim();
                                        return phoneA.localeCompare(phoneB);
                                    });
                                    break;
                                case "parkingType":
                                    sortedRows = rows.sort((a, b) => {
                                        const parkingTypeA = a.cells[2].textContent.trim().toLowerCase();
                                        const parkingTypeB = b.cells[2].textContent.trim().toLowerCase();
                                        return parkingTypeA.localeCompare(parkingTypeB);
                                    });
                                    break;
                                case "plateNo":
                                    sortedRows = rows.sort((a, b) => {
                                        const plateNoA = a.cells[3].textContent.trim().toLowerCase();
                                        const plateNoB = b.cells[3].textContent.trim().toLowerCase();
                                        return plateNoA.localeCompare(plateNoB);
                                    });
                                    break;
                                case "vehicleType":
                                    sortedRows = rows.sort((a, b) => {
                                        const vehicleTypeA = a.cells[4].textContent.trim().toLowerCase();
                                        const vehicleTypeB = b.cells[4].textContent.trim().toLowerCase();
                                        return vehicleTypeA.localeCompare(vehicleTypeB);
                                    });
                                    break;
                                case "date":
                                    sortedRows = rows.sort((a, b) => {
                                        const dateA = new Date(a.cells[5].textContent.trim());
                                        const dateB = new Date(b.cells[5].textContent.trim());
                                        return dateA - dateB;
                                    });
                                    break;
                                case "amount":
                                    sortedRows = rows.sort((a, b) => {
                                        const amountA = parseFloat(a.cells[6].textContent.trim().replace("₱", "").replace(",", ""));
                                        const amountB = parseFloat(b.cells[6].textContent.trim().replace("₱", "").replace(",", ""));
                                        return amountA - amountB;
                                    });
                                    break;
                                default:
                                    sortedRows = rows;
                                    break;
                            }

                            // Append sorted rows back to the table body
                            sortedRows.forEach(row => tableBody.appendChild(row));
                        });
                    });

                    </script>


                    <select id="parking-type-sort">
                        <option value="">Parking Type</option>
                        <option value="Hourly">Hourly</option>
                        <option value="Monthly">Monthly</option>
                    </select>
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                        const parkingTypeSort = document.getElementById("parking-type-sort"); // Select the Parking Type dropdown
                        const tableBody = document.querySelector(".resident-table tbody"); // Select the table body

                        // Parking Type sorting functionality
                        parkingTypeSort.addEventListener("change", function () {
                            const parkingType = parkingTypeSort.value; // Get selected parking type (Hourly or Monthly)
                            const rows = Array.from(tableBody.querySelectorAll("tr")); // Get all table rows
                            let sortedRows;

                            if (parkingType) {
                                sortedRows = rows.filter(row => {
                                    const rowParkingType = row.cells[2].textContent.trim().toLowerCase();
                                    return rowParkingType === parkingType.toLowerCase();
                                });
                            } else {
                                sortedRows = rows;
                            }

                            // Append filtered rows (or all rows if no selection) back to the table body
                            sortedRows.forEach(row => tableBody.appendChild(row));
                        });
                    });

                        </script>
                    <select id="vehicle-type-sort">
                        <option value="">Vehicle Type</option>
                        <option value="SUV">SUV</option>
                        <option value="Sedan">Sedan</option>
                        <option value="Truck">Truck</option>
                        <option value="Motorcycle">Motorcycle</option>
                    </select>
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                        const vehicleTypeSort = document.getElementById("vehicle-type-sort"); // Select the Vehicle Type dropdown
                        const tableBody = document.querySelector(".resident-table tbody"); // Select the table body

                        // Vehicle Type sorting functionality
                        vehicleTypeSort.addEventListener("change", function () {
                            const vehicleType = vehicleTypeSort.value; // Get selected vehicle type (SUV, Sedan, etc.)
                            const rows = Array.from(tableBody.querySelectorAll("tr")); // Get all table rows
                            let sortedRows;

                            if (vehicleType) {
                                sortedRows = rows.filter(row => {
                                    const rowVehicleType = row.cells[4].textContent.trim().toLowerCase(); // Vehicle type is in column 5 (index 4)
                                    return rowVehicleType === vehicleType.toLowerCase();
                                });
                            } else {
                                sortedRows = rows; // If no selection, show all rows
                            }

                            // Append filtered rows (or all rows if no selection) back to the table body
                            sortedRows.forEach(row => tableBody.appendChild(row));
                        });
                    });

                    </script>

                </div>
                <button class="add-button">ADD +</button>
            </div>            

            <!-- Parking Table -->
            <table class="resident-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PHONE NO.</th>
                        <th>PARKING TYPE</th>
                        <th>PLATE NO.</th>
                        <th>VEHICLE TYPE</th>
                        <th>DATE</th>
                        <th>AMOUNT</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </main>
    </div>

    <!-- Add Parking Popup -->
    <div id="add-parking-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>ADD PARKING</h2>
            <form id="add-parking-form">
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

                        <label>Parking Type:</label>
                        <select name="parking_type" required>
                            <option value="">Select...</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Hourly">Hourly</option>
                        </select>

                        <label>Amount:</label>
                        <input type="text" name="amount" required>
                    </div>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-add-parking" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>

<!-- Add Parking Popup -->
    <div id="add-parking-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>ADD PARKING</h2>
            <form id="add-parking-form">
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

                        <label>Parking Type:</label>
                        <select name="parking_type" required>
                            <option value="">Select...</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Hourly">Hourly</option>
                        </select>

                        <label>Amount:</label>
                        <input type="text" name="amount" required>
                    </div>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-add-parking" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>

    
    
    <!-- Edit Parking Popup -->
    <div id="edit-parking-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>EDIT PARKING</h2>
            <form id="edit-parking-form">
                <div class="form-container">
                    <!-- Left Side Fields -->
                    <div class="left-fields">
                        <label for="edit-full-name">Full Name:</label>
                        <input type="text" id="edit-full-name" name="Name" required>

                        <label for="edit-phone-number">Phone Number:</label>
                        <input type="text" id="edit-phone-number" name="PhoneNo" required>
                    </div>

                    <!-- Right Side Fields -->
                    <div class="right-fields">
                        <label for="edit-vehicle-type">Vehicle Type:</label>
                        <select id="edit-vehicle-type" name="vehicle_type" required>
                            <option value="">Select...</option>
                            <option value="SUV">SUV</option>
                            <option value="Sedan">Sedan</option>
                            <option value="Truck">Truck</option>
                            <option value="Motorcycle">Motorcycle</option>
                        </select>

                        <label for="edit-plate-number">Plate Number:</label>
                        <input type="text" id="edit-plate-number" name="Plate_No" required>

                        <label for="edit-parking-type">Parking Type:</label>
                        <select id="edit-parking-type" name="parking_type" required>
                            <option value="">Select...</option>
                            <option value="Monthly">Monthly</option>
                            <option value="Hourly">Hourly</option>
                        </select>

                        <label for="edit-amount">Amount:</label>
                        <input type="text" id="edit-amount" name="Amount" required>
                    </div>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-edit-parking" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector(".resident-table tbody");
    const editParkingPopup = document.getElementById("edit-parking-popup");
    const cancelEditButton = document.getElementById("cancel-edit-parking");
    const editForm = document.getElementById("edit-parking-form");

    const removePopup = document.getElementById("remove-popup");
    const removeName = document.getElementById("remove-name");
    const cancelRemoveButton = document.getElementById("cancel-remove");
    const confirmRemoveButton = document.getElementById("confirm-remove");

    let parkingToRemove = null;

    // Fetch and display the parking data when the page loads
    fetchParkingData();

    function fetchParkingData() {
        fetch('fetch_parking_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    tableBody.innerHTML = ''; // Clear any existing rows

                    data.data.forEach(parking => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${parking.Name}</td>
                            <td>${parking.PhoneNo}</td>
                            <td>${parking.ParkingType}</td>
                            <td>${parking.PlateNo}</td>
                            <td>${parking.VehicleType}</td>
                            <td>${parking.Date || 'N/A'}</td>
                            <td>₱${parseFloat(parking.Amount).toFixed(2)}</td>
                            <td>
                                <button class="edit" data-id="${parking.PlateNo}" data-name="${parking.Name}" data-phone="${parking.PhoneNo}" data-vehicle-type="${parking.VehicleType}" data-plate-no="${parking.PlateNo}" data-parking-type="${parking.ParkingType}" data-amount="${parking.Amount}">EDIT</button>
                                <button class="remove" data-id="${parking.Name}" data-name="${parking.Name}">ARCHIVE</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });

                    // Set up the edit button functionality
                    document.querySelectorAll(".edit").forEach(button => {
                        button.addEventListener("click", function () {
                            const parkingData = this.dataset;
                            resetEditForm();
                            editForm.querySelector('[name="Name"]').value = parkingData.name || '';
                            editForm.querySelector('[name="PhoneNo"]').value = parkingData.phone || '';
                            editForm.querySelector('[name="vehicle_type"]').value = parkingData.vehicleType || '';
                            editForm.querySelector('[name="Plate_No"]').value = parkingData.plateNo || '';
                            editForm.querySelector('[name="parking_type"]').value = parkingData.parkingType || '';
                            editForm.querySelector('[name="Amount"]').value = parkingData.amount || '';

                            editParkingPopup.classList.remove("hidden");
                        });
                    });

                    // Set up the remove button functionality
                    document.querySelectorAll(".remove").forEach(button => {
                        button.addEventListener("click", function () {
                            const parkingData = this.dataset;
                            parkingToRemove = parkingData.id;
                            removeName.textContent = `${parkingData.name}`;
                            removePopup.classList.remove("hidden");
                        });
                    });
                } else {
                    console.log("Error:", data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Reset form fields to clear any previous invalid state
    function resetEditForm() {
        editForm.reset(); // Resets all fields
    }

    // Handle the form submission for updating parking data
    editForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(editForm);

        fetch('update_parking.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Parking updated successfully!");
                    fetchParkingData(); // Refresh the table
                    editParkingPopup.classList.add("hidden");
                } else {
                    alert("Error updating parking: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred while updating parking.");
            });
    });

    // Handle the cancel button click to hide the popup
    if (cancelEditButton) {
        cancelEditButton.addEventListener("click", () => {
            editParkingPopup.classList.add("hidden");
        });
    }

    // Handle the cancel button click to hide the remove popup
    cancelRemoveButton.addEventListener("click", () => {
        removePopup.classList.add("hidden");
    });

    // Handle the confirm button click to remove parking
    confirmRemoveButton.addEventListener("click", () => {
        if (parkingToRemove) {
            fetch('remove_parking.php', {
                method: 'POST',  // Changed from GET to POST
                headers: {
                    'Content-Type': 'application/json'  // Sending JSON data
                },
                body: JSON.stringify({ id: parkingToRemove })  // Send the parking Name as JSON
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Parking removed successfully!");
                        fetchParkingData(); // Refresh the table
                        removePopup.classList.add("hidden");
                    } else {
                        alert("Error removing parking: " + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred while removing parking.");
                });
        }
    });
});



</script>


<div id="remove-popup" class="popup-overlay hidden">
    <div class="popup">
        <p>ARE YOU SURE YOU WANT TO REMOVE THIS PARKING INFORMATION?</p>
        <h3 id="remove-name"></h3> <!-- Ensure this element exists -->
        <div class="popup-buttons">
            <button id="cancel-remove" class="cancel-btn">CANCEL</button>
            <button id="confirm-remove" class="confirm-btn">CONFIRM</button>
        </div>
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

    <script src="parking.js"></script>
</body>
</html>
