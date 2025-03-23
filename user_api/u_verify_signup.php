<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/estate.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try{
$data = json_decode(file_get_contents('php://input'), true);
function generate_random()
{
    require dirname(dirname(__FILE__)) . '/include/reconfig.php';
    $six_digit_random_number = mt_rand(100000, 999999);
    $c_refer                 = $rstate->query("select * from tbl_user where refercode=" . $six_digit_random_number . "")->num_rows;
    if ($c_refer != 0) {
        generate_random();
    } else {
        return $six_digit_random_number;
    }
}
$name     = strip_tags(mysqli_real_escape_string($rstate, $data['name']));
$email = isset($data['email']) ? trim($data['email']) : "";
$email = strip_tags(mysqli_real_escape_string($rstate, $email));
$mobile    = strip_tags(mysqli_real_escape_string($rstate, $data['mobile']));
$ccode     = strip_tags(mysqli_real_escape_string($rstate, $data['ccode']));
$password  = strip_tags(mysqli_real_escape_string($rstate, $data['password']));
$refercode = strip_tags(mysqli_real_escape_string($rstate, $data['refercode']));


if ($name == '' or $mobile == '' or $password == '' or $ccode == '') {
    
    $returnArr    = generateResponse('false', "Something Went Wrong!", 400);

} else if (!validateName($name , 'Full Name' , 50)['status']) {
    $returnArr    = generateResponse('false', validateName($name , 'Full Name' , 50 )['response'], 400);
} else if (!validateEgyptianPhoneNumber($mobile )['status']) {
    $returnArr    = generateResponse('false', validateEgyptianPhoneNumber($mobile )['response'], 400);
} 
else if (!validatePassword($password )['status']) {
    $returnArr    = generateResponse('false', validatePassword($password )['response'], 400);
}
else if ($ccode !== "+20") {
    $returnArr    = generateResponse('false', "Not Supported Country Code", 400);
}
else if ($email !== null && $email !== '' && (!validateEmail($email)['status'])) {

    $returnArr    = generateResponse('false', validateEmail($email)['response'], 400);
}

else {
    $otp = rand(111111, 999999);
    $message = "Your OTP is: $otp";

    
    $checkmob   = $rstate->query("select * from tbl_user where mobile=" . $mobile . "");
    $checkemail = $rstate->query("select * from tbl_user where email='" . $email . "'");
    
    if ($checkmob->num_rows != 0) {
        $returnArr    = generateResponse('false', "Mobile Number Already Used!", 400);
    }  else {
        
        if ($refercode != '') {
            $c_refer = $rstate->query("select * from tbl_user where refercode=" . $refercode . "")->num_rows;
            if ($c_refer != 0) {
                
                $timestamp    = date("Y-m-d H:i:s");
                $prentcode    = generate_random();
                $wallet       = $rstate->query("select * from tbl_setting")->fetch_assoc();
                $table        = "tbl_user";
                $field_values = array(
                    "name",
                    "email",
                    "mobile",
                    "reg_date",
                    "password",
                    "ccode",
                    "refercode",
                    "parentcode",
                    "is_owner",
                    "verified",
                    "otp"
                );
                $data_values  = array(
                    "$name",
                    "$email",
                    "$mobile",
                    "$timestamp",
                    "$password",
                    "$ccode",
                    "$prentcode",
                    "$refercode",
                    1,
                    0,
                    $otp
                );
                
                $h     = new Estate();
                $check = $h->restateinsertdata_Api_Id($field_values, $data_values, $table);

                $result = sendMessage([$mobile] , $message);
                if($result){
                    $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200);
    
                }else{
                    $returnArr    = generateResponse('false', "Something Went Wrong Try Again", 400);
    
                }

            } else {
                $returnArr    = generateResponse('false', "Refer Code Not Found Please Try Again!!", 400);

            }
        } else {
            $timestamp    = date("Y-m-d H:i:s");
            $prentcode    = generate_random();
            $table        = "tbl_user";
            $field_values = array(
                "name",
                "email",
                "mobile",
                "reg_date",
                "password",
                "ccode",
                "refercode",
                "is_owner",
                "verified",
                "otp"
            );
            $data_values  = array(
                "$name",
                "$email",
                "$mobile",
                "$timestamp",
                "$password",
                "$ccode",
                "$prentcode",
                1,
                0,
                $otp
            );
            $h            = new Estate();
            $check        = $h->restateinsertdata_Api_Id($field_values, $data_values, $table);

            $result = sendMessage([$mobile] , $message);

            if($result){
                $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200);

            }else{
                $returnArr    = generateResponse('false', "Something Went Wrong Try Again", 400);

            }
        }
        
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