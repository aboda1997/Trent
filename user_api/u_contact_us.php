<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {


    $data = array();

    $setting = $rstate->query("select contact_us_email , REPLACE(contact_us_mobile, ' ', '') as  contact_us_mobile	 	from tbl_setting")->fetch_assoc();

    $data['email'] = $setting['contact_us_email'];
    $data['mobile'] =  str_replace(' ', '', $setting['contact_us_mobile']); 

    $returnArr = generateResponse(
        'true',
        "Contact Us Data  Exist!",
        200,
    array(
        "contact_us" => $data,

    )
             
    );

    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
