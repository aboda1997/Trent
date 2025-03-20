<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$rawInput = file_get_contents('php://input');

$rawInput = preg_replace('/^\xEF\xBB\xBF/', '', $rawInput);
$rawInput = trim($rawInput);
$rawInput = preg_replace('/\x{00A0}/u', ' ', $rawInput);
$data = json_decode($rawInput, true);



if (!isset($data['mobile']) || !isset($data['password']) || !isset($data['ccode'])) {
    
    $returnArr = array(
        "ResponseCode" => "401",
        "Result" => "false",
        "ResponseMsg" => "Something Went Wrong!"
    );
} else {
    $mobile   = strip_tags(mysqli_real_escape_string($rstate, $data['mobile']));
    $ccode    = strip_tags(mysqli_real_escape_string($rstate, $data['ccode']));
    $password = strip_tags(mysqli_real_escape_string($rstate, $data['password']));
    
    $chek   = $rstate->query("select * from tbl_user where  (mobile='" . $mobile . "' or email='" . $mobile . "') and ccode='" . $ccode . "' and status = 1 and password='" . $password . "'");
    $status = $rstate->query("select * from tbl_user where status = 1");
    if ($status->num_rows != 0) {
        if ($chek->num_rows != 0) {
            $c = $rstate->query("select * from tbl_user where  (mobile='" . $mobile . "' or email='" . $mobile . "')  and ccode='" . $ccode . "' and status = 1 and password='" . $password . "'")->fetch_assoc();
            
            $returnArr = array(
                "UserLogin" => $c,
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Login successfully!"
            );
        } else {
            $returnArr = array(
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Invalid Mobile Number Or Email Address or Password!!!"
            );
        }
    } else {
        $returnArr = array(
            "ResponseCode" => "401",
            "Result" => "false",
            "ResponseMsg" => "Your Status Deactivate!!!"
        );
    }
}

echo json_encode($returnArr);