<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-Type: application/json');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
try {
    $data = json_decode(file_get_contents('php://input'), true);
    $otp = isset($data['otp']) ? $data['otp'] : '';
    $mobile = isset($data['mobile']) ? $data['mobile'] : '';
    $is_change_password = isset($data['is_change_password']) ? $data['is_change_password'] : null;
    if ($mobile == '') {
        $returnArr    = generateResponse('false', "You must Enter Mobile Number", 400);
    }else  if ($otp == '') {
        $returnArr    = generateResponse('false', "You must Enter OTP Number", 400);
    }
    else if (!validateEgyptianPhoneNumber($mobile )['status']) {
        $returnArr    = generateResponse('false', validateEgyptianPhoneNumber($mobile )['response'], 400);
    }
    else {
        $checkmob   = $rstate->query("select * from tbl_user where status = 1 and mobile=" . $mobile . "");
        $data = $checkmob->fetch_assoc();
        if ($checkmob->num_rows != 0 && $data['otp'] == $otp ) { 

        if($is_change_password == true && isset($data['new_password']) && $data['new_password'] !== null && $data['new_password'] !== ''){
            $new_password = mysqli_real_escape_string($rstate, $data['new_password']);

            $updateQuery = "UPDATE tbl_user 
            SET verified = 1, password = '$new_password' , new_password = null
            WHERE mobile = " . intval($mobile);           
            $msg = "Password Updated Successufully";

        }else if($is_change_password === false && isset($data['new_mobile']) && $data['new_mobile'] !== null && $data['new_mobile'] !== '')  {
            $new_mobile = mysqli_real_escape_string($rstate, $data['new_mobile']);
            $updateQuery = "UPDATE tbl_user 
            SET verified = 1, mobile = '$new_mobile' , new_mobile = null
            WHERE mobile = " . intval($mobile);
            $msg = "Mobile Number Updated Successufully";
        }else{
            $updateQuery = "UPDATE tbl_user 
            SET verified = 1
            WHERE mobile = " . intval($mobile);           
            $msg = "Sign Up Successufully";
 
        }
        $c   = $rstate->query("select id, name , email , ccode , mobile  from tbl_user where status = 1 and  id=" . $data['id'] . "")->fetch_assoc();

        $rstate->query($updateQuery);
        $returnArr    = generateResponse('true', $msg, 200 ,  array("user_login" => $c ));

        } else {
            $returnArr    = generateResponse('false', "OTP OR Mobile Number Not Valid", 400);
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
