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

  $mobile = isset($data['mobile']) ? $data['mobile'] : '';
  $password = isset($data['password']) ? $data['password'] : '';

  if ($mobile == '' or $password == '') {
    $returnArr    = generateResponse('false', "You Must Send Both mobile and new password!", 400);
  } else if (!validatePassword($password)['status']) {
    $returnArr    = generateResponse('false', validatePassword($password)['response'], 400);
  }else if (!validateEgyptianPhoneNumber($mobile )['status']) {
    $returnArr    = generateResponse('false', validateEgyptianPhoneNumber($mobile )['response'], 400);
}  else {
    $mobile = strip_tags(mysqli_real_escape_string($rstate, $mobile));
    $password = strip_tags(mysqli_real_escape_string($rstate, $password));

    $counter = $rstate->query("select id , password from tbl_user where status =1 and  mobile='" . $mobile . "'");
    $otp = rand(111111, 999999);
    $message = "Your OTP is: $otp";

    if ($counter->num_rows != 0) {
      $table = "tbl_user";

      $h = new Estate();

      $field = array(  "new_password" => $password ,   'otp' => $otp);
      $where = "where mobile=" . '?' . "";
      $where_conditions = [$mobile];
      $check = $h->restateupdateData_Api($field, $table, $where, $where_conditions);
      $result = sendMessage([$mobile], $message);

      if ($result) {
        $returnArr    = generateResponse('true', "OTP message was sent successfully!", 200);
      } else {
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
