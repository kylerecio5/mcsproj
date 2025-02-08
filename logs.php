<?php
session_start(); // Start the session
// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost"; // Your server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "tms"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get logs from tbl_logs
$sql = "SELECT * FROM tbl_logs ORDER BY log_id DESC"; // Retrieve logs in descending order
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="logs.css">
    <script src="logs.js"></script>
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
                <li><a href="accounts.php">Accounts</a></li>
                <li><a href="logs.php" class="active">Logs</a></li>
                <li><a href="maintenance.php">Maintenance</a></li>
            </ul>
            <div class="sidebar-footer">
                <a id="to-staff-side" href="residents.php">Go to Staff Side</a>
                <a id="logout" href="#">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>LOGS</h1>
            </header>
            <div class="search-sort">
              <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const searchBox = document.querySelector(".search-box");
                    const tableRows = document.querySelectorAll(".resident-table tbody tr");

                    searchBox.addEventListener("input", function() {
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
                        <option value="">Sort by</option>
                        <option value="time">Time</option>
                        <option value="date">Date</option>
                        <option value="description">Description</option>
                        <option value="action">Action</option>
                    </select>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const searchBox = document.querySelector(".search-box");
                            const tableRows = document.querySelectorAll(".resident-table tbody tr");
                            const sortSelect = document.querySelector(".sort-by");

                            // Function to sort the rows based on the selected column
                            function sortTable(columnIndex, isDescending = false) {
                                const sortedRows = Array.from(tableRows).sort((rowA, rowB) => {
                                    const cellA = rowA.cells[columnIndex].textContent.trim();
                                    const cellB = rowB.cells[columnIndex].textContent.trim();

                                    // Compare strings and sort accordingly
                                    if (cellA < cellB) return isDescending ? 1 : -1;
                                    if (cellA > cellB) return isDescending ? -1 : 1;
                                    return 0;
                                });

                                // Reorder the rows in the table
                                sortedRows.forEach(row => {
                                    row.parentNode.appendChild(row);  // Append each row back to the table body
                                });
                            }

                            // Event listener for the sorting dropdown
                            sortSelect.addEventListener("change", function() {
                                const column = sortSelect.value;
                                if (column === "") return;  // Do nothing if "Sort by" is selected

                                let columnIndex;
                                switch (column) {
                                    case "time":
                                        columnIndex = 0;
                                        break;
                                    case "date":
                                        columnIndex = 1;
                                        break;
                                    case "description":
                                        columnIndex = 2;
                                        break;
                                    case "action":
                                        columnIndex = 3;
                                        break;
                                }

                                sortTable(columnIndex);
                            });
                        });
                        </script>
                </div>
            </div>            
            <table class="resident-table">
                <thead>
                    <tr>
                        <th>TIME</th>
                        <th>DATE</th>
                        <th>DESCRIPTION</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Check if there are logs
                    if ($result->num_rows > 0) {
                        // Display each log entry
                        while ($row = $result->fetch_assoc()) {
                            $time = htmlspecialchars($row['Time']);
                            $date = htmlspecialchars($row['Date']);
                            $action = htmlspecialchars($row['action']);
                            $description = htmlspecialchars($row['description']);  // Get the description
                            // Output log row in the table
                            echo "<tr>";
                            echo "<td>$time</td>";
                            echo "<td>$date</td>";
                            echo "<td>$description</td>";  // Display the description here
                            echo "<td>$action</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No logs found.</td></tr>";  // Updated to match column count
                    }
                    ?>
                </tbody>

            </table>
        </main>
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

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
