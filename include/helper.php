<?php

function generateResponse($result, $response_message, $response_code , $data = null ) {
    // Create an array for the response
    $response = array(
        'result' => $result,
        'response_message' => $response_message,
        'response_code' => $response_code,
    );
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    // Set the appropriate HTTP response code (default: 200 OK)
    http_response_code($response_code);

    // Return the response as JSON
    return json_encode($response);
}
