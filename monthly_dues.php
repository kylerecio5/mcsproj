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
    <title>Monthly Dues</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="monthly_dues.css">
    <script src="monthly_dues.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="monthly_dues.php" class="active">Monthly Dues</a></li>
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
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const table = document.querySelector('.resident-table');
                    const tableBody = document.querySelector('#monthly-dues-table-body');
                    const sortSelect = document.querySelector('.search-filter-group select');

                    sortSelect.addEventListener('change', () => {
                        const column = sortSelect.value;
                        const columnIndex = {
                            'Name': 1,
                            'Street Light': 2,
                            'Monthly Due': 3,
                            'Status': 4
                        }[column];

                        if (columnIndex !== undefined) {
                            sortTable(tableBody, columnIndex);
                        }
                    });

                    function sortTable(tbody, columnIndex) {
                        const rows = Array.from(tbody.querySelectorAll('tr'));
                        const isNumeric = columnIndex === 3; // Monthly Due is numeric
                        let ascending = tbody.getAttribute('data-sort-order') !== 'asc';

                        rows.sort((rowA, rowB) => {
                            let cellA = rowA.cells[columnIndex].textContent.trim();
                            let cellB = rowB.cells[columnIndex].textContent.trim();

                            if (isNumeric) {
                                return ascending ? cellA - cellB : cellB - cellA;
                            } else {
                                return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                            }
                        });

                        tbody.innerHTML = '';
                        rows.forEach(row => tbody.appendChild(row));

                        tbody.setAttribute('data-sort-order', ascending ? 'asc' : 'desc');
                    }
                });
            </script>



            <header>
                <h1>MONTHLY DUES</h1>
            </header>
            <div class="search-sort">
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        // Reference to the search bar and table body
                        const searchBox = document.querySelector('.search-box');
                        const tableBody = document.querySelector('#monthly-dues-table-body');

                        // Event listener for search box input
                        searchBox.addEventListener('input', () => {
                            const searchTerm = searchBox.value.toLowerCase();

                            // Loop through all rows in the table body
                            const rows = tableBody.querySelectorAll('tr');
                            rows.forEach(row => {
                                // Combine all text content of the row for searching
                                const rowText = row.textContent.toLowerCase();

                                // Check if the search term exists in the row's text
                                if (rowText.includes(searchTerm)) {
                                    row.style.display = ''; // Show row
                                } else {
                                    row.style.display = 'none'; // Hide row
                                }
                            });
                        });
                    });
                </script>

                <!-- Search and Filter Group -->
                <div class="search-filter-group">
                    <input type="text" placeholder="Search" class="search-box">
                    <select>
                        <option>Sort by</option>
                        <option>Name</option>
                        <option>Street Light</option>
                        <option>Monthly Due</option>
                        <option>Status</option>
                        <!-- Additional sorting options -->
                    </select>
                    <select id="sort-by" name="sort_by" class="yearSort">
                        <option value="" disabled selected>Year</option>
                        <?php
                        // Generate sorting options for years up to the current year + 1
                        $currentYear = date("Y");
                        $endYear = $currentYear + 1;

                        for ($year = 2024; $year <= $endYear; $year++) {
                            echo "<option value=\"$year\" >$year</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Add Button -->
                <button class="add-button">ADD +</button>
            </div>

            <table class="resident-table">
                <thead>
                    <tr>
                        <th>RESIDENT CODE</th>
                        <th>NAME</th>
                        <th>STREET LIGHT</th>
                        <th>MONTHLY DUE</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody id="monthly-dues-table-body">
                    <!-- Rows will be dynamically inserted here via JavaScript -->
                </tbody>
            </table>
        </main>
    </div>


    <?php
    // Database connection parameters for XAMPP
    $host = 'localhost';
    $dbname = 'tms';
    $username = 'root';
    $password = '';

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // Handle connection errors
        die("Database connection failed: " . $e->getMessage());
    }

    // Fetch residents from the Resident table
    //$query = "SELECT Resident_ID, F_name,M_name,L_name FROM tbl_residents where Resident_ID not in (SELECT DISTINCT Resident from tbl_monthly_dues)";
    $query = "SELECT 
    r.Resident_ID, 
    r.F_name, 
    r.M_name, 
    r.L_name,
    CASE 
        WHEN r.Resident_ID IN (SELECT Resident FROM tbl_monthly_dues) THEN 1 
        ELSE 0 
    END AS Existing
FROM tbl_residents r;
";
    $stmt = $pdo->query($query);
    $residents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- Add Monthly Dues Popup -->
    <div id="add-dues-popup" class="popup-add hidden">
        <div class="popup-a">
            <h2>ADD MONTHLY DUES</h2>
            <form method="POST" action="add_dues.php">
                <div class="form-container">

                    <label for="year">Year:</label>
                    <select id="year" name="year" required>
                        <option value="" disabled selected>Select Year</option>
                        <?php
                        // Generate options for years up to the current year + 1
                        $currentYear = date("Y"); // Get the current year
                        $endYear = $currentYear + 1; // Add 1 to the current year
                        
                        for ($year = 2024; $year <= $endYear; $year++) {
                            echo "<option value=\"$year\">$year</option>";
                        }
                        ?>
                    </select>

                    <label for="resident">Resident:</label>
                    <select id="resident" name="resident[]" multiple required>
                        <option value="" disabled selected>Select a year first</option>
                    </select>

                    <label for="street-light">Street Light:</label>
                    <select id="street-light" name="street-light" required>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>

                    <label for="monthly-amount">Monthly Due Amount:</label>
                    <input type="number" id="monthly-amount" name="monthly_amount" value="1500" readonly required>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-add-dues" class="cancel-btn">CANCEL</button>
                    <button type="submit" id="confirm-add-dues" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById("year").addEventListener("change", function () {
    var selectedYear = this.value;
    var residentDropdown = document.getElementById("resident");

    fetch("fetch_MDuesresidents.php?year=" + selectedYear)
        .then(response => response.json())
        .then(data => {
            residentDropdown.innerHTML = '';

            data.forEach(resident => {
                let option = document.createElement("option");
                option.value = resident.Resident_ID;
                option.textContent = resident.F_name + " " + resident.M_name + " " + resident.L_name;
                residentDropdown.appendChild(option);
            });
        })
        .catch(error => console.error("Error fetching residents:", error));
});
    </script>





    <!-- Edt Monthly Dues Popup -->
    <div id="edit-dues-popup" class="popup-add hidden">
        <div class="popup-a">
            <h2>EDIT MONTHLY DUES</h2>
            <form method="POST" action="edit_dues.php">
                <input type="hidden" name="idToEdit" id="idToEdit">
                <div class="form-container">
                    <label for="resident">Resident:</label>
                    <select id="edit_resident" name="edit_resident" required disabled>
                        <option value="" disabled selected>Select Resident</option>
                        <?php foreach ($residents as $resident): ?>
                            <option value="<?= htmlspecialchars($resident['Resident_ID']) ?>">
                                <?= htmlspecialchars($resident['F_name'] . ' ' . $resident['M_name'] . ' ' . $resident['L_name']) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="Other Resident">Other Resident</option>
                    </select>

                    <label for="street-light">Street Light:</label>
                    <select id="edit_street-light" name="edit_street-light" required>

                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>

                    <label for="monthly-amount">Monthly Due Amount:</label>
                    <input type="number" id="edit_monthly-amount" name="edit_monthly_amount" readonly required>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-edit-dues" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">CONFIRM</button>
                </div>
            </form>
        </div>
    </div>



    <!-- Add Payment Modal -->
    <div id="add-payment-modal" class="payment-modal-overlay hidden">
        <div class="popup-a">
            <!-- Modal Header -->
            <div class="payment-modal-header ">
                <h2>ADD PAYMENT</h2>
                <!-- Modal Form -->
                <!-- <form class="payment-form" method = "POST" action = "add_payment.php"> -->
                <form method="POST" action="add_payment.php">
                    <div class="form-container">
                        <input type="hidden" id="add_payment_residentID" name="add_payment_residentID" />

                        <label for="payment-date">Date:</label>
                        <input type="date" id="payment-date" name="payment-date" required />
                        <script>
                            // Get today's date in the format 'YYYY-MM-DD'
                            const today = new Date().toISOString().split('T')[0];
                            // Set the value of the input field
                            document.getElementById('payment-date').value = today;
                        </script>
                        <label for="year">Year:</label>
                        <select id="year" name="year" required>
                            <option value="" disabled selected>Select Year</option>
                            <?php
                            // Generate options for years up to the current year + 1
                            $currentYear = date("Y"); // Get the current year
                            $endYear = $currentYear + 1; // Add 1 to the current year
                            
                            for ($year = 2024; $year <= $endYear; $year++) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                            ?>
                        </select>


                        <label for="payment-month">Month:</label>
                        <select id="payment-month" name="month" required>
                            <option value="" disabled selected>Select Month</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>

                        <label for="payment-amount">Amount:</label>
                        <input type="number" name="add_amount" id="add_amount" readonly />
                    </div>
                    <!-- Buttons -->
                    <div class="payment-modal-buttons">
                        <button id="cancel-payment-btn" class="cancel-btn">CANCEL</button>
                        <button type="submit" class="confirm-btn">CONFIRM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- View History Popup -->
    <div id="history-popup" class="popup-history hidden">
        <div class="popup-h">
            <button id="close-history" class="close-btn">✖</button>
            <div class="popup-header-h">
                <h2>PAYMENT HISTORY</h2>
            </div>

            <!-- Sorting Option -->
            <!-- <div class="history-sort">
                <label for="sort-history">Sort by</label>
                <select id="sort-history">
                    <option value="date">Date</option>
                    <option value="month">Month</option>
                    <option value="amount">Amount</option>
                </select>
            </div> -->

            <!-- History Table -->
            <div class="history-table">
                <table id="historyTable">
                    <thead>
                        <tr>

                            <th>MONTH</th>
                            <th>YEAR</th>
                            <th hidden>AMOUNT DUE</th>
                            <th>AMOUNT PAID</th>
                            <th hidden>ACTION</th>
                            <th>PAYMENT DATE</th>
                        </tr>

                    </thead>
                    <tbody id="history-table-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Monthly Dues Popup -->
    <!-- <div id="edit-dues-popup" class="popup-add hidden">
        <div class="popup-a">
            <h2>EDIT MONTHLY DUES</h2>
            <form id="edit-dues-form">
                <div class="form-container">
                    <label for="resident">Resident:</label>
                    <select id="resident" name="resident" required>
                        <option value="" disabled selected>Select Resident</option>
                        <option value="vlad">Vladimir D. Leyson</option>
                        <option value="other">Other Resident</option> 
                    </select>

                    <label for="street-light">Street Light:</label>
                    <select id="edit-street-light" name="street-light" required>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>

                    <label for="monthly-amount">Monthly Due Amount:</label>
                    <input type="number" id="edit-monthly-amount" name="monthly-amount" value="1500" required>
                </div>
                <div class="popup-buttons">
                    <button type="button" id="cancel-edit-dues" class="cancel-btn">CANCEL</button>
                    <button type="submit" class="confirm-btn">SAVE</button>
                </div>
            </form>
        </div>
    </div> -->


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

    <div id="remove-popup" class="popup-overlay hidden">
        <div class="popup">
            <p>ARE YOU SURE YOU WANT TO ARCHIVE THIS PAYER?</p>
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

</body>

</html>