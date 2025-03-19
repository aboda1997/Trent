<?php 
set_exception_handler(function (Throwable $e) {
    // Log the error in the specified format
    error_log(json_encode([
        "result" => "false",
        "response_message" => "An error occurred!",
        "response_code" => 500,
        "error" => [
            "error_message" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine()
        ]
    ]));

    // Return a consistent API response
    header('Content-Type: application/json');
    echo json_encode([
        "result" => "false",
        "response_message" => "An error occurred!",
        "response_code" => 500,
        "error" => [
            "error_message" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine()
        ]
    ]);
    exit;
});

// Set a global error handler to convert errors into exceptions
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set a shutdown function to catch fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Convert the fatal error into an exception
        throw new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
    }
});