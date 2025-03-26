<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';


    $data = array();

    $setting = $rstate->query("select privacy_policy from tbl_setting")->fetch_assoc();

    $data['privacy_policy'] = json_decode($setting['privacy_policy'], true)[$lang_code];

    $returnArr = generateResponse(
        'true',
        "Privacy Policy Exist!",
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
