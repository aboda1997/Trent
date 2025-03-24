<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
try {
    $rawInput = file_get_contents('php://input');

    $rawInput = preg_replace('/^\xEF\xBB\xBF/', '', $rawInput);
    $rawInput = trim($rawInput);
    $rawInput = preg_replace('/\x{00A0}/u', ' ', $rawInput);
    $data = json_decode($rawInput, true);

    $mobile   = strip_tags(mysqli_real_escape_string($rstate, $data['mobile']));
    $ccode    = strip_tags(mysqli_real_escape_string($rstate, $data['ccode']));
    $password = strip_tags(mysqli_real_escape_string($rstate, $data['password']));

    $mobile = isset($data['mobile']) ? strip_tags(mysqli_real_escape_string($rstate, $data['mobile'])) : '';
    $ccode = isset($data['ccode']) ? strip_tags(mysqli_real_escape_string($rstate, $data['ccode'])) : '';
    $password = isset($data['password']) ? strip_tags(mysqli_real_escape_string($rstate, $data['password'])) : '';

    if (!isset($data['mobile']) || !isset($data['password']) || !isset($data['ccode'])) {
        $returnArr    = generateResponse('false', "Something Went Wrong!", 400);
    } else if (!validateEgyptianPhoneNumber($mobile)['status']) {
        $returnArr    = generateResponse('false', validateEgyptianPhoneNumber($mobile)['response'], 400);
    } else if (!validatePassword($password)['status']) {
        $returnArr    = generateResponse('false', validatePassword($password)['response'], 400);
    } else {

        $chek   = $rstate->query("select * from tbl_user where  (mobile='" . $mobile . "'  or email='" . $mobile . "') and ccode='" . $ccode . "' and status = 1 and password='" . $password . "'");
        $status = $rstate->query("select * from tbl_user where  mobile='" . $mobile . "'  and status = 1");
        $verified = $rstate->query("select * from tbl_user where  mobile='" . $mobile . "'  and verified = 1 and status = 1");
        if ($status->num_rows != 0) {

            if ($verified->num_rows != 0) {
                if ($chek->num_rows != 0) {
                    $c = $rstate->query("select id , name , email , ccode , mobile from tbl_user where  (mobile='" . $mobile . "' or email='" . $mobile . "')  and ccode='" . $ccode . "' and status = 1 and password='" . $password . "'")->fetch_assoc();
                    $returnArr    = generateResponse('true', "Login successfully!", 200, array(
                        "user_login" => $c
                    ));
                } else {
                    $returnArr    = generateResponse('false', "Invalid Mobile Number Or Email Address or Password!!!", 400);
                }
            } else {
                $returnArr    = generateResponse('false', "Your Status Not Verified!!!", 400);
            }
        } else {
            $returnArr    = generateResponse('true', "Your Status is Deactivated!!!", 400);
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
