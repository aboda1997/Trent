<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $data = json_decode(file_get_contents('php://input'), true);

    $new_mobile = isset($data['new_mobile']) ? $data['new_mobile'] : '';
    $old_mobile = isset($data['old_mobile']) ? $data['old_mobile'] : '';
    $new_ccode = isset($data['new_ccode']) ? $data['new_ccode'] : '';
    $old_ccode = isset($data['old_ccode']) ? $data['old_ccode'] : '';

    if ($new_mobile == '' || $old_mobile == '') {
        $returnArr    = generateResponse('false', "You Must Enter Both old and new Mobile Number", 400);
    }
    else if ($new_ccode == '' || $old_ccode == '') {
        $returnArr    = generateResponse('false', "You Must Enter Both old and new Mobile Number Country Code ", 400);
    }
    else if (!validateEgyptianPhoneNumber($new_mobile, $new_ccode)['status']) {
        $returnArr    = generateResponse('false', 'New Mobile Is '.validateEgyptianPhoneNumber($new_mobile,$new_ccode)['response'], 400);
    }else if (!validateEgyptianPhoneNumber($old_mobile, $old_ccode)['status']) {
        $returnArr    = generateResponse('false', 'Old Number Is ' . validateEgyptianPhoneNumber($old_mobile , $old_ccode)['response'], 400);
    }
    else if ($old_mobile == $new_mobile) {
        $returnArr    = generateResponse('false', 'You Must Enter Two Different Numbers ', 400);
    } 
    else {

        $new_mobile = strip_tags(mysqli_real_escape_string($rstate, $new_mobile));
        $old_mobile = strip_tags(mysqli_real_escape_string($rstate, $old_mobile));

        $counter = $rstate->query("select id from tbl_user where status = 1 and  mobile='" . $old_mobile . "'");
        $otp = rand(111111, 999999);
        $message = "Your OTP is: $otp";
        $checkmob = $rstate->query("select * from tbl_user where status = 1 and  mobile='" . $new_mobile . "'");

        if ($checkmob->num_rows != 0) {
            $returnArr    = generateResponse('false', "New Mobile Number Already Used!", 400);
        } else {

            if ($counter->num_rows != 0) {
                $table = "tbl_user";
                
                $field = array('new_mobile' => $new_mobile,  'otp' => $otp);
                $where = "where mobile=" . '?' . "";
                $where_conditions = [$old_mobile];

                $h = new Estate();
                $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
                $result = sendMessage([$new_ccode.$new_mobile], $message);

                if ($result) {
                    $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200);
                } else {
                    $returnArr    = generateResponse('false', "Something Went Wrong While Sending OTP Please Try Again", 400);
                }
            } else {
                $returnArr    = generateResponse('false', "Old Mobile Not Matched!!", 400);
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
