<?php
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require  'get_pay_status.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : 0;
    $method_key = isset($_POST['method_key']) ? $_POST['method_key'] : '';
    $merchant_ref_number = isset($_POST['merchant_ref_number']) ? $_POST['merchant_ref_number'] : null;

    $methods  = AppConstants::getAllMethodKeys();
    $lang_ = load_specific_langauage($lang);
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : null;

    if ($uid == null) {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if (!in_array($method_key, $methods)) {
        $returnArr    = generateResponse('false', $lang_["invalid_payment_method"], 400);
    } else if ($item_id == null) {
        $returnArr = generateResponse('false',  $lang_["item_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($booking_id, 'tbl_book', ' uid  =' . $uid . "  and book_status =  'Confirmed'") === false) {
        $returnArr    = generateResponse('false', $lang_["booking_not_available"], 400);
    } else if (validateIdAndDatabaseExistance($booking_id, 'tbl_book', ' uid  =' . $uid . "  and book_status =  'Confirmed'" . "  and item_id =  '$item_id'" ) === false) {
        $returnArr    = generateResponse('false',  $lang_["general_validation_error"], 400);
    } else if (validatePeriod($booking_id) === false) {
        $returnArr    = generateResponse('false', $lang_["booking_expired"], 400);
    } else {


        $checkQuery1 = "SELECT *  FROM tbl_book WHERE id=  " . $booking_id .  "";
        $book_data = $rstate->query($checkQuery1)->fetch_assoc();
        $prop_id = $book_data['prop_id'];
        $checkQuery = "SELECT *  FROM tbl_property WHERE id=  " . $prop_id .  "";
        $res_data = $rstate->query($checkQuery)->fetch_assoc();
        $balance = '0.00';
        $sel = $rstate->query("select message,status,amt,tdate from wallet_report where uid=" . $uid . " order by id desc");
        while ($row = $sel->fetch_assoc()) {

            if ($row['status'] == 'Adding') {
                $balance = bcadd($balance, $row['amt'], 2);
            } else if ($row['status'] == 'Withdraw') {
                $balance = bcsub($balance, $row['amt'], 2);
            }
        }


        $table = "tbl_book";

        $fp = array();
        $vr = array();
        $fp['id'] = $res_data['id'];
        $add_user_id = $res_data['add_user_id'];
        $user = $rstate->query("select is_owner , mobile	, ccode from tbl_user where  id= $uid  ")->fetch_assoc();
        $fp['from_date'] = $book_data['check_in'];
        $fp['to_date'] = $book_data['check_out'];
        $fp['days'] = $book_data['total_day'];
        $fp['book_id'] = $booking_id;

        $fp['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where  uid= $uid and property_id=" . $res_data['id'] . "")->num_rows;

        $titleData = json_decode($res_data['title'], true);
        $fp['title'] = $titleData[$lang];

        $rdata_rest = $rstate->query("SELECT sum(rating)/count(*) as rate_rest FROM tbl_rating where prop_id=" . $res_data['id'] . "")->fetch_assoc();
        $fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 1, '.', '');

        $fp['price'] = $res_data['price'];

        $fp['wallet_balance'] = $balance;
        $periods = [
            "d" => ["ar" => "يومي", "en" => "daily"],
            "m" => ["ar" => "شهري", "en" => "monthly"]
        ];

        $fp['period_type'] =  $periods[$res_data['period']][$lang];

        $imageArray = array_filter(explode(',', $res_data['image'] ?? ''));

        // Loop through each image URL and push to $vr array
        foreach ($imageArray as $image) {
            $vr[] = array('img' => trim($image));
        }
        $fp['image_list'] = $vr;
        $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
        $created_at = $date->format('Y-m-d H:i:s');
        $reminder_value = $book_data['reminder_value'];
        $fp['total'] = number_format($book_data['total'], 2, '.', '');
        $fp['guest_count'] = $book_data['noguest'];
        $fp['book_status'] = $book_data['book_status'];

        $fp['reminder_value'] = number_format($reminder_value, 2, '.', '');
        $fp['partial_value'] =  number_format($book_data['total'] - $book_data['reminder_value'], 2, '.', '');
        $total_90_percent_int = (int)$reminder_value;

        if ($method_key == 'TRENT_BALANCE' && $balance <  $reminder_value) {
            $returnArr    = generateResponse('false', $lang_["insufficient_wallet_balance"], 400);
        } else if ($method_key == 'TRENT_BALANCE' && $balance >= $reminder_value) {

            $GLOBALS['rstate']->begin_transaction();
            $field_ = array('pay_status' => 'Completed', 'item_id' => '');
            $where = "where uid=" . '?' . " and id=" . '?' . "";
            $table = "tbl_book";
            $created_at1 = $date->format('Y-m-d H:i:s');

            $h = new Estate();
            $where_conditions = [$uid, $booking_id];
            $check = $h->restateupdateData_Api($field_, $table, $where, $where_conditions);
            if (!$check) {
                throw new Exception("Insert failed");
            }
            $field_values1 = ["uid", 'status', 'amt', 'tdate'];
            $data_values1  = [$uid, 'Withdraw', $reminder_value, $created_at1];
            $table1 = 'wallet_report';

            $check = $h->restateinsertdata_Api($field_values1, $data_values1, $table1);
            if (!$check) {
                throw new Exception("Insert failed");
            }

            $GLOBALS['rstate']->commit();

            $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                "booking_details" => $fp,
            ));
        } else {

            if (getPaymentStatus($merchant_ref_number, $item_id,  $total_90_percent_int)['status']) {


                $GLOBALS['rstate']->begin_transaction();
                $field_ = array('pay_status' => 'Completed', 'item_id' => '');
                $where = "where uid=" . '?' . " and id=" . '?' . "";
                $table = "tbl_book";
                $h = new Estate();
                $where_conditions = [$uid, $booking_id];
                $check = $h->restateupdateData_Api($field_, $table, $where, $where_conditions);
                if (!$check) {
                    throw new Exception("Insert failed");
                }


                $GLOBALS['rstate']->commit();

                $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                    "booking_details" => $fp,
                ));
            } else {
                $returnArr    = generateResponse('false', $lang_["payment_validation_failed"], 400);
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
