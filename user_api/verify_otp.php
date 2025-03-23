<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $otp = isset($data['otp']) ? $data['otp'] : '';
    $mobile = isset($data['mobile']) ? $data['mobile'] : '';

    if ($mobile == '') {
        $returnArr    = generateResponse('false', "You must Enter Mobile Number", 400);
    }else  if ($otp == '') {
        $returnArr    = generateResponse('false', "You must Enter OTP Number", 400);
    }
    else if (!validateEgyptianPhoneNumber($mobile )['status']) {
        $returnArr    = generateResponse('false', validateEgyptianPhoneNumber($mobile )['response'], 400);
    }
    else {
        $checkmob   = $rstate->query("select otp from tbl_user where mobile=" . $mobile . "");


        if ($checkmob->num_rows != 0 && $checkmob->fetch_assoc()['otp'] == $otp ) { 
        $updateQuery = "UPDATE tbl_user SET verified = 1 WHERE mobile = " . $mobile;
        $rstate->query($updateQuery);
        $returnArr    = generateResponse('true', "OTP Verified Successfully!!", 200);

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
