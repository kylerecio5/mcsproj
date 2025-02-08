<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tms";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed");
}

$sql = "SELECT image_data FROM tbl_images LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    header("Content-Type: image/jpeg"); // Change type based on image format
    echo $row["image_data"];
} else {
    header("Content-Type: image/png"); // Default placeholder
    readfile("placeholder.png");
}

$conn->close();
?>
