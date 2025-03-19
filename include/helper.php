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

?>