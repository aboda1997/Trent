<?php

function generateResponse($result, $response_message, $response_code , $data = null , $file = null , $line = null ) {
    // Create an array for the response
    $response = array(
        'result' => $result,
        'response_message' => $response_message,
        'response_code' => $response_code,
    );
    
    if ($data !== null) { 
        
        if($result == "true"){
            $response['data'] = $data;

        }else{
            $data['file'] = $file;
            $data['line'] = $line;
            $response['error'] = $data;
        }
    }
    // Set the appropriate HTTP response code (default: 200 OK)
    http_response_code($response_code);

    // Return the response as JSON
    return json_encode($response);
}


function generateDashboardResponse( $response_code,  $result,  $title ,  $response_message  , $action) {
    // Create an array for the response
    $response = array(
        'ResponseCode' => $response_code,
        'Result' => $result,
        'title' => $title,
        'message' => $response_message,
        'action' => $action,
    );
   
    // Set the appropriate HTTP response code (default: 200 OK)
    //http_response_code($response_code);

    // Return the response as JSON
    return json_encode($response);
}
function sendMessage($mobiles, $message) {
    $url = "http://whats-pro.net/backend/public/index.php/api/messages/send";
    $token = "efd2mxoGOPTwtyNl9OuufgcTnrC20ErzUKr2fh3mrwl4uAFRqVaTTY8WNyAf";
    $ccode = "EG";
    
    // Set up the request headers
    $headers = [
        "token: $token",
        "Accept: application/json",
        "Content-Type: application/json"
    ];
    
    // Prepare the payload
    $payload = [
        "phones" => $mobiles,
        "message" => $message,
        "country_code" => $ccode
    ];
    
    // Initialize cURL
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    try {
        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Check if request was successful
        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Error sending message: " . $e->getMessage());
        return false;
    } finally {
        // Close cURL resource
        curl_close($ch);
    }
}

?>