<?php
require dirname(dirname(__FILE__),2) . '/include/reconfig.php';
require dirname(dirname(__FILE__),2) . '/include/helper.php';
require dirname(dirname(__FILE__),2) . '/include/validation.php';
require_once dirname(dirname(__FILE__),2) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';


    $data = array();
$fawry_credentials = array();
    $setting = $rstate->query("select merchant_code , secure_key from tbl_setting")->fetch_assoc();

    $fawry_credentials['merchant_code'] = $setting['merchant_code'];
    $fawry_credentials['secure_key'] = $setting['secure_key'];

    $data['fawry_credentials'] = $fawry_credentials;
   
    
    $returnArr = generateResponse(
        'true',
        "Fawry Credentials Exist!",
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
