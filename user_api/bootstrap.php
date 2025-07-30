<?php
// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Output Buffering
ob_start();


// Security Headers
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');