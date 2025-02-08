<?php
// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Path to log file
$logFile = 'log.txt';

// If log file doesn't exist, create it
if (!file_exists($logFile)) {
    file_put_contents($logFile, "Log started: " . date('Y-m-d H:i:s') . "\n");
}

// Append the log data to the file
if (isset($data['log'])) {
    file_put_contents($logFile, $data['log'], FILE_APPEND);
}
?>
