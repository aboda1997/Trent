<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Max-Age: 86400');
    http_response_code(200);
    exit();
}
try {
    $data = json_decode(file_get_contents('php://input'), true);
    $mobile = isset($data['mobile']) ? $data['mobile'] : '';
    $is_new_user = isset($data['is_new_user']) ? $data['is_new_user'] : true;

    if ($mobile == '') {
        $returnArr    = generateResponse('false', "You must Enter Mobile Number", 400);
    }else if (!validateEgyptianPhoneNumber($mobile)['status']) {
        $returnArr    = generateResponse('false',   validateEgyptianPhoneNumber($new_mobile)['response'], 400);
    } else {
        $checkmob   = $rstate->query("select * from tbl_user where status =1 and verified =1 and mobile=" . $mobile . "");

        $otp = rand(111111, 999999);
        $message = "Your OTP is: $otp";

        if($is_new_user){
            if ($checkmob->num_rows != 0) { 
                $returnArr    = generateResponse('false', "Mobile Number Already Exists", 400);

            }else{

            $result = sendMessage([$mobile] , $message);
            if($result){
                $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200);

            }else{
                $returnArr    = generateResponse('false', "Something Went Wrong Try Again", 400);

            }
        }
        }else{
            $result = sendMessage([$mobile] , $message);
            if($result){
                $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200);

            }else{
                $returnArr    = generateResponse('false', "Something Went Wrong Try Again", 400);

            }
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
