<?php
// Simple script to check if global.js is being loaded
header('Content-Type: application/json');

$response = [
    'status' => 'success',
    'message' => 'Script check endpoint is working',
    'timestamp' => date('Y-m-d H:i:s')
];

echo json_encode($response);
?>