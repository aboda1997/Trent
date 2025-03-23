<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $mobile = isset($data['mobile']) ? $data['mobile'] : '';
    $is_new_user = isset($data['is_new_user']) ? $data['is_new_user'] : '';

    if ($mobile == '') {
        $returnArr    = generateResponse('false', "You must Enter Mobile Number", 400);
    } else {
        $checkmob   = $rstate->query("select * from tbl_user where mobile=" . $mobile . "");

        $otp = rand(111111, 999999);
        $message = "Your OTP is: $otp";


        if ($checkmob->num_rows != 0) { 
            $result = sendMessage([$mobile] , $message);
            if($result){
                $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200);

            }else{
                $returnArr    = generateResponse('false', "Something Went Wrong Try Again", 400);

            }
        } else {
            $returnArr    = generateResponse('false', "Mobile Not Matched!!", 400);
        }
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
