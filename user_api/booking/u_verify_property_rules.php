<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require_once dirname(dirname(__FILE__), 2) . '/include/load_language.php';

header('Content-Type: application/json');
try {
    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
        $prop_id = isset($_POST['prop_id']) ? $_POST['prop_id'] : null;

    $lockDir = dirname(dirname(__FILE__), 2)  . '/property_locks/';
if (!file_exists($lockDir)) {
    mkdir($lockDir, 0777, true);
}
$lockFile = $lockDir . 'prop_lock_' . $prop_id . '.lock';

// Open the lock file
$fpf = fopen($lockFile, 'w+');
if (!$fpf) {
    throw new Exception("Could not create lock file");
}

// Try to acquire an exclusive lock (wait up to 5 seconds)
if (!flock($fpf, LOCK_EX | LOCK_NB, $wouldBlock)) {
    if ($wouldBlock) {
        // Lock is held by another process
        throw new Exception("Another operation is already processing this property. Please try again later.");
    } else {
        throw new Exception("Could not acquire lock");
    }
}

    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';

    $uid = isset($_POST['uid']) ? $_POST['uid'] : null;
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;
    $confirm_guest_rules = isset($_POST['confirm_guest_rules']) ? $_POST['confirm_guest_rules'] : 'false';
    $guest_counts = isset($_POST['guest_counts']) ? $_POST['guest_counts'] : 0;
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
    } else {
        [$days, $days_message] = processDates($from_date, $to_date, $lang_);
        $date_list = get_dates($prop_id, $uid, $rstate);
        [$status, $status_message] = validateDateRange($from_date, $to_date, $date_list, $lang_);
        $checkQuery = "SELECT *  FROM tbl_property WHERE id=  " . $prop_id .  "";
        $res_data = $rstate->query($checkQuery)->fetch_assoc();
        $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
        $created_at = $date->format('Y-m-d H:i:s');
        $balance = '0.00';
        $sel = $rstate->query("select message,status,amt,tdate from wallet_report where uid=" . $uid . " order by id desc");
        while ($row = $sel->fetch_assoc()) {

            if ($row['status'] == 'Adding') {
                $balance = bcadd($balance, $row['amt'], 2);
            } else if ($row['status'] == 'Withdraw') {
                $balance = bcsub($balance, $row['amt'], 2);
            }
        }
        if ($days == 0) {
            $returnArr    = generateResponse('false', $days_message, 400);
        } else if ($status  == false) {
            $returnArr    = generateResponse('false', $status_message, 400);
        } else if ((int)$res_data['plimit'] !== 0 &&  $guest_counts > $res_data['plimit']) {
            $returnArr    = generateResponse('false',  $lang_["guest_limit_exceeded"], 400);
        } else if (
            (int)$res_data['min_days'] !== 0 && (int)$res_data['max_days'] !== 0  &&
            ($days < (int)$res_data['min_days'] || $days > (int)$res_data['max_days'])
        ) {
            $returnArr    = generateResponse('false', sprintf($lang_["invalid_date_range"], $res_data['min_days'], $res_data['max_days']), 400);
        } ///else if (validateBookingConflict($from_date, $to_date, $prop_id) == false) {
        //$returnArr    = generateResponse('false', $lang_["Not_allow_book_range"], 400);
        //} 
        else {
            $fp = array();
            $vr = array();
            $set = $rstate->query("select owner_fees, property_manager_fees,tax ,gateway_percent_fees,gateway_money_fees from tbl_setting ")->fetch_assoc();
            $fp['id'] = $res_data['id'];
            $user = $rstate->query("select is_owner from tbl_user where  id= $uid  ")->fetch_assoc();

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

            $imageArray = array_filter(explode(',', $res_data['image']) ?? '');

            // Loop through each image URL and push to $vr array
            foreach ($imageArray as $image) {
                $vr[] = array('img' => trim($image));
            }
            $fp['image_list'] = $vr;
            $sub_total = get_property_price($res_data['period'], $res_data['price'], $prop_id, $from_date, $to_date);
            $deposit_fees = $res_data["security_deposit"];
            $trent_fess = ($user['is_owner'] == 0) ? ($set["property_manager_fees"] * $sub_total) / 100  : ($set["owner_fees"] * $sub_total) / 100;
            $taxes = ($trent_fess * $set['tax']) / 100;
            $service_fees = (($sub_total) * $set['gateway_percent_fees']) / 100 + $set['gateway_money_fees'];
            $final_total = $sub_total + $taxes + $service_fees + $deposit_fees;

            $fp['sub_total'] = number_format($sub_total, 2, '.', '');
            $fp['tax_percent'] = $set['tax'];
            $fp['taxes'] = number_format($taxes, 2, '.', '');
            $fp['service_fees'] = number_format($service_fees, 2, '.', '');
            $fp['final_total'] = number_format($final_total, 2, '.', '');
            $fp['deposit_fees'] = number_format($deposit_fees, 2, '.', '');
            $fp['trent_fees'] = number_format(0, 2, '.', '');

            $partial_value = ($fp['final_total'] * 10) / 100;
            $reminder_value = $fp['final_total'] -  $partial_value;

            $fp['partial_value'] = number_format($partial_value, 2, '.', '');
            $fp['reminder_value'] = number_format($reminder_value, 2, '.', '');


            $postString = http_build_query($_POST);
            $total_as_int = (int)$fp['final_total'];

            // $fp['total_int'] = $total_as_int;

            $field_values = ["data", "f1", "f2", "created_at", "prop_id", "total", "sub_total", 'uid'];
            $data_values = [$postString, $from_date, $to_date, $created_at, $prop_id, $fp['final_total'],  $fp['sub_total'], $uid];

            $h = new Estate();
            if($uid == 67){
            sleep(50);
            var_dump('test');
            }

            $GLOBALS['rstate']->begin_transaction();

            // Sanitize the prop_id to prevent SQL injection
            $prop_id = $GLOBALS['rstate']->real_escape_string($prop_id);

            // Lock query without prepared statement
            $lockQuery = "SELECT * FROM tbl_non_completed WHERE prop_id = $prop_id FOR UPDATE";
            $GLOBALS['rstate']->query($lockQuery);
            $check = $h->restateinsertdata_Api($field_values, $data_values, 'tbl_non_completed');
            if (!$check) {
                throw new Exception("Insert failed");
            }
            $GLOBALS['rstate']->commit();
            flock($fpf, LOCK_UN);

            $fp['item_id'] = $check;
            $returnArr    = generateResponse('true', "Property booking Details", 200, array(
                "booking_details" => $fp,
            ));
        }
    }
    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
} finally {
    // Release the lock
    fclose($fpf);
    
    // Optional: Clean up the lock file
    unlink($lockFile);
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
        return [
            0,
            $lang_["DATE_VALIDATION_ERROR"]
        ];
    }

    $interval = $date1->diff($date2);
    $days = $interval->days;

    return [
        $days,
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
        if (in_array($current_date, $date_list)) {
            return [
                false,
                sprintf($lang_["date_range_conflict"], $current_date)
            ];
        }
        $current = strtotime('+1 day', $current);
    }

    return [true, "Valid date range"];
}
