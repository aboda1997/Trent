<?php
require dirname(dirname(__FILE__) ) . '/include/reconfig.php';
require dirname(dirname(__FILE__) ) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__)) . '/user_api/estate.php';
require dirname(dirname(__FILE__)) . '/include/constants.php';

try {
  // Get all users who might be owners
  $users = $rstate->query("SELECT id FROM tbl_user")->fetch_all(MYSQLI_ASSOC);

  foreach ($users as $user) {
    $user_id = $user['id'];

    // Count approved, non-deleted properties for this user
    $check_owner = $rstate->query("SELECT COUNT(*) as property_count FROM tbl_property 
                                  WHERE is_approved = 1 AND add_user_id = " . $user_id . " AND is_deleted = 0")
      ->fetch_assoc()['property_count'];

    // Update user's owner status based on property count
    if ($check_owner >= AppConstants::Property_Count) {
      $rstate->query("UPDATE tbl_user SET is_owner = 0 WHERE id = " . $user_id);
    } else {
      $rstate->query("UPDATE tbl_user SET is_owner = 1 WHERE id = " . $user_id);
    }
  }
} catch (Exception $e) {
  // Handle exceptions and return an error response
  $returnArr = generateResponse('false', "An error occurred!", 500, array(
    "error_message" => $e->getMessage()
  ), $e->getFile(),  $e->getLine());
  echo $returnArr;
}
