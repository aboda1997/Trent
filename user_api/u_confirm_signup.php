<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
try{
$data = json_decode(file_get_contents('php://input'), true);


$name     = strip_tags(mysqli_real_escape_string($rstate, $data['name']));
$email = isset($data['email']) ? trim($data['email']) : "";
$email = strip_tags(mysqli_real_escape_string($rstate, $email));
$mobile    = strip_tags(mysqli_real_escape_string($rstate, $data['mobile']));
$ccode     = strip_tags(mysqli_real_escape_string($rstate, $data['ccode']));
$password  = strip_tags(mysqli_real_escape_string($rstate, $data['password']));
$refercode = strip_tags(mysqli_real_escape_string($rstate, $data['refercode']));
$otp = strip_tags(mysqli_real_escape_string($rstate, $data['otp']));


if ( $mobile == '') {
    
    $returnArr    = generateResponse('false', "Mobile Number Are Required!", 400);

}

else if ( $otp == '') {
    
    $returnArr    = generateResponse('false', "OTP Are Required!", 400);

}else{

    $checkmob   = $rstate->query("select otp from tbl_user where status = 1 and mobile=" . $mobile . "");
    if ($checkmob->num_rows != 0 && $checkmob->fetch_assoc()['otp'] == $otp ) {
        $updateQuery = "UPDATE tbl_user SET verified = 1 WHERE mobile = " . $mobile;
        $rstate->query($updateQuery);
        $c   = $rstate->query("select id, name , email , mobile , ccode  from tbl_user where mobile=" . $mobile . "")->fetch_assoc();
        
        $returnArr    = generateResponse('true', "SignUp Successfully", 200 , array("user_login" => $c ));
    }  else {
        $returnArr    = generateResponse('false', "OTP OR Mobile Number Not Valid", 400);

    }
}        
echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile() ,  $e->getLine());
    echo $returnArr;
}