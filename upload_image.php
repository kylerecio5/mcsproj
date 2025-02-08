<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tms";

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
    $image = $_FILES["image"]["tmp_name"];
    $imageData = file_get_contents($image); // Read image as binary data

    // Check if an image already exists
    $checkQuery = "SELECT id FROM tbl_images LIMIT 1";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // Update existing image
        $stmt = $conn->prepare("UPDATE tbl_images SET image_data=? WHERE id=1");
    } else {
        // Insert new image
        $stmt = $conn->prepare("INSERT INTO tbl_images (image_data) VALUES (?)");
    }

    $stmt->bind_param("s", $imageData);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Image uploaded successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No image uploaded or error occurred.']);
}

$conn->close();
?>
