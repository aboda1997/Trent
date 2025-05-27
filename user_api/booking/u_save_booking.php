<?php
require_once dirname(dirname(__FILE__), 2) . '/include/validation.php';
require_once dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/send_notification.php';
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require  'fawry_pull_status.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $lang = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
    $prop_id = isset($_POST['prop_id']) ? $_POST['prop_id'] : null;
    $merchant_ref_number = isset($_POST['merchant_ref_number']) ? $_POST['merchant_ref_number'] : null;
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : 0;
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;
    $confirm_guest_rules = isset($_POST['confirm_guest_rules']) ? $_POST['confirm_guest_rules'] : 'false';
    $guest_counts = isset($_POST['guest_counts']) ? $_POST['guest_counts'] : 0;
    $method_key = isset($_POST['method_key']) ? $_POST['method_key'] : '';
    $methods  = AppConstants::getAllMethodKeys();
    [$valid, $message] = validateDates($from_date, $to_date);
    if ($prop_id  == null) {
        $returnArr    = generateResponse('false', "Property id is required", 400);
    } else if ($uid == null) {
        $returnArr    = generateResponse('false', "User id is required", 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
        $returnArr    = generateResponse('false', "User id is not exists", 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', ' status = 1 and is_approved =1 and is_deleted =0') === false) {
        $returnArr    = generateResponse('false', "This property  is not Available", 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', '  add_user_id =' . $uid . '') === true) {
        $returnArr    = generateResponse('false', "Not Allow to book Your Own Property", 400);
    } else if ($valid == false) {
        $returnArr    = generateResponse('false', $message, 400);
    } else if ($confirm_guest_rules  == 'false') {
        $returnArr    = generateResponse('false', "You Must confirm Guest Rules", 400);
    } else if (!in_array($method_key, $methods)) {
        $returnArr    = generateResponse('false', "Payment method not valid", 400);
    } else {
        [$days, $days_message] = processDates($from_date, $to_date);
        $date_list = get_dates($prop_id, $rstate);
        [$status, $status_message] = validateDateRange($from_date, $to_date, $date_list);
        $checkQuery = "SELECT *  FROM tbl_property WHERE id=  " . $prop_id .  "";
        $res_data = $rstate->query($checkQuery)->fetch_assoc();
        $balance = '0.00';
        $sel = $rstate->query("select message,status,amt,tdate from wallet_report where uid=" . $uid . " order by id desc");
        $non_completed_data = $rstate->query("select id from tbl_non_completed where id=" . $item_id)->num_rows;
        while ($row = $sel->fetch_assoc()) {

            if ($row['status'] == 'Adding') {
                $balance = bcadd($balance, $row['amt'], 2);
            } else if ($row['status'] == 'Withdraw') {
                $balance = bcsub($balance, $row['amt'], 2);
            }
        }
        if ($non_completed_data == 0) {
            $returnArr    = generateResponse('false', "Something Went Wrong Enure that sent data are correct", 400);
        } elseif ($days == 0) {
            $returnArr    = generateResponse('false', $days_message, 400);
        } else if ($status  == false) {
            $returnArr    = generateResponse('false', $status_message, 400);
        } else if ((int)$res_data['plimit'] !== 0 &&  $guest_counts > $res_data['plimit']) {
            $returnArr    = generateResponse('false', "Guest count are exceed persons limits", 400);
        } else if (
            (int)$res_data['min_days'] !== 0 && (int)$res_data['max_days'] !== 0  &&
            ($days < (int)$res_data['min_days'] || $days > (int)$res_data['max_days'])
        ) {
            $returnArr    = generateResponse('false', "The selected dates are not  within the allowed limit [" . $res_data['min_days'] . ',' . $res_data['max_days'] . "] days", 400);
        } else {

            $table = "tbl_book";

            $fp = array();
            $vr = array();
            $set = $rstate->query("select owner_fees, property_manager_fees,tax ,gateway_percent_fees,gateway_money_fees from tbl_setting ")->fetch_assoc();
            $prop = $rstate->query("select add_user_id  from tbl_property where  id= $prop_id  ")->fetch_assoc();
            $fp['id'] = $res_data['id'];
            $add_user_id = $res_data['add_user_id'];
            $user = $rstate->query("select is_owner , mobile	, ccode from tbl_user where  id= $add_user_id  ")->fetch_assoc();

            $fp['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where  uid= $uid and property_id=" . $res_data['id'] . "")->num_rows;

            $titleData = json_decode($res_data['title'], true);
            $fp['title'] = $titleData[$lang];

            $rdata_rest = $rstate->query("SELECT sum(rating)/count(*) as rate_rest FROM tbl_rating where prop_id=" . $res_data['id'] . "")->fetch_assoc();
            $fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 1, '.', '');

            $fp['price'] = $res_data['price'];
            $fp['from_date'] = $from_date;
            $fp['to_date'] = $to_date;
            $fp['days'] = $days;
            $fp['guest_count'] = $guest_counts;
            $fp['tax_percent'] = $set['tax'];
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
            $price = ($res_data['period'] == 'd') ? $res_data['price'] : ($res_data['price'] / 30);
            $fp['sub_total'] = $days * $price;
            $trent_fess = ($user['is_owner'] == 0) ? ($set["property_manager_fees"] * $fp['sub_total']) / 100  : ($set["owner_fees"] * $fp['sub_total']) / 100;
            $deposit_fees = $res_data["security_deposit"];

            $fp['taxes'] = ($trent_fess * $set['tax']) / 100;
            $fp['service_fees'] = (($days * $price) * $set['gateway_percent_fees']) / 100 + $set['gateway_money_fees'];
            $fp['final_total'] = $fp['sub_total'] + $fp['taxes'] + $fp['service_fees'] + $deposit_fees + $trent_fess;
            $fp['deposit_fees'] = $res_data['security_deposit'];
            $fp['trent_fees'] = $trent_fess;
            $propertyName = $titleData["ar"];
            $message = "لديك حجز جديد للعقار ($propertyName)\nمع تحيات فريق ت-رينت";
            $mobile = $user["mobile"];
            $ccode = $user["ccode"];
            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $created_at = $date->format('Y-m-d');
            $total_as_int = (int)$fp['final_total'];

            $fp['book_status'] = 'Booked';

            if ($method_key == 'TRENT_BALANCE' && $balance <  $fp['final_total']) {
                $returnArr    = generateResponse('false', "Wallet balance not sufficent", 400);
            }else if ($method_key == 'TRENT_BALANCE' && $balance >= $fp['final_total']) {

                $GLOBALS['rstate']->begin_transaction();

                $field_values = ["prop_id", 'total_day', "check_in", "check_out",   "uid", "book_date", "book_status", "prop_price", "prop_img", "prop_title", "add_user_id", "noguest",  "subtotal", "tax", "trent_fees", "service_fees", "deposit_fees", "total"];
                $data_values = [$res_data['id'], $days, $from_date, $to_date,   $uid, $created_at, "Booked", $res_data['price'], $res_data['image'], $res_data['title'], $res_data['add_user_id'], "$guest_counts", $fp['sub_total'],  $fp['taxes'], $trent_fess, $fp['service_fees'],  $fp['deposit_fees'],  $fp['final_total']];

                $h = new Estate();
                $check = $h->restateinsertdata_Api($field_values, $data_values, $table);
                $fp['book_id'] = $check;

                $whatsapp = sendMessage([$ccode . $mobile], $message);
                $firebase_notification = sendFirebaseNotification($message, $message, $add_user_id, 'booking_id', $check, $res_data['image']);
                $created_at1 = $date->format('Y-m-d H:i:s');

                $field_values1 = ["uid", 'status', 'amt', 'tdate'];
                $data_values1  = [$uid, 'Withdraw', $fp['final_total'], $created_at1];
                $table1 = 'wallet_report';

                $check = $h->restateinsertdata_Api($field_values1, $data_values1, $table1);
                $GLOBALS['rstate']->commit();

                $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                    "booking_details" => $fp,
                ));
            } 
            else {
                $get_secure_key = $rstate->query("select merchant_code ,secure_key from tbl_setting ")->fetch_assoc();
                $secureKey = $get_secure_key['secure_key'];
                $merchantCode = $get_secure_key['merchant_code'];
                $decrypted_secure_key =  decryptData($secureKey,  dirname(dirname(__FILE__), 2) . '/keys/private.pem')['data'];
                $decrypted_code = decryptData($merchantCode,  dirname(dirname(__FILE__), 2) . '/keys/private.pem')['data'];

                $check_push_pay = $rstate->query("SELECT orderStatus, orderAmount, itemId AS itemCode FROM payment WHERE merchantRefNumber = '" . $merchant_ref_number . "'");
                // Trim all fields to avoid hidden characters
                $concatenatedString =
                    trim($decrypted_code) .
                    trim($merchant_ref_number) .
                    trim($decrypted_secure_key);
                $expectedSignature = hash('sha256', $concatenatedString);
                $check_pull_pay = getFawryPaymentStatus($decrypted_code, $merchant_ref_number, $expectedSignature);


               if(isPaymentValid($check_push_pay , $check_pull_pay ,$item_id , $total_as_int)){

               
                $GLOBALS['rstate']->begin_transaction();

                $field_values = ["prop_id", 'total_day', "check_in", "check_out",   "uid", "book_date", "book_status", "prop_price", "prop_img", "prop_title", "add_user_id", "noguest",  "subtotal", "tax", "trent_fees", "service_fees", "deposit_fees", "total"];
                $data_values = [$res_data['id'], $days, $from_date, $to_date,   $uid, $created_at, "Booked", $res_data['price'], $res_data['image'], $res_data['title'], $res_data['add_user_id'], "$guest_counts", $fp['sub_total'],  $fp['taxes'], $trent_fess, $fp['service_fees'],  $fp['deposit_fees'],  $fp['final_total']];

                $h = new Estate();
                $check = $h->restateinsertdata_Api($field_values, $data_values, $table);
                $fp['book_id'] = $check;

                $whatsapp = sendMessage([$ccode . $mobile], $message);
                $firebase_notification = sendFirebaseNotification($message, $message, $add_user_id, 'booking_id', $check, $res_data['image']);
             
                $GLOBALS['rstate']->commit();

                $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                    "booking_details" => $fp,
                ));
            }else{
                $returnArr    = generateResponse('false', "Payment validation failed!", 400);

            }
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

function validateDates(?string $from_date, ?string $to_date): array
{
    // Check presence
    [$valid, $message] = checkDatesPresent($from_date, $to_date);
    if (!$valid) {
        return [false, $message];
    }

    // Check format
    [$valid, $message] = checkDateFormat($from_date, $to_date);
    if (!$valid) {
        return [false, $message];
    }

    return [true, 'Dates are valid'];
}

/**
 * Check if dates are present
 * Returns [bool $success, string $message]
 */
function checkDatesPresent(?string $from_date, ?string $to_date): array
{
    if (empty($from_date)) {
        return [false, "from_date is required"];
    }

    if (empty($to_date)) {
        return [false, "to_date is required"];
    }

    return [true, "Both dates are present"];
}

/**
 * Check date format validity
 * Returns [bool $success, string $message]
 */
function checkDateFormat(string $from_date, string $to_date): array
{
    if (!strtotime($from_date)) {
        return [false, "Invalid from_date format. Use YYYY-MM-DD"];
    }

    if (!strtotime($to_date)) {
        return [false, "Invalid to_date format. Use YYYY-MM-DD"];
    }

    return [true, "Date formats are valid"];
}

/**
 * Process dates and calculate difference
 * Returns [int $days, string $message]
 */
function processDates(string $from_date, string $to_date): array
{
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($to_date);

    // Validate order
    if ($date1 > $date2) {
        return [0, "from_date must be earlier than to_date"];
    }

    $interval = $date1->diff($date2);
    $days = $interval->days + 1;

    return [
        $days,
        "Successfully calculated $days days between dates"
    ];
}

function get_dates(string $pro_id, $rstate)
{
    $sql = "SELECT check_in, check_out FROM tbl_book where prop_id=" . $pro_id . " and book_status != 'Cancelled'";
    $result = $rstate->query($sql);
    $date_list = [];
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $date_list = array_merge($date_list, getDatesFromRange($row['check_in'], $row['check_out']));
    }

    // Remove duplicate dates
    $date_list = array_unique($date_list);
    // Sort the dates
    sort($date_list);
    return $date_list;
}
function getDatesFromRange($start, $end)
{
    $dates = [];
    $current = strtotime($start);
    $end = strtotime($end);

    while ($current <= $end) {
        $dates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }

    return $dates;
}
function validateDateRange($from_date, $to_date, $date_list)
{


    // Check for conflicts with date_list
    $current = strtotime($from_date);
    $end = strtotime($to_date);

    while ($current <= $end) {
        $current_date = date('Y-m-d', $current);
        if (in_array($current_date, $date_list)) {
            return [false, "Conflict from date and to date range with: $current_date"];
        }
        $current = strtotime('+1 day', $current);
    }

    return [true, "Valid date range"];
}
function isPaymentValid($check_push_pay, $check_pull_pay, $item_id, $total_as_int) {
    // Check if push payment has rows and pull payment status exists
    if ($check_push_pay->num_rows && $check_pull_pay["status"]) {
        $check_push_pay_data = $check_push_pay->fetch_assoc();

        // Convert amounts to integers for strict comparison
        $push_amount = (int)$check_push_pay_data['orderAmount'];
        $pull_amount = (int)$check_pull_pay['orderAmount'];
        
        // Verify all conditions with integer comparison
        return (
            $check_push_pay_data['orderStatus'] == 'PAID' &&
            $check_push_pay_data['itemCode'] == $item_id &&
            $check_pull_pay['orderStatus'] == 'PAID' &&
            $check_pull_pay['itemCode'] == $item_id &&
            $push_amount === $total_as_int &&
            $pull_amount === $total_as_int
        );
    }
    
    return false;
}