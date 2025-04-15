<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';
require dirname(dirname(__FILE__), 2) . '/include/constants.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';

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
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;
    $confirm_guest_rules = isset($_POST['confirm_guest_rules']) ? $_POST['confirm_guest_rules'] : 'false';
    $guest_counts = isset($_POST['guest_counts']) ? $_POST['guest_counts'] : 0;
    [$valid, $message] = validateDates($from_date, $to_date);
    if ($prop_id  == null) {
        $returnArr    = generateResponse('false', "Property id is required", 400);
    } else if ($uid == null) {
        $returnArr    = generateResponse('false', "User id is required", 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user', ' status = 1 and verified =1 ') === false) {
        $returnArr    = generateResponse('false', "User id is not exists", 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', ' status = 1 and is_approved =1') === false) {
        $returnArr    = generateResponse('false', "This property  is not Available", 400);
    } else if ($valid == false) {
        $returnArr    = generateResponse('false', $message, 400);
    } else if ($confirm_guest_rules  == 'false') {
        $returnArr    = generateResponse('false', "You Must confirm Guest Rules", 400);
    } else {
        [$days, $days_message] = processDates($from_date, $to_date);
        [$status, $status_message] = validateDateRangeAfterNextDate($from_date, next_available_date($prop_id, $rstate));
        $checkQuery = "SELECT *  FROM tbl_property WHERE id=  " . $prop_id .  "";
        $res_data = $rstate->query($checkQuery)->fetch_assoc();
        if ($days == 0) {
            $returnArr    = generateResponse('false', $days_message, 400);
        } else if ($status  == false) {
            $returnArr    = generateResponse('false', $status_message, 400);
        } else if ($guest_counts > $res_data['plimit']) {
            $returnArr    = generateResponse('false', "Guest count are exceed persons limits", 400);
        } 
        else if (
            (int)$res_data['min_days'] !== 0 && (int)$res_data['max_days'] !== 0  &&
            ($days < (int)$res_data['min_days'] || $days > (int)$res_data['max_days'])
        ) {
            $returnArr    = generateResponse('false', "The selected dates are not  within the allowed limit [" . $res_data['min_days'] . ',' . $res_data['max_days'] . "]", 400);
        } 
        
        else {
            $fp = array();
            $vr = array();
            $set = $rstate->query("select owner_fees, property_manager_fees,tax ,gateway_percent_fees,gateway_money_fees from tbl_setting ")->fetch_assoc();
            $fp['id'] = $res_data['id'];
            $user = $rstate->query("select is_owner from tbl_user where  id= $uid  ")->fetch_assoc();

            $fp['IS_FAVOURITE'] = $rstate->query("select * from tbl_fav where  uid= $uid and property_id=" . $res_data['id'] . "")->num_rows;

            $titleData = json_decode($res_data['title'], true);
            $fp['title'] = $titleData[$lang];
            $checkrate = $rstate->query("SELECT *  FROM tbl_book where prop_id=" . $res_data['id'] . " and book_status='Completed' and total_rate !=0")->num_rows;
            if ($checkrate != 0) {
                $rdata_rest = $rstate->query("SELECT sum(total_rate)/count(*) as rate_rest FROM tbl_book where prop_id=" . $res_data['id'] . " and book_status='Completed' and total_rate !=0")->fetch_assoc();
                $fp['rate'] = number_format((float)$rdata_rest['rate_rest'], 1, '.', '');
            } else {
                $fp['rate'] = number_format(0, 1, '.', '');
            }
            $fp['price'] = $res_data['price'];
            $fp['from_date'] = $from_date;
            $fp['from_date'] = $from_date;
            $fp['days'] = $days;
            $fp['guest_count'] = $guest_counts;
            $fp['tax'] = $set['tax'];

            $periods = [
                "d" => ["ar" => "يومي", "en" => "daily"],
                "m" => ["ar" => "شهري", "en" => "monthly"]
            ];

            $fp['period_type'] =  $periods[$res_data['period']][$lang];

            $imageArray = explode(',', $res_data['image']);

            // Loop through each image URL and push to $vr array
            foreach ($imageArray as $image) {
                $vr[] = array('img' => trim($image));
            }
            $fp['image_list'] = $vr;
            $price = ($res_data['period'] == 'd') ? $res_data['price'] : ($res_data['price'] / 30);
            $fp['sub_total'] = $days * $price;
            $trent_fess = ($user['is_owner'] == 0) ? ($set["property_manager_fees"] * $fp['sub_total'] ) /100  : ($set["owner_fees"] * $fp['sub_total'] )/100; 
            $deposit_fees = $res_data["security_deposit"];

            $fp['taxes'] = ($trent_fess * $set['tax']) / 100;
            $fp['service_fees'] = (($days * $price) * $set['gateway_percent_fees']) / 100 + $set['gateway_money_fees'];
            $fp['final_total'] = $fp['sub_total'] + $fp['taxes'] + $fp['service_fees']+ $deposit_fees +$trent_fess;
            $fp['deposit_fees'] = $res_data['security_deposit'];
            $fp['trent_fees'] = $trent_fess;

            $returnArr    = generateResponse('true', "Propperty booking Details", 200, array(
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
    $days = $interval->days+1;
    return [
        $days,
        "Successfully calculated $days days between dates"
    ];
}

function next_available_date(string $pro_id, $rstate): string
{

    $check_date_query = $rstate->query("SELECT check_in, check_out FROM tbl_book WHERE prop_id = $pro_id AND book_status != 'Cancelled' ORDER BY check_out ASC");

    $booked_dates = [];
    while ($row = $check_date_query->fetch_assoc()) {
        $booked_dates[] = [
            'check_in'  => strtotime($row['check_in']),
            'check_out' => strtotime($row['check_out'])
        ];
    }

    // Find the next available date
    $next_available_date = date('Y-m-d'); // Default to today if no bookings exist

    if (!empty($booked_dates)) {
        $latest_check_out = 0;

        foreach ($booked_dates as $booking) {
            if ($booking['check_out'] > $latest_check_out) {
                $latest_check_out = $booking['check_out'];
            }
        }

        // The next available date is the day after the latest check_out date
        $next_available_date = date('Y-m-d', strtotime('+1 day', $latest_check_out));
    }

    // Assign the next available date to the response
    return $next_available_date;
}

function validateDateRangeAfterNextDate(string $from_date, string $next_date): array
{
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($next_date);

    // Check if range starts at or after next date
    if ($date1 < $date2) {
        return [
            false,
            "Range must start at or after next available date. " .
                "Next date: {$date2->format('Y-m-d')}, " .
                "Range starts: {$date1->format('Y-m-d')}"
        ];
    }

    return [true, "Date range is valid relative to next available date"];
}
