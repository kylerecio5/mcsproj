<?php
// Start the session
session_start();

// Include the database connection file
include 'db_connection.php';

// Initialize error message
$error = "";

// Set the debug file path
$debug_file = 'debug_output.txt';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted username and password
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Secure the input (prevent SQL injection)
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Log the submitted values to the debug file
    $debug_output = "Username: $username\nPassword: $password\n";

    // Query to check if the credentials exist in tbl_accounts, where is_deleted is 0 (not deleted)
    $stmt = $conn->prepare("SELECT * FROM tbl_accounts WHERE username = ? AND password = ? AND is_deleted = 0");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // If a record is found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Store the user details in the session, including account_id
        $_SESSION['user'] = [
            'account_id' => $row['account_id'],
            'username' => $row['username'],
            'role' => trim($row['role']), // Trim spaces
            'firstname' => $row['firstname'],
            'middlename' => $row['middlename'],
            'lastname' => $row['lastname']
        ];

        // Log session values to the debug file
        $debug_output .= "Logged in as: " . $_SESSION['user']['username'] . "\n";
        $debug_output .= "Role: " . $_SESSION['user']['role'] . "\n";

        // Debug check for the session role before redirect
        $debug_output .= "Session role check: " . $_SESSION['user']['role'] . "\n";

        // Log session information to debug file
        file_put_contents($debug_file, $debug_output, FILE_APPEND);

        // Redirect based on the role
        if ($_SESSION['user']['role'] == 'Admin') {
            // Log redirection to admin page
            $debug_output .= "Redirecting to accounts.php\n";
            file_put_contents($debug_file, $debug_output, FILE_APPEND);
            header("Location: accounts.php"); // Redirect admins to accounts.php
            exit();
        } elseif ($_SESSION['user']['role'] == 'Staff') {
            // Log redirection to residents page
            $debug_output .= "Redirecting to residents.php\n";
            file_put_contents($debug_file, $debug_output, FILE_APPEND);
            header("Location: residents.php"); // Redirect staff to residents.php
            exit();
        } else {
            // If the role is neither Admin nor Staff, handle it here
            $error = "Invalid user role.";
            $debug_output .= "Error: $error\n";
            file_put_contents($debug_file, $debug_output, FILE_APPEND);
        }
        exit();
    } else {
        // If credentials are invalid
        $error = "Invalid username or password.";
        $debug_output .= "Login failed: $error\n";
        file_put_contents($debug_file, $debug_output, FILE_APPEND);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="login.css">
    <script src="login.js"></script>
</head>

<body>
    <div class="topnav" id="myTopnav">
        <a href="login.php" class="active">
            <img src="citation_logo.png" alt="Home" class="logo-icon">
        </a>
    </div>

    <div class="logo-container">
        <img id="logo" src="citation_logo_big.png" alt="logo">
    </div>

    <!-- Login Form -->
    <div class="gap">
        <div class="login_bg">
            <h2 style="text-align: center;">Login</h2>
            <form action="login.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>

                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility()">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>

                <button type="submit" class="login-button">Login</button>
                <?php if (!empty($error)) echo "<p class='error' style='color:red;'>$error</p>"; ?>
            </form>
        </div>
    </div>

    <div class="footer">
        ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
        <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> | 
        <a href="#">Follow us</a>
    </div>
</body>
</html>
