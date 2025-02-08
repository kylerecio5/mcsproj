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
    <title>Residents</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="residents.css">
    <script src="residents.js"></script>
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
                <li><a href="residents.php" class="active">Residents</a></li>
                <li><a href="monthly_dues.php">Monthly Dues</a></li>
                <li><a href="stickers.php">Stickers</a></li>
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
                <h1>RESIDENTS</h1>
            </header>
            <div class="search-sort">
            <script>
                // This script will handle the search functionality
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
                    <select id="sortBy">
                        <option value="">Sort by</option>
                        <option value="Name">Name</option>
                        <option value="Phone No.">Phone No.</option>
                        <option value="Age">Age</option>
                    </select>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const sortByDropdown = document.getElementById("sortBy");
                            const tableBody = document.querySelector(".resident-table tbody");

                            sortByDropdown.addEventListener("change", function () {
                                const sortByValue = sortByDropdown.value;
                                const rows = Array.from(tableBody.querySelectorAll("tr"));

                                if (sortByValue) {
                                    rows.sort((rowA, rowB) => {
                                        const cellA = rowA.querySelector(`td:nth-child(${getColumnIndex(sortByValue)})`).innerText.trim();
                                        const cellB = rowB.querySelector(`td:nth-child(${getColumnIndex(sortByValue)})`).innerText.trim();
                                        
                                        if (sortByValue === "Name" || sortByValue === "Phone No.") {
                                            return cellA.localeCompare(cellB);
                                        } else if (sortByValue === "Age") {
                                            return parseInt(cellA) - parseInt(cellB);
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
                                    case "Name":
                                        return 1;  // Name is in the 1st column (td:nth-child(1))
                                    case "Phone No.":
                                        return 2;  // Phone No. is in the 2nd column (td:nth-child(2))
                                    case "Age":
                                        return 5;  // Age is in the 5th column (td:nth-child(5))
                                    default:
                                        return 0;
                                }
                            }
                        });
                        </script>
                        <select name="member-type-filter">
                            <option value="">Member Type</option>
                            <option value="Homeowner">Homeowner</option>
                            <option value="Tenant">Tenant</option>
                        </select>
                        <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const tableBody = document.querySelector(".resident-table tbody");
                            const memberTypeFilter = document.querySelector("[name='member-type-filter']"); // Member type filter dropdown

                            // Filter residents based on the selected member type
                            memberTypeFilter.addEventListener("change", function() {
                                const selectedMemberType = memberTypeFilter.value.toLowerCase();
                                const rows = tableBody.querySelectorAll("tr");

                                rows.forEach(row => {
                                    const memberTypeCell = row.querySelector("td:nth-child(3)").innerText.toLowerCase(); // Member type is in the 3rd column
                                    
                                    if (selectedMemberType === "" || memberTypeCell === selectedMemberType) {
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
                <!-- Resident Table -->
                <table class="resident-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Resident Code</th>
                            <th>Phone No.</th>
                            <th>Member Type</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Block</th>
                            <th>Lot</th>
                            <th>Street</th>
                            <th>Membership</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic content will be added here -->
                    </tbody>
                </table>
                </main>
    </div>
<!-- Add this HTML markup for the popup -->

<div id="add-resident-popup" class="popup-overlay hidden">
    <div class="popup add-resident">
        <h2>ADD RESIDENT</h2>
        <form id="add-resident-form">
            <div class="form-container">
                <div class="left-column">
                    <label for="first-name">First name:</label>
                    <input type="text" id="first-name" name="first-name" required>
                    <label for="middle-name">Middle name:</label>
                    <input type="text" id="middle-name" name="middle-name">
                    <label for="last-name">Last name:</label>
                    <input type="text" id="last-name" name="last-name" required>
                    <label for="phone-number">Phone number:</label>
                    <input type="text" id="phone-number" name="phone-number" required>
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" min="0" required>
                    <label for="sex">Sex:</label>
                    <select id="sex" name="sex">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="right-column">
                    <label for="member-type">Member type:</label>
                    <select id="member-type" name="member-type">
                        <option value="Homeowner">Homeowner</option>
                        <option value="Tenant">Tenant</option>
                    </select>
                    <label for="membership-fee">Membership fee:</label>
                    <select id="membership-fee" name="membership-fee">
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                    </select>
                    <label for="block">Block:</label>
                    <input type="text" id="block" name="block" required>
                    <label for="lot">Lot:</label>
                    <input type="text" id="lot" name="lot" required>
                    <label for="street">Street:</label>
                    <input type="text" id="street" name="street" required>
                </div>
            </div>
            <div class="popup-buttons">
                <button type="button" id="cancel-add" class="cancel-btn">CANCEL</button>
                <button type="submit" id="confirm-add" class="confirm-btn">CONFIRM</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Resident Popup -->
<div id="edit-resident-popup" class="popup-overlay hidden">
    <div class="popup edit-resident">
        <h2>EDIT RESIDENT</h2>
        <form id="edit-resident-form">
            <div class="form-container">
                <div class="left-column">
                    <label for="edit-first-name">First name:</label>
                    <input type="text" id="edit-first-name" name="F_name" required>

                    <label for="edit-middle-name">Middle name:</label>
                    <input type="text" id="edit-middle-name" name="M_name">

                    <label for="edit-last-name">Last name:</label>
                    <input type="text" id="edit-last-name" name="L_name" required>

                    <label for="edit-phone-number">Phone number:</label>
                    <input type="text" id="edit-phone-number" name="PhoneNum" required>

                    <label for="edit-age">Age:</label>
                    <input type="number" id="edit-age" name="Age" min="0" required>

                    <label for="edit-sex">Sex:</label>
                    <select id="edit-sex" name="Sex">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="right-column">
                    <label for="edit-member-type">Member type:</label>
                    <select id="edit-member-type" name="MemberType">
                        <option value="Homeowner">Homeowner</option>
                        <option value="Tenant">Tenant</option>
                    </select>

                    <label for="edit-membership-fee">Membership fee:</label>
                    <select id="edit-membership-fee" name="Membership">
                        <option value="Paid">Paid</option>
                        <option value="Unpaid">Unpaid</option>
                    </select>

                    <label for="edit-block">Block:</label>
                    <input type="text" id="edit-block" name="Block" required>

                    <label for="edit-lot">Lot:</label>
                    <input type="text" id="edit-lot" name="Lot" required>

                    <label for="edit-street">Street:</label>
                    <input type="text" id="edit-street" name="Street" required>
                </div>
            </div>
            <!-- Hidden Resident_ID -->
            <input type="hidden" name="Resident_ID" id="resident_id">
            <div class="popup-buttons">
                <button type="button" id="cancel-edit" class="cancel-btn">CANCEL</button>
                <button type="submit" id="confirm-edit" class="confirm-btn">SAVE</button>
            </div>
        </form>
    </div>
</div>



<!-- JavaScript -->
 <script>
document.addEventListener("DOMContentLoaded", function () {
    
    const tableBody = document.querySelector(".resident-table tbody");
    const editResidentPopup = document.getElementById("edit-resident-popup");
    const cancelEditButton = document.getElementById("cancel-edit");
    const editForm = document.getElementById("edit-resident-form");
    const removePopup = document.getElementById("remove-popup");
    const cancelRemoveButton = document.getElementById("cancel-remove");
    const confirmRemoveButton = document.getElementById("confirm-remove");
    const addResidentPopup = document.getElementById("add-resident-popup");
    const cancelAddButton = document.getElementById("cancel-add");
    const addForm = document.getElementById("add-resident-form");
    let selectedResidentId = null;

    
    // Function to load residents from the database
    function loadResidents() {
        fetch("fetch_residents.php")
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = ""; // Clear existing rows

                data.forEach(resident => {
                    const phone = resident.PhoneNum || 'No phone number';
                    const row = document.createElement("tr");

                    row.innerHTML = `
                        <td>${resident.name}</td>
                        <td>${resident.residentcode}</td>
                        <td>${phone}</td>
                        <td>${resident.member_type}</td>
                        <td>${resident.sex}</td>
                        <td>${resident.age}</td>
                        <td>${resident.block}</td>
                        <td>${resident.lot}</td>
                        <td>${resident.street}</td>
                        <td><span class="status ${resident.membership.toLowerCase()}">${resident.membership}</span></td>
                        <td>
                            <button class="edit" 
                                data-id="${resident.id}" 
                                data-first-name="${resident.first_name}" 
                                data-middle-name="${resident.middle_name}" 
                                data-last-name="${resident.last_name}" 
                                data-phone="${resident.PhoneNum}" 
                                data-age="${resident.age}" 
                                data-sex="${resident.sex}" 
                                data-member-type="${resident.member_type}" 
                                data-membership-fee="${resident.membership}" 
                                data-block="${resident.block}" 
                                data-lot="${resident.lot}" 
                                data-street="${resident.street}">EDIT</button>
                            <button class="remove" 
                                data-id="${resident.id}" 
                                data-first-name="${resident.first_name}" 
                                data-middle-name="${resident.middle_name}" 
                                data-last-name="${resident.last_name}">ARCHIVE</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                // Add edit event listeners
                document.querySelectorAll(".edit").forEach(button => {
                    button.addEventListener("click", function () {
                        // Populate the edit form fields
                        editForm.querySelector('[name="Resident_ID"]').value = this.getAttribute('data-id');
                        editForm.querySelector('[name="F_name"]').value = this.getAttribute('data-first-name') || '';
                        editForm.querySelector('[name="M_name"]').value = this.getAttribute('data-middle-name') || '';
                        editForm.querySelector('[name="L_name"]').value = this.getAttribute('data-last-name') || '';
                        editForm.querySelector('[name="PhoneNum"]').value = this.getAttribute('data-phone') || '';
                        editForm.querySelector('[name="Age"]').value = this.getAttribute('data-age') || '';
                        editForm.querySelector('[name="Sex"]').value = this.getAttribute('data-sex') || 'Male';
                        editForm.querySelector('[name="MemberType"]').value = this.getAttribute('data-member-type') || 'Homeowner';
                        editForm.querySelector('[name="Membership"]').value = this.getAttribute('data-membership-fee') || 'Unpaid';
                        editForm.querySelector('[name="Block"]').value = this.getAttribute('data-block') || '';
                        editForm.querySelector('[name="Lot"]').value = this.getAttribute('data-lot') || '';
                        editForm.querySelector('[name="Street"]').value = this.getAttribute('data-street') || '';

                        editResidentPopup.classList.remove("hidden");
                    });
                });

                // Add remove event listeners
                document.querySelectorAll(".remove").forEach(button => {
                    button.addEventListener("click", function () {
                        // Capture the full name of the resident to be removed
                        const residentName = `${this.getAttribute('data-first-name')} ${this.getAttribute('data-middle-name')} ${this.getAttribute('data-last-name')}`;
                        selectedResidentId = this.getAttribute('data-id');

                        // Update the popup with the resident's name
                        document.querySelector("#remove-popup h3").textContent = ` ${residentName}?`;

                        // Show the remove confirmation popup
                        removePopup.classList.remove("hidden");
                    });
                });
            })
            .catch(error => console.error('Error fetching residents:', error));
    }

    // Cancel edit popup
    cancelEditButton.addEventListener("click", () => {
        editResidentPopup.classList.add("hidden");
    });

    // Handle form submission for updating a resident
    editForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(editForm);
        fetch("update_resident.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Resident updated successfully!");
                loadResidents();  // Reload the residents table
                editResidentPopup.classList.add("hidden");
            } else {
                alert("Error updating resident.");
            }
        })
        .catch(error => {
            console.error('Error updating resident:', error);
            alert("An error occurred while updating the resident.");
        });
    });

    // Handle removal of the resident
    confirmRemoveButton.addEventListener("click", () => {
        fetch("remove_resident.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: selectedResidentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Resident removed successfully!");
                loadResidents();  // Reload the residents table
                removePopup.classList.add("hidden");
            } else {
                alert("Error removing resident.");
            }
        })
        .catch(error => {
            console.error('Error removing resident:', error);
            alert("An error occurred while removing the resident.");
        });
    });

    // Cancel removal
    cancelRemoveButton.addEventListener("click", () => {
        removePopup.classList.add("hidden");
    });

    // Add resident form logic
    addForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(addForm);
        fetch("add_resident.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Resident added successfully!");
                loadResidents();  // Reload the residents table after adding
                addResidentPopup.classList.add("hidden");
            } else {
                alert("Error adding resident.");
            }
        })
        .catch(error => {
            console.error('Error adding resident:', error);
            alert("An error occurred while adding the resident.");
        });
    });

    loadResidents(); // Initially load the residents
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
        <p>ARE YOU SURE YOU WANT TO REMOVE THIS ACCOUNT?</p>
        <h3></h3> <!-- This will be updated dynamically with the resident's name -->
        <div class="popup-buttons">
            <button id="cancel-remove" class="cancel-btnr">CANCEL</button>
            <button id="confirm-remove" class="confirm-btnr">CONFIRM</button>
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
    <script src="residents.js"></script>

</body>
</html>
