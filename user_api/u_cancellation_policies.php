<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';


    $data = array();

    $setting = $rstate->query("select cancellation_policies from tbl_setting")->fetch_assoc();

    $cancellation_policies = json_decode($setting['cancellation_policies'], true);

    // Check if decoding was successful and if the expected key exists
    if (json_last_error() === JSON_ERROR_NONE && is_array($cancellation_policies) && isset($cancellation_policies[$lang_code])) {
        $data['cancellation_policies'] = $cancellation_policies[$lang_code];
    } else {
        // Provide a default fallback value
        $data['cancellation_policies'] = "";
    }
    
    $returnArr = generateResponse(
        'true',
        "Cancellation Policies Exist!",
        200,
    
             $data
        
    );

    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
