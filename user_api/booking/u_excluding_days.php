<?php
require dirname(dirname(__FILE__), 2) . '/include/reconfig.php';
require dirname(dirname(__FILE__), 2) . '/include/validation.php';
require dirname(dirname(__FILE__), 2) . '/include/helper.php';
require_once dirname(dirname(__FILE__), 2) . '/user_api/error_handler.php';
require dirname(dirname(__FILE__), 2) . '/user_api/estate.php';

header('Content-Type: application/json');
try {

    // Handle preflight request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $prop_id  =  isset($_POST['prop_id']) ? $_POST['prop_id'] : null;
    $uid  =  isset($_POST['uid']) ? $_POST['uid'] : '';
    $lang = isset($_POST['lang']) ? $rstate->real_escape_string($_POST['lang']) : 'en';
    $lang_ = load_specific_langauage($lang);
    $from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
    $to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;

    $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
    $timestamp = $date->format('Y-m-d');
    [$valid, $message] = validateDates($from_date, $to_date);

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($valid == false) {
        $returnArr    = generateResponse('false', $message, 400);
    } else if ($prop_id  == null) {
        $returnArr    = generateResponse('false', $lang_["property_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', ' status = 1 and is_approved =1 and is_deleted =0') === false) {
        $returnArr    = generateResponse('false', $lang_["property_not_available"], 400);
    } else {
        $date_list = get_dates($prop_id, $rstate);
        [$status, $status_message] = validateDateRange($from_date, $to_date, $date_list, $lang_);


        if ($status  == false) {
            $returnArr    = generateResponse('false', $status_message, 400);
        } else {
            $field_values = ["prop_id", "check_in", "check_out",   "uid", "book_date", "book_status",   "add_user_id"];
            $data_values = [$prop_id,  $from_date, $to_date,   '0', $timestamp, "Excluded", '0'];
            $table = "tbl_book";
            $h = new Estate();
            $book_id = $h->restateinsertdata_Api($field_values, $data_values, $table);
            if (!$book_id) {
                throw new Exception("Insert failed");
            }
            $returnArr    = generateResponse('true', "Days Excluded Successfully", 200, array(
                "from_date" => $from_date,
                "to_date" => $to_date,
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
function processDates(string $from_date, string $to_date, $lang_): array
{
    $date1 = new DateTime($from_date);
    $date2 = new DateTime($to_date);

    // Validate order
    if ($date1 >= $date2) {
        return [0,   $lang_["DATE_VALIDATION_ERROR"]];
    }

    $interval = $date1->diff($date2);
    $days = $interval->days ;

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

    while ($current < $end) {
        $dates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }

    return $dates;
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
 $lang_["booked_already_excluded"]            ];
        }
        $current = strtotime('+1 day', $current);
    }

    return [true, "Valid date range"];
}
