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

// Fetch prices from the database
$sql = "SELECT * FROM tbl_bookingprices";
$result = $conn->query($sql);
$prices = [];
while ($row = $result->fetch_assoc()) {
    $prices[$row['facility_name']] = [
        'day_rate' => $row['day_rate'],
        'night_rate' => $row['night_rate']
    ];
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
    <title>Maintenance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="maintenancee.css">
</head>
<body>
    <div class="container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <img src="citation_logo_big.png" alt="Subdivision Logo" class="logo">
                <?php
                if (isset($_SESSION['user']['firstname']) && isset($_SESSION['user']['middlename']) && isset($_SESSION['user']['lastname'])) {
                    echo "<p>Hello, " . $_SESSION['user']['firstname'] . " " . $_SESSION['user']['middlename'] . " " . $_SESSION['user']['lastname'] . "!</p>";
                } else {
                    echo "<p>Hello, Guest!</p>";
                }
                ?>
            </div>
            <ul>
                <li><a href="accounts.php">Accounts</a></li>
                <li><a href="logs.php">Logs</a></li>
                <li><a href="maintenance.php" class="active">Maintenance</a></li>
            </ul>
            <div class="sidebar-footer">
                <a id="to-staff-side" href="residents.php">Go to Staff Side</a>
                <a id="logout" href="#">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <h1>MAINTENANCE</h1>
            </header>
            
            <div class="search-sort">
            <div class="pricelist-value">
                <h2>BOOKING PRICE</h2>
                <table class="resident-table">
                    <tr>
                        <th>Facility</th>
                        <th>Day time Rate</th>
                        <th>Night time Rate</th>
                    </tr>
                    <tr>
    <td>Clubhouse (HOMEOWNER)</td>
    <td><input type="number" id="clubhouse-homeowner-day" class="price-input" value="<?= isset($prices['Clubhouse (HOMEOWNER)']) ? $prices['Clubhouse (HOMEOWNER)']['day_rate'] : '5000' ?>"></td>
    <td><input type="number" id="clubhouse-homeowner-night" class="price-input" value="<?= isset($prices['Clubhouse (HOMEOWNER)']) ? $prices['Clubhouse (HOMEOWNER)']['night_rate'] : '5000' ?>"></td>
</tr>
<tr>
    <td>Clubhouse (OUTSIDER)</td>
    <td><input type="number" id="clubhouse-outsider-day" class="price-input" value="<?= isset($prices['Clubhouse (OUTSIDER)']) ? $prices['Clubhouse (OUTSIDER)']['day_rate'] : '5000' ?>"></td>
    <td><input type="number" id="clubhouse-outsider-night" class="price-input" value="<?= isset($prices['Clubhouse (OUTSIDER)']) ? $prices['Clubhouse (OUTSIDER)']['night_rate'] : '5000' ?>"></td>
</tr>
<tr>
    <td>Swimming Pool (HOMEOWNER)</td>
    <td><input type="number" id="pool-homeowner-day" class="price-input" value="<?= isset($prices['Swimming Pool (HOMEOWNER)']) ? $prices['Swimming Pool (HOMEOWNER)']['day_rate'] : '100' ?>"></td>
    <td><input type="number" id="pool-homeowner-night" class="price-input" value="<?= isset($prices['Swimming Pool (HOMEOWNER)']) ? $prices['Swimming Pool (HOMEOWNER)']['night_rate'] : '150' ?>"></td>
</tr>
<tr>
    <td>Swimming Pool (OUTSIDER)</td>
    <td><input type="number" id="pool-outsider-day" class="price-input" value="<?= isset($prices['Swimming Pool (OUTSIDER)']) ? $prices['Swimming Pool (OUTSIDER)']['day_rate'] : '100' ?>"></td>
    <td><input type="number" id="pool-outsider-night" class="price-input" value="<?= isset($prices['Swimming Pool (OUTSIDER)']) ? $prices['Swimming Pool (OUTSIDER)']['night_rate'] : '150' ?>"></td>
</tr>
<tr>
    <td>Basketball Court</td>
    <td><input type="number" id="basketball-day" class="price-input" value="<?= isset($prices['Basketball Court']) ? $prices['Basketball Court']['day_rate'] : '100' ?>"></td>
    <td><input type="number" id="basketball-night" class="price-input" value="<?= isset($prices['Basketball Court']) ? $prices['Basketball Court']['night_rate'] : '150' ?>"></td>
</tr>
<tr>
    <td>Volleyball Court</td>
    <td><input type="number" id="volleyball-day" class="price-input" value="<?= isset($prices['Volleyball Court']) ? $prices['Volleyball Court']['day_rate'] : '100' ?>"></td>
    <td><input type="number" id="volleyball-night" class="price-input" value="<?= isset($prices['Volleyball Court']) ? $prices['Volleyball Court']['night_rate'] : '150' ?>"></td>
</tr>
<tr>
    <td>Tennis Court</td>
    <td><input type="number" id="tennis-day" class="price-input" value="<?= isset($prices['Tennis Court']) ? $prices['Tennis Court']['day_rate'] : '100' ?>"></td>
    <td><input type="number" id="tennis-night" class="price-input" value="<?= isset($prices['Tennis Court']) ? $prices['Tennis Court']['night_rate'] : '150' ?>"></td>
</tr>
                </table>
                <div class="save-prices-container">
                    <button onclick="savePrices()" class="confirm_btn">Save Prices</button>
                </div>
            </div>
            </div>

            <script>
                function savePrices() {
    const prices = [
        { facility: "Clubhouse (HOMEOWNER)", day: document.getElementById("clubhouse-homeowner-day").value, night: document.getElementById("clubhouse-homeowner-night").value },
        { facility: "Clubhouse (OUTSIDER)", day: document.getElementById("clubhouse-outsider-day").value, night: document.getElementById("clubhouse-outsider-night").value },
        { facility: "Swimming Pool (HOMEOWNER)", day: document.getElementById("pool-homeowner-day").value, night: document.getElementById("pool-homeowner-night").value },
        { facility: "Swimming Pool (OUTSIDER)", day: document.getElementById("pool-outsider-day").value, night: document.getElementById("pool-outsider-night").value },
        { facility: "Basketball Court", day: document.getElementById("basketball-day").value, night: document.getElementById("basketball-night").value },
        { facility: "Volleyball Court", day: document.getElementById("volleyball-day").value, night: document.getElementById("volleyball-night").value },
        { facility: "Tennis Court", day: document.getElementById("tennis-day").value, night: document.getElementById("tennis-night").value }
    ];

    // Sending the prices to the server
    fetch('save_prices.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(prices)
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            location.reload(); // Reload the page if successful
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving prices.');
    });
}
            </script>

            <div class="search-sort">

            <div class="pricelist-value">
                <h2>PRICE LIST IMAGE</h2>
                <div class="centered-container">
    <div>
        <img id="price-image" src="get_image.php" alt="Price List Image" style="max-width: 300px; max-height: 200px;"/>
    </div>
    <div class="custom-file-upload">
    <input type="file" id="image-upload" onchange="previewImage()" />
    <label for="image-upload" class="file-label">Choose Image</label>
</div>
    <div class="save-prices-container">
        <button onclick="saveImage()" class="confirm_btn">Save Image</button>
    </div>
</div>

                </div>

            </div>

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

    <script>
        function previewImage() {
    const fileInput = document.getElementById('image-upload');
    const file = fileInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('price-image').src = e.target.result; // Preview before upload
        };
        reader.readAsDataURL(file);
    }
}

function saveImage() {
    const fileInput = document.getElementById('image-upload');
    if (!fileInput.files[0]) {
        alert('Please select an image to upload.');
        return;
    }

    const formData = new FormData();
    formData.append('image', fileInput.files[0]);

    fetch('upload_image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            document.getElementById('price-image').src = 'get_image.php?' + new Date().getTime(); // Refresh image
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error uploading image.');
    });
}

    </script>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>