<?php
// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Start output buffering with callback
ob_start(function($buffer) {
    // Store the response body before sending
    file_put_contents(
        __DIR__.'/../logs/response.log',
        date('[Y-m-d H:i:s]')." Response: ".$buffer.PHP_EOL,
        FILE_APPEND
    );
    return $buffer; // Send to browser
}, 4096); // 4KB chunk size
register_shutdown_function(function() {
    if (ob_get_length() > 0) {
        ob_end_flush();
    }
});

// Security Headers
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');