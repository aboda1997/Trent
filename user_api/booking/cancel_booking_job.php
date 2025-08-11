<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';

$date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
$currentDate = $date->format('Y-m-d H:i:s');

// Update database
try {
    $h = new Estate();
    $sel = $rstate->query("SELECT `id`, `check_in`, `check_out`, `prop_title`, `add_user_id`, `uid`, `prop_img` 
                        FROM `tbl_book` 
                        WHERE `book_status` = 'Booked' 
                        ORDER BY `id` DESC");

    $sel1 = $rstate->query("SELECT `id`, `check_in`, `check_out`, `prop_title`, `add_user_id`, `uid`, `prop_img` 
                        FROM `tbl_book` 
                        WHERE `book_status` = 'Confirmed' 
                        and `pay_status`   = 'Partial'
                        ORDER BY `id` DESC");
    $sel2 = $rstate->query("SELECT `id`, `check_in`, `check_out`, `prop_title`, `add_user_id`, `uid`, `prop_img` 
                        FROM `tbl_book` 
                        WHERE `book_status` = 'Confirmed' 
                        and `pay_status`   = 'Completed'
                        ORDER BY `id` DESC");
    $cairoTimezone = new DateTimeZone('Africa/Cairo');

    // Get current time in Cairo
    $currentDateTime = new DateTime('now', $cairoTimezone);

    while ($row = $sel->fetch_assoc()) {
        $add_user_id = $row['add_user_id'];
        $uid = $row['uid'];

        $check_in_str = $row['check_in'];
        $check_out_str = $row['check_out'];

        $book_id = $row['id'];
        if (validateCancelBooking($book_id)) {
            $res1 = refundMoney($uid, $book_id);
        }
    }

    while ($row = $sel1->fetch_assoc()) {
        $add_user_id = $row['add_user_id'];
        $uid = $row['uid'];

        $check_in_str = $row['check_in'];
        $check_out_str = $row['check_out'];
        $book_id = $row['id'];
        $flag = validatePeriod($book_id);
        if ($flag == false) {
            cancel_booking($book_id);
        }
    }
      while ($row = $sel2->fetch_assoc()) {
       
        $book_id = $row['id'];
        $flag = validateCheckInDate($book_id, $currentDate);
        if ($flag == false) {
           // cancel_booking($book_id);
        }
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
