<?php
session_start(); // Start the session
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Permit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="c_permit.css">
    <script src="c_permit.js"></script>
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
                <li><a href="c_permit.php" class="active">C Permit</a></li>
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
                <h1>CONSTRUCTION PERMIT</h1>
            </header>
            <div class="search-sort">
                <!-- Separate JavaScript for Search Functionality -->
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const tableBody = document.querySelector(".resident-table tbody");
                        const searchBox = document.querySelector(".search-box");

                        // Function to filter rows based on the search input
                        searchBox.addEventListener("input", () => {
                            const searchTerm = searchBox.value.toLowerCase();
                            const rows = tableBody.querySelectorAll("tr");

                            rows.forEach(row => {
                                const rowText = row.innerText.toLowerCase();
                                if (rowText.includes(searchTerm)) {
                                    row.style.display = ""; // Show row if it matches the search term
                                } else {
                                    row.style.display = "none"; // Hide row if it doesn't match
                                }
                            });
                        });
                    });
                </script>

                <div class="search-filter-group">
                    <input type="text" placeholder="Search" class="search-box">
                    <select>
                        <option value="">Sort by</option>
                        <option value="Name">Name</option>
                        <option value="Phone No.">Phone No.</option>
                        <option value="Building Type">Building Type</option>
                        <option value="Date">Date</option>
                        <option value="Amount">Amount</option>
                    </select>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                    const sortByDropdown = document.querySelector("select"); // Select the dropdown
                    const tableBody = document.querySelector(".resident-table tbody");

                    sortByDropdown.addEventListener("change", function () {
                        const sortByValue = sortByDropdown.value; // Get the selected sort option
                        const rows = Array.from(tableBody.querySelectorAll("tr"));

                        if (sortByValue) {
                            rows.sort((rowA, rowB) => {
                                let cellA = rowA.querySelector(`td:nth-child(${getColumnIndex(sortByValue)})`).innerText.trim();
                                let cellB = rowB.querySelector(`td:nth-child(${getColumnIndex(sortByValue)})`).innerText.trim();

                                // Sorting for text values (Name, Phone No., Building Type)
                                if (sortByValue === "Name" || sortByValue === "Phone No." || sortByValue === "Building Type") {
                                    return cellA.localeCompare(cellB);
                                } 
                                
                                // Sorting for numeric and date values (Amount, Date)
                                if (sortByValue === "Amount") {
                                    return parseFloat(cellA.replace('₱', '').replace(',', '')) - parseFloat(cellB.replace('₱', '').replace(',', ''));
                                }
                                if (sortByValue === "Date") {
                                    return new Date(cellA) - new Date(cellB);
                                }

                                return 0; // Default return if no match
                            });

                            // Clear the table body and append the sorted rows
                            tableBody.innerHTML = "";
                            rows.forEach(row => tableBody.appendChild(row));
                        }
                    });

                    // Function to get the column index based on the selected sort option
                    function getColumnIndex(sortBy) {
                        switch (sortBy) {
                            case "Name":
                                return 1;  // Name is in the 1st column
                            case "Phone No.":
                                return 2;  // Phone No. is in the 2nd column
                            case "Building Type":
                                return 3;  // Building Type is in the 3rd column
                            case "Date":
                                return 4;  // Date is in the 4th column
                            case "Amount":
                                return 9;  // Amount is in the 9th column
                            default:
                                return 0;
                        }
                    }
                });


                </script>
                </div>
                <button class="add-button">ADD +</button>
            </div>            
            <table class="resident-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>PHONE NO.</th>
                        <th>BUILDING TYPE</th>
                        <th>PERMIT NO.</th>
                        <th>DATE</th>
                        <th>BLOCK</th>
                        <th>LOT</th>
                        <th>STREET</th>
                        <th>AMOUNT</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Vladimir D. Leyson</td>
                        <td>09693476935</td>
                        <td>Apartment</td>
                        <td>ABC - 104039</td>
                        <td>06/05/2024</td>
                        <td>24</td>
                        <td>19</td>
                        <td>Sapphire Street</td>
                        <td>₱500.00</td>
                        <td>
                            <button class="edit">EDIT</button>
                            <button class="remove">REMOVE</button>
                        </td>
                    </tr>
                    <!-- Add other rows similarly -->
                </tbody>
            </table>
        </main>
    </div>

    <!-- Add Permit Popup -->
    <div id="add-permit-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>ADD PERMIT</h2>
            <form id="add-permit-form">
                <div class="form-container">
                    <div class="left-fields">
                        <label>First Name:</label>
                        <input type="text" id="first-name" required>
                        <label>Middle Name:</label>
                        <input type="text" id="middle-name">
                        <label>Last Name:</label>
                        <input type="text" id="last-name" required>
                        <label>Phone Number:</label>
                        <input type="text" id="phone-number" required>
                        <label>Building Type:</label>
                        <select id="building-type" required>
                            <option value="House">House</option>
                            <option value="Apartment">Apartment</option>
                        </select>
                    </div>
                    <div class="right-fields">
                        <label>Permit Number:</label>
                        <input type="text" id="permit-number" required>
                        <label>Block:</label>
                        <input type="text" id="block" required>
                        <label>Lot:</label>
                        <input type="text" id="lot" required>
                        <label>Street:</label>
                        <input type="text" id="street" required>
                        <label>Amount:</label>
                        <input type="text" id="amount" required>
                    </div>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-add-permit" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>
    
<!-- Edit Permit Popup -->
<!-- Edit Permit Popup -->
<div id="edit-permit-popup" class="popup-overlay hidden">
    <div class="popup">
        <h2>EDIT PERMIT</h2>
        <form id="edit-permit-form">
            <div class="form-container">
                <div class="left-fields">
                    <label for="edit-full-name">Full Name:</label>
                    <input type="text" id="edit-full-name" name="Name" required>
                
                    <label for="edit-phone-number">Phone Number:</label>
                    <input type="text" id="edit-phone-number" name="PhoneNo" required>

                    <label for="edit-building-type">Building Type:</label>
                    <select id="edit-building-type" name="BuildingType" required>
                        <option value="House">House</option>
                        <option value="Apartment">Apartment</option>
                    </select>
                </div>
                <div class="right-fields">
                    <label for="edit-permit-number">Permit Number:</label>
                    <input type="text" id="edit-permit-number" name="PermitNum" required>

                    <label for="edit-block">Block:</label>
                    <input type="text" id="edit-block" name="Block" required>

                    <label for="edit-lot">Lot:</label>
                    <input type="text" id="edit-lot" name="Lot" required>

                    <label for="edit-street">Street:</label>
                    <input type="text" id="edit-street" name="Street" required>

                    <label for="edit-amount">Amount:</label>
                    <input type="text" id="edit-amount" name="Amount" required>

                    <!-- Hidden input for Permit_ID -->
                    <input type="hidden" id="edit-permit-id" name="Permit_ID">
                </div>
            </div>
            <div class="popup-buttons">
                <button type="button" id="cancel-edit-permit" class="cancel-btn">CANCEL</button>
                <button type="submit" class="confirm-btn">CONFIRM</button>
            </div>
        </form>
    </div>
</div>



<!-- JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.querySelector(".resident-table tbody");
    const editPermitPopup = document.getElementById("edit-permit-popup");
    const cancelEditButton = document.getElementById("cancel-edit-permit");
    const editForm = document.getElementById("edit-permit-form");
    const cancelRemoveButton = document.getElementById("cancel-remove");
    const confirmRemoveButton = document.getElementById("confirm-remove");
    const removePopup = document.getElementById("remove-popup"); // The remove popup element
    let selectedPermitId = null; // Track selected permit for removal

    // Fetch and display the construction permits when the page loads
    fetchPermits();

    function fetchPermits() {
        fetch('fetch_permits.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Fetched permits data:", data); // Debugging the fetched data

                    tableBody.innerHTML = ''; // Clear existing rows

                    data.data.forEach(permit => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${permit.Name}</td>
                            <td>${permit.PhoneNo}</td>
                            <td>${permit.BuildingType}</td>
                            <td>${permit.PermitNum}</td>
                            <td>${permit.PermitDate}</td>
                            <td>${permit.Block}</td>
                            <td>${permit.Lot}</td>
                            <td>${permit.Street}</td>
                            <td>₱${parseFloat(permit.Amount).toFixed(2)}</td>
                            <td>
                                <button class="edit" data-id="${permit.Permit_ID}" data-name="${permit.Name}" data-phone="${permit.PhoneNo}" data-building-type="${permit.BuildingType}" data-permit-num="${permit.PermitNum}" data-block="${permit.Block}" data-lot="${permit.Lot}" data-street="${permit.Street}" data-amount="${permit.Amount}">EDIT</button>
                                <button class="remove" data-id="${permit.Permit_ID}">ARCHIVE</button>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });

                    // Attach event listeners to the edit buttons
                    document.querySelectorAll(".edit").forEach(button => {
                        button.addEventListener("click", function () {
                            const permitData = this.dataset;
                            console.log("Editing permit:", permitData);

                            // Populate the edit form fields
                            editForm.querySelector('[name="Name"]').value = permitData.name || '';
                            editForm.querySelector('[name="PhoneNo"]').value = permitData.phone || '';
                            editForm.querySelector('[name="BuildingType"]').value = permitData.buildingType || 'House';
                            editForm.querySelector('[name="PermitNum"]').value = permitData.permitNum || '';
                            editForm.querySelector('[name="Block"]').value = permitData.block || '';
                            editForm.querySelector('[name="Lot"]').value = permitData.lot || '';
                            editForm.querySelector('[name="Street"]').value = permitData.street || '';
                            editForm.querySelector('[name="Amount"]').value = permitData.amount || '';

                            const permitId = permitData.id;
                            if (permitId && permitId !== "undefined") {
                                console.log("Setting Permit_ID:", permitId);
                                editForm.querySelector('[name="Permit_ID"]').value = permitId;
                            } else {
                                console.error("Permit_ID is missing or invalid.");
                            }

                            editPermitPopup.classList.remove("hidden");
                        });
                    });

                    // Handle "REMOVE" button click
                    document.querySelectorAll(".remove").forEach(button => {
                        button.addEventListener("click", function () {
                            selectedPermitId = this.getAttribute("data-id"); // Store the permit ID
                            const permitName = this.closest("tr").querySelector("td:first-child").textContent;
                            console.log("Selected permit for removal:", selectedPermitId, permitName); // Debugging

                            if (selectedPermitId) {
                                removePopup.querySelector("h3").textContent = ` ${permitName}`;
                                removePopup.classList.remove("hidden"); // Show the remove confirmation popup
                            } else {
                                console.error("No Permit ID found for removal.");
                            }
                        });
                    });

                } else {
                    console.log("Error fetching data:", data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Form submission for updating permits
    editForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(editForm);
        console.log("Form data being sent:", [...formData]);

        fetch('update_permit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Response from update_permit.php:", data);

            if (data.success) {
                alert("Permit updated successfully!");
                fetchPermits(); // Refresh the table
                editPermitPopup.classList.add("hidden");
            } else {
                alert("Error updating permit: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("An error occurred while updating the permit.");
        });
    });

    // Close the edit popup on cancel
    cancelEditButton.addEventListener("click", () => {
        editPermitPopup.classList.add("hidden");
    });

    // Handle the removal of the permit (confirmed)
    confirmRemoveButton.addEventListener("click", () => {
        console.log("Confirmed removal for Permit ID:", selectedPermitId);

        fetch("remove_permit.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: selectedPermitId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Permit removed successfully!");
                fetchPermits();  // Reload the permits table
                removePopup.classList.add("hidden");
            } else {
                alert("Error removing permit.");
            }
        })
        .catch(error => {
            console.error('Error removing permit:', error);
            alert("An error occurred while removing the permit.");
        });
    });

    // Cancel removal action
    cancelRemoveButton.addEventListener("click", () => {
        removePopup.classList.add("hidden");
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

<div id="remove-popup" class="popup-overlay hidden">
    <div class="popup">
        <p>ARE YOU SURE YOU WANT TO REMOVE THIS CONSTRUCTION PERMIT?</p>
        <h3></h3> <!-- This will be updated dynamically with the resident's name -->
        <div class="popup-buttons">
            <button id="cancel-remove" class="cancel-btn">CANCEL</button>
            <button id="confirm-remove" class="confirm-btn">CONFIRM</button>
        </div>
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

</body>
</html>
