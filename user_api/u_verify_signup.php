<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__)) . '/include/validation.php';

header('Content-Type: application/json');

try{
    // Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
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
$refercode = isset($data['refercode']) ? strip_tags(mysqli_real_escape_string($rstate, $data['refercode'])) : '';


if ($name == '' or $mobile == '' or $password == '' or $ccode == '') {
    
    $returnArr    = generateResponse('false', "Something Went Wrong!", 400);

} else if (!validateName($name , 'Full Name' , 50)['status']) {
    $returnArr    = generateResponse('false', validateName($name , 'Full Name' , 50 )['response'], 400);
} else if (!validateEgyptianPhoneNumber($mobile , $ccode)['status']) {
    $returnArr    = generateResponse('false', validateEgyptianPhoneNumber($mobile ,$ccode )['response'], 400);
} 
else if (!validatePassword($password )['status']) {
    $returnArr    = generateResponse('false', validatePassword($password )['response'], 400);
}

else if ($email !== null && $email !== '' && (!validateEmail($email)['status'])) {

    $returnArr    = generateResponse('false', validateEmail($email)['response'], 400);
}

else {
    $otp = rand(111111, 999999);
    $message = "Your OTP is: $otp";

    
    $checkmob   = $rstate->query("select * from tbl_user where status = 1 and mobile=" . $mobile . "");
    $data = $checkmob->fetch_assoc();
    if ($checkmob->num_rows != 0 &&  $data['verified'] == 0  ) {
        $table = "tbl_user";

        $h = new Estate();
  
        $field = array(  "name" => $name , "email" => $email , "password" => $password , "otp" => $otp , );
        $where = "where mobile=" . '?' . "";
        $where_conditions = [$mobile];
        $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
        $result = sendMessage([$ccode.$mobile] , $message);
      
        if($result){
            $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200 , array("otp" => $otp) );

        }else{
            $returnArr    = generateResponse('false', "Something Went Wrong While Sending OTP Please Try Again", 400);

        }
    
    }else if ($checkmob->num_rows != 0 &&  $data['verified'] == 1  ) {
        $returnArr    = generateResponse('false', "Mobile Number Already Used!", 400);
    }else {
        
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
                    "$otp",

                );
                
                $h     = new Estate();
                $check = $h->restateinsertdata_Api_Id($field_values, $data_values, $table);

              $result = sendMessage([$ccode.$mobile] , $message);
                if($result){
                    $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200, array("otp" => $otp));
    
                }else{
                    $returnArr    = generateResponse('false', "Something Went Wrong While Sending OTP Please Try Again", 400);
    
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
                "$otp",

            );
            $h            = new Estate();
            $check        = $h->restateinsertdata_Api_Id($field_values, $data_values, $table);

            $result =  sendMessage([$ccode.$mobile] , $message);

            if($result){
                $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200,  array("otp" => $otp));

            }else{
                $returnArr    = generateResponse('false', "Something Went Wrong While Sending OTP Please Try Again", 400);

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