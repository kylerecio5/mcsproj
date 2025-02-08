<?php
session_start(); // Start the session
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
// Connect to your database (Update with your own credentials)
$host = 'localhost';
$username = 'root'; // Database username
$password = ''; // Database password
$dbname = 'tms'; // Your database name

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get all data from tbl_accounts
$sql = "SELECT account_id, username, password, admin_id, role, firstname, middlename, lastname, dateofbirth, phonenum, age, sex, email, address FROM tbl_accounts";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="accounts.css">
    <script src="accounts.js"></script>
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
                <li><a href="accounts.php" class="active">Accounts</a></li>
                <li><a href="logs.php">Logs</a></li>
                <li><a href="maintenance.php">Maintenance</a></li>
            </ul>
            <div class="sidebar-footer">
                <a id="to-staff-side" href="residents.php">Go to Staff Side</a>
                <a id="change_password_self">Change Password</a>
                <a id="logout" href="#">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>ACCOUNTS</h1>
            </header>
            <div class="search-sort">
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const searchBox = document.querySelector(".search-box");
                        const tableRows = document.querySelectorAll(".resident-table tbody tr");

                        searchBox.addEventListener("input", function () {
                            const searchText = searchBox.value.toLowerCase();

                            tableRows.forEach(row => {
                                const rowText = row.innerText.toLowerCase();
                                row.style.display = rowText.includes(searchText) ? "" : "none";
                            });
                        });
                    });
                </script>

                <div class="search-filter-group">
                    <input type="text" placeholder="Search" class="search-box">
                    <select class="sort-by">
                        <option value="">Sort by</option> <!-- Default option -->
                        <option value="name">Name</option>
                        <option value="role">Account Type</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone Number</option>
                        <option value="age">Age</option>
                    </select>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const sortByDropdown = document.querySelector(".sort-by");
                            const tableBody = document.querySelector(".resident-table tbody");
                            const rows = Array.from(tableBody.rows); // Convert NodeList to Array

                            sortByDropdown.addEventListener("change", function () {
                                const sortKey = sortByDropdown.value;

                                // Function to get the cell value for the specified key
                                const getCellValue = (row, key) => {
                                    switch (key) {
                                        case "name":
                                            return row.cells[0].innerText.toLowerCase(); // Name
                                        case "role":
                                            return row.cells[1].innerText.toLowerCase(); // Account Type
                                        case "email":
                                            return row.cells[2].innerText.toLowerCase(); // Email
                                        case "phone":
                                            return row.cells[3].innerText.toLowerCase(); // Phone Number
                                        case "age":
                                            return parseInt(row.cells[5].innerText) || 0; // Age (convert to number)
                                        default:
                                            return "";
                                    }
                                };

                                // Sort the rows based on the selected key
                                rows.sort((a, b) => {
                                    const aValue = getCellValue(a, sortKey);
                                    const bValue = getCellValue(b, sortKey);

                                    if (aValue < bValue) return -1;
                                    if (aValue > bValue) return 1;
                                    return 0;
                                });

                                // Re-append the sorted rows back to the table body
                                rows.forEach(row => tableBody.appendChild(row));
                            });
                        });
                    </script>
                    <select class="user-type">
                        <option value="">User Type</option>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                    </select>
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const sortByDropdown = document.querySelector(".sort-by");
                            const userTypeDropdown = document.querySelector(".user-type");
                            const tableBody = document.querySelector(".resident-table tbody");
                            const rows = Array.from(tableBody.rows);

                            // Function to get cell value for a specific column
                            const getCellValue = (row, key) => {
                                switch (key) {
                                    case "name":
                                        return row.cells[0].innerText.toLowerCase();
                                    case "role":
                                        return row.cells[1].innerText.toLowerCase();
                                    case "email":
                                        return row.cells[2].innerText.toLowerCase();
                                    case "phone":
                                        return row.cells[3].innerText.toLowerCase();
                                    case "age":
                                        return parseInt(row.cells[5].innerText) || 0;
                                    default:
                                        return "";
                                }
                            };

                            // Sort rows based on selected column
                            sortByDropdown.addEventListener("change", function () {
                                const sortKey = sortByDropdown.value;

                                rows.sort((a, b) => {
                                    const aValue = getCellValue(a, sortKey);
                                    const bValue = getCellValue(b, sortKey);

                                    if (aValue < bValue) return -1;
                                    if (aValue > bValue) return 1;
                                    return 0;
                                });

                                rows.forEach(row => tableBody.appendChild(row));
                            });

                            // Filter rows based on selected user type
                            userTypeDropdown.addEventListener("change", function () {
                                const selectedRole = userTypeDropdown.value.toLowerCase();

                                rows.forEach(row => {
                                    const role = row.cells[1].innerText.toLowerCase();
                                    if (selectedRole === "" || role === selectedRole) {
                                        row.style.display = ""; // Show the row
                                    } else {
                                        row.style.display = "none"; // Hide the row
                                    }
                                });
                            });
                        });
                    </script>

                </div>
                <button class="add-button">ADD +</button>
            </div>
            <table class="resident-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Account Type</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Date of Birth</th>
                        <th>Address</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all accounts from the database
                    $sql = "SELECT account_id, firstname, middlename, lastname, phonenum, email, sex, age, dateofbirth, address, role, username, password FROM tbl_accounts WHERE is_deleted = 0";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-id='{$row['account_id']}'>
                                <td>{$row['firstname']} {$row['middlename']} {$row['lastname']}</td>
                                <td>{$row['role']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['phonenum']}</td>
                                <td>{$row['sex']}</td>
                                <td>{$row['age']}</td>
                                <td>{$row['dateofbirth']}</td>
                                <td>{$row['address']}</td>
                                <td>{$row['username']}</td>
                                <td>" . str_repeat('*', strlen($row['password'])) . "</td>
                                <td>
                                    <button class='change_pass' data-id='{$row['account_id']}' data-name='{$row['firstname']} {$row['middlename']} {$row['lastname']}' data-role='{$row['role']}'>CHANGE PASSWORD</button>
                                    <button class='edit-button' data-id='{$row['account_id']}' data-role='{$row['role']}'>EDIT</button>
                                <button class='remove' data-id='{$row['account_id']}' data-name='{$row['firstname']} {$row['middlename']} {$row['lastname']}' data-role='{$row['role']}'>ARCHIVE</button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11'>No accounts found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Add Account Popup -->
            <div id="add-account-popup" class="popup-overlay hidden">
                <div class="popup">
                    <h2>ADD ACCOUNT</h2>
                    <form id="add-account-form">
                        <div class="form-container">
                            <div class="left-column">
                                <label>First name:</label>
                                <input type="text" name="first_name" required>

                                <label>Middle name:</label>
                                <input type="text" name="middle_name" required>

                                <label>Last name:</label>
                                <input type="text" name="last_name" required>

                                <label>Phone number:</label>
                                <input type="text" name="phone_number" required>

                                <label>Age:</label>
                                <input type="number" name="age" required>

                                <label>Sex:</label>
                                <select name="sex" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="right-column">
                                <label>Email:</label>
                                <input type="email" name="email" required>

                                <label>Password:</label>
                                <div style="position: relative; width: 100%; max-width: 300px;">
    <input type="password" name="password" id="password" required 
        style="width: 100%;">
    <button type="button" id="togglePassword" 
        style="position: absolute; top: 50%; right: -10px; transform: translateY(-50%); background: none; border: none; cursor: pointer;">
        Show
    </button>
</div>

                                <label>Username:</label>
                                <input type="text" name="username" required>

                                <label>Account type:</label>
                                <select name="account_type" required>
                                    <option value="Admin">Admin</option>
                                    <option value="Staff">Staff</option>
                                </select>

                                <label>Date of birth:</label>
                                <input type="date" name="dob" required>

                                <label>Address:</label>
                                <textarea name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="popup-buttons">
                            <button type="button" id="cancel-add" class="cancel-btn">CANCEL</button>
                            <button type="submit" class="confirm-btn">CREATE</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.getElementById('add-account-form').addEventListener('submit', function (event) {
                    event.preventDefault();

                    const formData = new FormData(this);

                    fetch('add_account.php', {
                        method: 'POST',
                        body: formData,
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                const table = document.querySelector('.resident-table tbody');
                                const newRow = table.insertRow();

                                newRow.setAttribute('data-id', data.account_id);
                                newRow.innerHTML = `
                                    <td>${formData.get('first_name')} ${formData.get('middle_name') || ''} ${formData.get('last_name')}</td>
                                    <td>${formData.get('account_type')}</td>
                                    <td>${formData.get('email')}</td>
                                    <td>${formData.get('phone_number')}</td>
                                    <td>${formData.get('sex')}</td>
                                    <td>${formData.get('age')}</td>
                                    <td>${formData.get('dob')}</td>
                                    <td>${formData.get('address')}</td>
                                    <td>${formData.get('username')}</td>
                                    <td> ${'*'.repeat(formData.get('password').length)}</td>
                                    <td>
                                        <button class='change_pass' data-id='${data.account_id}' data-name='${formData.get('first_name')} ${formData.get('last_name')}' data-role='${data.role}'>CHANGE PASSWORD</button>
                                        <button class='edit-button' data-id='${data.account_id}' data-role='${data.role}'>EDIT</button>
                                        <button class='remove' data-id='${data.account_id}' data-name='${formData.get('first_name')} ${formData.get('last_name')}' data-role='${data.role}'>REMOVE</button>

                                    </td>
                                `;

                                document.getElementById('add-account-popup').classList.add('hidden');
                                this.reset();
                            } else {
                                alert(data.message || 'Failed to add account. Please try again.');
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            alert('There was an error adding the account.');
                        });
                });
            </script>

            <!-- Edit Account Popup -->
            <div id="edit-account-popup" class="popup-overlay hidden">
                <div class="popup">
                    <h2>EDIT ACCOUNT</h2>
                    <form id="edit-account-form">
                        <div class="form-container">
                            <div class="left-column">
                                <label>First name:</label>
                                <input type="text" name="first_name" required>

                                <label>Middle name:</label>
                                <input type="text" name="middle_name" required>

                                <label>Last name:</label>
                                <input type="text" name="last_name" required>

                                <label>Phone number:</label>
                                <input type="text" name="phone_number" required>

                                <label>Age:</label>
                                <input type="number" name="age" required>

                                <label>Sex:</label>
                                <select name="sex" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="right-column">
                                <label>Email:</label>
                                <input type="email" name="email" required>

                                <label>Username:</label>
                                <input type="text" name="username" required>

                                <label>Account type:</label>
                                <select name="account_type" required>
                                    <option value="Admin">Admin</option>
                                    <option value="Staff">Staff</option>
                                </select>

                                <label>Date of birth:</label>
                                <input type="date" name="dob" required>

                                <label>Address:</label>
                                <textarea name="address" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Hidden input for account_id -->
                        <input type="hidden" name="account_id" id="account_id">

                        <div class="popup-buttons">
                            <button type="button" id="cancel-edit" class="cancel-btn">CANCEL</button>
                            <button type="submit" class="confirm-btn">UPDATE</button>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Attach event listeners to the edit buttons
                    document.querySelectorAll('.edit-button').forEach(function (button) {
                        button.addEventListener('click', function () {
                            const accountId = this.getAttribute('data-id');
                            const row = document.querySelector(`tr[data-id="${accountId}"]`);

                            // Extract data from the row
                            const nameParts = row.cells[0].textContent.split(' '); // Assuming name is in the first cell
                            const firstName = nameParts[0];
                            const middleName = nameParts.length === 3 ? nameParts[1] : '';
                            const lastName = nameParts[nameParts.length - 1];
                            const phoneNumber = row.cells[3].textContent;
                            const email = row.cells[2].textContent;
                            const sex = row.cells[4].textContent;
                            const age = row.cells[5].textContent;
                            const dateOfBirth = row.cells[6].textContent;
                            const address = row.cells[7].textContent;
                            const accountType = row.cells[1].textContent;
                            const username = row.cells[8].textContent; // Assuming username is in the 9th cell (index 8)

                            // Fill the form with the extracted data
                            document.querySelector('#edit-account-form [name="first_name"]').value = firstName;
                            document.querySelector('#edit-account-form [name="middle_name"]').value = middleName;
                            document.querySelector('#edit-account-form [name="last_name"]').value = lastName;
                            document.querySelector('#edit-account-form [name="phone_number"]').value = phoneNumber;
                            document.querySelector('#edit-account-form [name="email"]').value = email;
                            document.querySelector('#edit-account-form [name="sex"]').value = sex;
                            document.querySelector('#edit-account-form [name="age"]').value = age;
                            document.querySelector('#edit-account-form [name="dob"]').value = dateOfBirth;
                            document.querySelector('#edit-account-form [name="address"]').value = address;
                            document.querySelector('#edit-account-form [name="account_type"]').value = accountType;

                            // Set the username value (it won't be editable but will show)
                            document.querySelector('#edit-account-form [name="username"]').value = username;

                            // Set the hidden account_id field value (make sure this field is in the form)
                            document.querySelector('#edit-account-form [name="account_id"]').value = accountId;

                            // Show the edit account popup
                            document.getElementById('edit-account-popup').classList.remove('hidden');
                        });
                    });

                    // Handle form submission
                    document.querySelector('#edit-account-form').addEventListener('submit', function (e) {
                        e.preventDefault(); // Prevent the form from reloading the page

                        const formData = new FormData(this);

                        // Send the form data to the server via AJAX
                        fetch('update_account.php', {
                            method: 'POST',
                            body: formData,
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert(data.message); // Display success message
                                    location.reload(); // Reload the page to reflect changes
                                } else {
                                    alert(data.message); // Display error message
                                }
                            })
                            .catch(error => {
                                console.error('Error updating account:', error);
                            });
                    });

                    // Handle cancel button click
                    document.querySelector('.cancel-btn').addEventListener('click', function () {
                        document.getElementById('edit-account-popup').classList.add('hidden');
                    });
                });




            </script>
        </main>
    </div>
    <!-- Remove Account Popup -->
    <div id="remove-popup" class="popup-overlay hidden">
        <div class="popup">
            <p>ARE YOU SURE YOU WANT TO REMOVE THIS ACCOUNT?</p>
            <h3 id="account-name"></h3> <!-- Added id="account-name" for dynamic updates -->
            <div class="popup-buttons">
                <button id="cancel-remove" class="cancel-btnr">CANCEL</button>
                <button id="confirm-remove" class="confirm-btnr">CONFIRM</button>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.remove').forEach(button => {
            button.addEventListener('click', function () {
                const accountId = this.getAttribute('data-id'); // Use data-id as defined in HTML
                const accountName = this.getAttribute('data-name');

                if (!accountId) {
                    console.error('Missing account ID.');
                    return;
                }

                // Update the popup with account details
                document.querySelector('#remove-popup').setAttribute('data-account-id', accountId);
                document.querySelector('#account-name').textContent = accountName;
                document.querySelector('#remove-popup').classList.remove('hidden');
            });
        });

        // Confirm deletion
        // Add event listener to the "REMOVE" buttons
        document.querySelectorAll('.remove').forEach(button => {
            button.addEventListener('click', function () {
                const accountId = this.getAttribute('data-id');
                const accountName = this.getAttribute('data-name');

                // Show confirmation popup
                document.querySelector('#remove-popup h3').textContent = accountName;
                document.querySelector('#remove-popup').classList.remove('hidden');

                // Confirm deletion
                document.querySelector('#confirm-remove').onclick = function () {
                    fetch('delete_account.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ accountId: accountId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove the row from the table
                                const row = document.querySelector(`tr[data-id="${accountId}"]`);
                                if (row) row.remove();

                                // Hide the popup
                                document.querySelector('#remove-popup').classList.add('hidden');
                            } else {
                                alert(`Error: ${data.error}`);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to delete account. Please try again.');
                        });
                };
            });
        });

        // Cancel deletion
        document.querySelector('#cancel-remove').addEventListener('click', function () {
            document.querySelector('#remove-popup').classList.add('hidden');
        });




    </script>
    <script>
        // Select all the "REMOVE" buttons
        const removeButtons = document.querySelectorAll('.remove');

        // Add event listener to each "REMOVE" button
        removeButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Get the name from the data-name attribute of the button
                const name = this.getAttribute('data-name');

                // Update the popup with the account name
                const accountNameElement = document.querySelector('#remove-popup h3');
                accountNameElement.textContent = name; // Dynamically updates the name in the popup

                // Show the popup
                document.getElementById('remove-popup').classList.remove('hidden');
            });
        });

        // Event listener for canceling the remove action
        document.getElementById('cancel-remove').addEventListener('click', function () {
            document.getElementById('remove-popup').classList.add('hidden');
        });
    </script>

    <!-- Change Password Popup -->
    <div id="change-password-popup" class="popup-overlay hidden">
        <div class="popup">
            <h2>CHANGE PASSWORD</h2>
            <h3>Vladimir D. Leyson (ADMIN)</h3>
            <form id="change-password-form">
                <div class="form-container">
                    <label>New Password:</label>
                    <div class="password-container">
                        <input type="password" name="new_password" id="new-password" required>
                        <i class="fa fa-eye" id="toggle-password" onclick="togglePassword()"></i>
                    </div>

                    <label>Confirm New Password:</label>
                    <div class="password-container">
                        <input type="password" name="confirm_password" id="confirm-password" required>
                        <i class="fa fa-eye" id="toggle-confirm-password" onclick="toggleConfirmPassword()"></i>
                    </div>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-change-password" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">SAVE</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Select all the "CHANGE PASSWORD" buttons
        const changePasswordButtons = document.querySelectorAll('.change_pass');

        // Add event listener to each "CHANGE PASSWORD" button
        changePasswordButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Get the account's ID and name from the button
                const accountId = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');

                // Update the popup with the account name
                const accountNameElement = document.querySelector('#change-password-popup h3');
                accountNameElement.textContent = name; // Dynamically updates the name in the popup

                // Store the accountId for later use
                document.getElementById('change-password-form').setAttribute('data-id', accountId);

                // Show the change password popup
                document.getElementById('change-password-popup').classList.remove('hidden');
            });
        });

        // Event listener for canceling the change password action
        document.getElementById('cancel-change-password').addEventListener('click', function () {
            // Clear the password input fields
            document.getElementById('new-password').value = '';
            document.getElementById('confirm-password').value = '';

            // Hide the popup
            document.getElementById('change-password-popup').classList.add('hidden');
        });

        // Event listener for confirming the change password action
        document.getElementById('change-password-form').addEventListener('submit', function (e) {
            e.preventDefault();

            // Get the new password and confirm password
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                return;
            }

            // Get the accountId stored in the form
            const accountId = this.getAttribute('data-id');

            // Send the new password to the server to update in the database
            fetch('change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ account_id: accountId, new_password: newPassword })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Password updated successfully.');

                        // Update the password in the table UI
                        const row = document.querySelector(`tr[data-id="${accountId}"]`);
                        const passwordCell = row.cells[9]; // The 10th cell should be the password column

                        // Dynamically update the number of asterisks based on the new password length
                        passwordCell.textContent = str_repeat('*', newPassword.length); // Use the length of the new password

                        // Clear the input fields
                        document.getElementById('new-password').value = '';
                        document.getElementById('confirm-password').value = '';

                        // Close the popup
                        document.getElementById('change-password-popup').classList.add('hidden');
                    } else {
                        alert('Failed to change password. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error updating the password.');
                });
        });

        // Helper function to repeat characters (to display the password as asterisks)
        function str_repeat(str, num) {
            return new Array(num + 1).join(str);
        }


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
                        <i class="fa fa-eye" id="toggle-confirm-password-self"
                            onclick="toggleConfirmPasswordSelf()"></i>
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
</body>

</html>

<?php
$conn->close();
?>