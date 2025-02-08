<?php
require 'db_connection.php'; // Make sure this file is in the same directory as upload.php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES["file"]) || !isset($_POST["booking_no"])) {
        echo json_encode(["success" => false, "message" => "Missing required data."]);
        exit;
    }

    $booking_no = $_POST["booking_no"];
    $file = $_FILES["file"];
    $uploadDir = "uploads/"; // Ensure this directory exists and is writable

    // Generate a unique filename
    $fileName = time() . "_" . basename($file["name"]);
    $filePath = $uploadDir . $fileName;

    // Move uploaded file
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create directory if not exists
    }

    if (move_uploaded_file($file["tmp_name"], $filePath)) {
        // Use the `$conn` variable from `db_connection.php`
        $stmt = $conn->prepare("UPDATE tbl_reservations SET Receipt = ? WHERE booking_no = ?");
        $stmt->bind_param("ss", $filePath, $booking_no);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "file_path" => $filePath]);
        } else {
            echo json_encode(["success" => false, "message" => "Database update failed."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "File upload failed."]);
    }
}
?>
