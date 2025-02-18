<?php

function generateResponse($result, $responseMsg, $statusCode ) {
    // Create an array for the response
    $response = array(
        'Result' => $result,
        'ResponseMsg' => $responseMsg,
        'ResponseCode' => $statusCode,
    );

    // Set the appropriate HTTP response code (default: 200 OK)
    http_response_code($statusCode);

    // Return the response as JSON
    return json_encode($response);
}
