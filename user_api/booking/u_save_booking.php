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
require dirname(dirname(__FILE__), 2) . '/user_api/notifications/Send_mail.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
    $prop_id = isset($_POST['prop_id']) ? $_POST['prop_id'] : null;
    $merchant_ref_number = isset($_POST['merchant_ref_number']) ? $_POST['merchant_ref_number'] : null;
    $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : 0;
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;
    $confirm_guest_rules = isset($_POST['confirm_guest_rules']) ? $_POST['confirm_guest_rules'] : 'false';
    $guest_counts = isset($_POST['guest_counts']) ? $_POST['guest_counts'] : 0;
    $method_key = isset($_POST['method_key']) ? $_POST['method_key'] : '';
    $coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
    $methods  = AppConstants::getAllMethodKeys();
    $lang_ = load_specific_langauage($lang);

    [$valid, $message] = validateDates($from_date, $to_date);
    if ($prop_id  == null) {
        $returnArr    = generateResponse('false', $lang_["property_id_required"], 400);
    } else if ($uid == null) {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', ' status = 1 and is_approved =1 and is_deleted =0') === false) {
        $returnArr    = generateResponse('false', $lang_["property_not_available"], 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', '  add_user_id =' . $uid . '') === true) {
        $returnArr    = generateResponse('false', $lang_["self_booking_not_allowed"], 400);
    } else if ($valid == false) {
        $returnArr    = generateResponse('false', $message, 400);
    } else if ($confirm_guest_rules  == 'false') {
        $returnArr    = generateResponse('false', $lang_["guest_rules_unconfirmed"], 400);
    } else if (!in_array($method_key, $methods)) {
        $returnArr    = generateResponse('false', $lang_["invalid_payment_method"], 400);
    } else if ($item_id == 0) {
        $returnArr = generateResponse('false',  $lang_["item_id_required"], 400);
    } else {
        [$days, $months, $days_message] = processDates($from_date, $to_date, $lang_);
        [$date_list, $check_in_list]  = get_dates($prop_id, $uid, $rstate);
        [$status, $status_message] = validateDateRange($from_date, $to_date, $date_list, $lang_);
        [$status1, $status_message1] = validateDateRangeAganistCheckIn($from_date, $to_date, $check_in_list, $lang_);
        $checkQuery = "SELECT *  FROM tbl_property WHERE id=  " . $prop_id .  "";
        $res_data = $rstate->query($checkQuery)->fetch_assoc();
        $balance = '0.00';
        $sel = $rstate->query("select message,status,amt,tdate from wallet_report where uid=" . $uid . " order by id desc");
        $non_completed_data = $rstate->query("select id from tbl_non_completed where id=" . $item_id . " and status = 1 ")->num_rows;
        $non_completed_data_check = $rstate->query("select book_id from tbl_non_completed where id=" . $item_id . " and completed = 1 ")->num_rows;
        while ($row = $sel->fetch_assoc()) {

            if ($row['status'] == 'Adding') {
                $balance = bcadd($balance, $row['amt'], 2);
            } else if ($row['status'] == 'Withdraw') {
                $balance = bcsub($balance, $row['amt'], 2);
            }
        }
        if ($non_completed_data == 0) {
            $returnArr    = generateResponse('false',  $lang_["general_validation_error"], 400);
        } elseif ($days == 0) {
            $returnArr    = generateResponse('false', $days_message, 400);
        } else if (($status  == false && !$non_completed_data_check)) {
            $returnArr    = generateResponse('false', $status_message, 400);
        } else if (($status1  == false && !$non_completed_data_check)) {
            $returnArr    = generateResponse('false', $status_message1, 400);
        } else if ((int)$res_data['plimit'] !== 0 &&  $guest_counts > $res_data['plimit']) {
            $returnArr    = generateResponse('false',  $lang_["guest_limit_exceeded"], 400);
        } else if (
            (int)$res_data['min_days'] !== 0 && (int)$res_data['max_days'] !== 0  &&
            ($days < (int)$res_data['min_days'] || $days > (int)$res_data['max_days'])
        ) {
            $returnArr    = generateResponse('false', sprintf($lang_["invalid_date_range"], $res_data['min_days'], $res_data['max_days']), 400);
        } else if ($months >= 6) {
            $GLOBALS['rstate']->commit();
            $returnArr = generateResponse('false', $lang_["invalid_months_count"], 400);
        } else {

            $table = "tbl_book";

            $fp = array();
            $vr = array();
            $set = $rstate->query("select owner_fees, property_manager_fees,tax ,gateway_percent_fees,gateway_money_fees from tbl_setting ")->fetch_assoc();
            $prop = $rstate->query("select add_user_id  from tbl_property where  id= $prop_id  ")->fetch_assoc();
            $fp['id'] = $res_data['id'];
            $add_user_id = $res_data['add_user_id'];
            $user = $rstate->query("select is_owner , mobile	, ccode from tbl_user where  id= $uid  ")->fetch_assoc();

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

            $sub_total = get_property_price($res_data['period'], $res_data['price'], $prop_id, $from_date, $to_date);
            $coupon_value = 0;
            $Coupon_data = validateCoupon($coupon_code, $sub_total);
            if ($Coupon_data['status'] === true) {
                $coupon_value = $Coupon_data['value'];
            }
            $deposit_fees = $res_data["security_deposit"];
            $trent_fess = ($user['is_owner'] == 0) ? ($set["property_manager_fees"] * $sub_total) / 100  : ($set["owner_fees"] * $sub_total) / 100;
            $taxes = ($trent_fess * $set['tax']) / 100;
            $service_fees = (($sub_total) * $set['gateway_percent_fees']) / 100 + $set['gateway_money_fees'];
            $final_total = $sub_total + $taxes + $service_fees + $deposit_fees - $coupon_value;

            $fp['sub_total'] = number_format($sub_total, 2, '.', '');
            $fp['tax_percent'] = $set['tax'];
            $fp['taxes'] = number_format($taxes, 2, '.', '');
            $fp['service_fees'] = number_format($service_fees, 2, '.', '');
            $fp['final_total'] = number_format($final_total, 2, '.', '');
            $fp['deposit_fees'] = number_format($deposit_fees, 2, '.', '');
            $fp['trent_fees'] = number_format(0, 2, '.', '');
            $propertyAddress = json_decode($res_data['address'] ?? '', true)["ar"] ?? '';
            $propertytitle = json_decode($res_data['title'] ?? '', true)["ar"] ?? '';
            $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
            $created_at = $date->format('Y-m-d H:i:s');
            $up_at = $date->format('Y-m-d');
            $partial_value = ($fp['final_total'] * 10) / 100;
            $reminder_value = $fp['final_total'] -  $partial_value;

            $fp['partial_value'] = number_format($partial_value, 2, '.', '');
            $fp['reminder_value'] = number_format($reminder_value, 2, '.', '');

            $total_10_percent_int = (int) $fp['partial_value'];

            // $fp['total_int'] = $total_as_int;
            $fp['book_status'] = 'Booked';
            $user1 = $rstate->query("select is_owner , mobile	, ccode from tbl_user where  id= $add_user_id  ")->fetch_assoc();
            $message = "عزيزي المالك،
            حابين نبلغك إن في حجز جديد على عقارك [$propertytitle] 🎉🎉🎉

            علشان تكمل الخطوات:
            * راجع بيانات المستأجر من خلال التطبيق
            * وافق أو ارفض الحجز خلال 24 ساعة

            شكراً إنك جزء من عائلة Trent 🏡
            فريق Trent دايمًا في خدمتك ✅";
            $title_ = 'لديك حجز جديد! 🔔';
            $mobile = $user1["mobile"];
            $ccode = $user1["ccode"];
            $generated_item_id = 'item_' . uniqid('', true); // Adds extra entropy
            $owner = $rstate->query("select name from tbl_user where  id=" . $uid . "")->fetch_assoc();
            $client_name = $owner['name'];
            if ($non_completed_data_check) {
                $non_completed_data_check_data = $rstate->query("select book_id from tbl_non_completed where id=" . $item_id . " and completed = 1 ")->fetch_assoc();
                $fp['book_id'] = $non_completed_data_check_data['book_id'];

                $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                    "booking_details" => $fp,
                ));
            } else if ($method_key == 'TRENT_BALANCE' && $balance <  $fp['final_total']) {
                $returnArr    = generateResponse('false', $lang_["insufficient_wallet_balance"], 400);
            } else if ($method_key == 'TRENT_BALANCE' && $balance >= $fp['final_total']) {

                $GLOBALS['rstate']->begin_transaction();

                $field_values = ["item_id", "prop_id", 'method_key', 'reminder_value', 'pay_status',  'total_day', "check_in", "check_out",   "uid", "book_date", "book_status", "prop_price", "prop_img", "prop_title", "add_user_id", "noguest",  "subtotal", "tax", "trent_fees", "service_fees", "deposit_fees", "total"];
                $data_values = ['', $res_data['id'], $method_key, 0, 'Completed', $days, $from_date, $to_date,   $uid, $created_at, "Booked", $res_data['price'], $res_data['image'], $res_data['title'], $res_data['add_user_id'], "$guest_counts", $fp['sub_total'],  $fp['taxes'], $trent_fess, $fp['service_fees'],  $fp['deposit_fees'],  $fp['final_total']];

                $h = new Estate();

                $book_id = $h->restateinsertdata_Api($field_values, $data_values, $table);
                if (!$book_id) {
                    throw new Exception("Insert failed");
                }
                $fp['book_id'] = $book_id;

                $created_at1 = $date->format('Y-m-d H:i:s');

                $field_values1 = ["uid", 'status', 'amt', 'tdate'];
                $data_values1  = [$uid, 'Withdraw', $fp['final_total'], $created_at1];
                $table1 = 'wallet_report';


                $check = $h->restateinsertdata_Api($field_values1, $data_values1, $table1);
                if (!$check) {
                    throw new Exception("Insert failed");
                }
                $where_conditions = [$item_id];
                $field = array('completed' => '1', 'active' => '0',  'book_id' => $book_id);
                $where = "where  id=" . '?' . "";

                $check = $h->restateupdateData_Api($field, 'tbl_non_completed', $where, $where_conditions);
                if (!$check) {
                    throw new Exception("Insert failed");
                }
                $GLOBALS['rstate']->commit();
                $whatsapp = sendMessage([$ccode . $mobile], $message);
                $firebase_notification = sendFirebaseNotification($title_, $message, $add_user_id, 'booking_id', $book_id, $res_data['image']);
                send_email($book_id, $client_name, $up_at, $days);

                $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                    "booking_details" => $fp,
                ));
            } else {

                if (getPaymentStatus($merchant_ref_number, $item_id,  $total_10_percent_int)) {


                    $GLOBALS['rstate']->begin_transaction();

                    $field_values = ["item_id", "prop_id", 'method_key', 'reminder_value', 'pay_status', 'total_day', "check_in", "check_out",   "uid", "book_date", "book_status", "prop_price", "prop_img", "prop_title", "add_user_id", "noguest",  "subtotal", "tax", "trent_fees", "service_fees", "deposit_fees", "total"];
                    $data_values = [$generated_item_id, $res_data['id'], $method_key, $reminder_value, 'Partial', $days, $from_date, $to_date,   $uid, $created_at, "Booked", $res_data['price'], $res_data['image'], $res_data['title'], $res_data['add_user_id'], "$guest_counts", $fp['sub_total'],  $fp['taxes'], $trent_fess, $fp['service_fees'],  $fp['deposit_fees'],  $fp['final_total']];

                    $h = new Estate();
                    $book_id = $h->restateinsertdata_Api($field_values, $data_values, $table);
                    if (!$book_id) {
                        throw new Exception("Insert failed");
                    }
                    $fp['book_id'] = $book_id;
                    $where_conditions = [$item_id];
                    $field = array('completed' => '1', 'active' => '0',  'book_id' => $book_id);
                    $where = "where  id=" . '?' . "";

                    $check = $h->restateupdateData_Api($field, 'tbl_non_completed', $where, $where_conditions);
                    if (!$check) {
                        throw new Exception("Insert failed");
                    }
                    $GLOBALS['rstate']->commit();
                    $whatsapp = sendMessage([$ccode . $mobile], $message);
                    $firebase_notification = sendFirebaseNotification($title_, $message, $add_user_id, 'booking_id', $book_id, $res_data['image']);
                    send_email($book_id, $client_name, $up_at, $days);
                    $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                        "booking_details" => $fp,
                    ));
                } else {
                    $returnArr    = generateResponse('false', $lang_["payment_validation_failed"], 400);
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

function send_email($BookingId, $ClientName, $BookingDate, $Days)
{
    // Subject with emoji and Arabic text
    $subject = '🔔 حجز جديد برقم #' . $BookingId;

    // Body with placeholders using HEREDOC syntax
    $body = <<<EMAIL
السلام عليكم

يوجد حجز جديد بالتفاصيل التالية:

🆔 رقم الحجز: $BookingId
👤 اسم العميل: $ClientName
📅 تاريخ الحجز: $BookingDate
📅 عدد الليالي: $Days

لمراجعة تفاصيل الحجز، يرجى زيارة الرابط التالي:
👉 https://trent.com.eg/trent/pending.php

مع فائق التحيات
TRENT
EMAIL;
    sendPlainTextEmail($subject, $body);
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
function processDates(string $from_date, string $to_date, $lang_): array
{
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($to_date);

    // Validate order
    if ($date1 >= $date2) {
        return [0,   $lang_["DATE_VALIDATION_ERROR"]];
    }

    $interval = $date1->diff($date2);
    $days = $interval->days;
    $months = $interval->y * 12 + $interval->m;

    return [
        $days,
        $months,
        "Successfully calculated $days days between dates"
    ];
}


function validateDateRange($from_date, $to_date, $date_list, $lang_)
{


    // Check for conflicts with date_list
    $current = strtotime($from_date);
    $end = strtotime($to_date);

    while ($current <= $end) {
        $current_date = date('Y-m-d', $current);
        if (array_key_exists($current_date, $date_list)) {
            $details = $date_list[$current_date];

            return [
                false,
                sprintf($lang_["date_range_conflict"], $details['check_in'],  $details['check_out'])
            ];
        }
        $current = strtotime('+1 day', $current);
    }

    return [true, "Valid date range"];
}

function validateDateRangeAganistCheckIn($from_date, $to_date, $check_in_list, $lang_)
{


    // Check for conflicts with date_list
    $current = strtotime($from_date);
    $end = strtotime($to_date);

    $current_date = date('Y-m-d', $current);
    if (array_key_exists($current_date, $check_in_list)) {
        $details = $check_in_list[$current_date];

        return [
            false,
            sprintf($lang_["date_range_conflict"], $details['check_in'],  $details['check_out'])
        ];
    }


    return [true, "Valid date range"];
}
