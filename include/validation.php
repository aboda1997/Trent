<?php
require 'reconfig.php';
require dirname(dirname(__FILE__)) . '/vendor/autoload.php';


use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;


function validateIdAndDatabaseExistance($id, $table,  $additionalCondition = '')
{
    // Check if it's a number and a positive integer
    if (filter_var($id, FILTER_VALIDATE_INT) !== false && $id > 0) {
        $condition = "id= " . $id . " ";

        // Append additional condition if provided
        if (!empty($additionalCondition)) {
            $condition .= " AND ($additionalCondition)";
        }
        // Build and execute the query
        $query = "SELECT id FROM " . $table . " WHERE " . $condition;

        //var_dump($query);
        $result = $GLOBALS['rstate']->query($query)->num_rows;
        return $result === 1;
    }
    return false;
}

function checkTableStatus($id, $table)
{
    // Check if it's a number and a positive integer
    if (filter_var($id, FILTER_VALIDATE_INT) !== false && $id > 0) {

        // Build and execute the query
        $query = "SELECT status FROM " . $table . " WHERE id= " . $id . " ";
        $result = $GLOBALS['rstate']->query($query)->fetch_assoc();
        return $result['status'] == 1;
    }
    return false;
}

function checkPropertyBookingStatus($id)
{
    // Check if it's a number and a positive integer
    if (filter_var($id, FILTER_VALIDATE_INT) !== false && $id > 0) {

        // Build and execute the query
        $query = "SELECT b.book_status 
        FROM tbl_book b
        INNER JOIN tbl_user u ON b.uid = u.id
        WHERE b.prop_id = " . $id . "
        AND u.status = 1 
        AND u.verified = 1";
        $result = $GLOBALS['rstate']->query($query);
        $statuses = array();
        while ($row = $result->fetch_assoc()) {
            $statuses[] = $row['book_status']; // Add each status to the array
        }
        $restrictedStatuses = ['Booked', 'Check_in', 'Confirmed'];
        $hasRestrictedStatus = true;
        foreach ($statuses as $status) {
            if (in_array($status, $restrictedStatuses)) {
                $hasRestrictedStatus = false;
                break; // Exit early if found
            }
        }
    }
    return $hasRestrictedStatus;
}

function validateFacilityIds($idString, $table = "tbl_facility", $uid = null)
{
    // Check if input is a JSON array and decode it
    $decodedIds = json_decode($idString, true);

    // If JSON decoding fails, assume it's a comma-separated string
    if (!is_array($decodedIds)) {
        $decodedIds = explode(',', $idString);
    }
    // Convert comma-separated string to an array and sanitize
    $ids = array_filter(array_map('trim', $decodedIds));

    // Ensure all values are positive integers
    foreach ($ids as $id) {
        if (!ctype_digit($id) || $id <= 0) {
            return false; // Invalid ID detected
        }
    }
    // Build and execute the query
    $idList = implode(',', $ids);

    $query = "SELECT id FROM $table WHERE id IN ($idList)";
    if ($uid) {
        $query .= " and add_user_id = $uid and book_status  IN ('Check_in', 'Completed' ,'Confirmed') ";
    }
    $result = $GLOBALS['rstate']->query($query);
    // Fetch valid IDs
    $validIds = [];

    while ($row = $result->fetch_assoc()) {
        $validIds[] = $row['id'];
    }
    // Check if all provided IDs exist in the table
    return count($validIds) === count($ids);
}


function validatePayouts($idString)
{
    // Check if input is a JSON array and decode it
    $decodedIds = json_decode($idString, true);

    // If JSON decoding fails, assume it's a comma-separated string
    if (!is_array($decodedIds)) {
        $decodedIds = explode(',', $idString);
    }
    // Convert comma-separated string to an array and sanitize
    $ids = array_filter(array_map('trim', $decodedIds));

    // Ensure all values are positive integers
    foreach ($ids as $id) {
        if (!ctype_digit($id) || $id <= 0) {
            return false; // Invalid ID detected
        }
    }
    // Build and execute the query
    $idList = implode(',', $ids);

    $query = "SELECT b.id 
          FROM tbl_book b
          WHERE b.id IN ($idList)
          AND  EXISTS (
              SELECT 1 
              FROM tbl_payout_list pl
              WHERE pl.book_id = b.id
              AND pl.payout_status IN ('Pending', 'Completed')
          )";
    $result = $GLOBALS['rstate']->query($query);
    return $result->num_rows;
}
function expandShortUrl($shortUrl)
{
    // Validate the URL format
    if (!filter_var($shortUrl, FILTER_VALIDATE_URL)) {
        return ['status' => false, 'response' => 'Invalid MAP URL'];
    }

    // Try to fetch headers with error handling
    try {
        $headers = @get_headers($shortUrl, 1);
        // Check if headers were retrieved successfully
        if ($headers === false) {
            return ['status' => false, 'response' => 'Invalid MAP URL'];
        }
        // Check if there is a redirection (Location header)
        if (isset($headers['Location'])) {
            $finalUrl = is_array($headers['Location']) ? end($headers['Location']) : $headers['Location'];
            return ['status' => true, 'response' => $finalUrl];
        }

        // Return the original URL if no redirection occurred
        return ['status' => true, 'response' => $shortUrl];
    } catch (Exception $e) {
        return ['status' => false, 'response' => 'Error: ' . $e->getMessage()];
    }
}


function validateAndExtractCoordinates($url)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0"); // Avoid bot detection

    // Get HTML content
    $html = curl_exec($ch);
    curl_close($ch);
    $decodedContent = urldecode($html);

    // Regular expression to match various coordinate formats
    $pattern = '/([+-]?\d*\.\d+),\s*([+-]?\d*\.\d+)/';

    // Find all matches
    preg_match_all($pattern, $decodedContent, $matches, PREG_SET_ORDER);

    // Store valid coordinates and their frequencies
    $coordinatesCount = [];
    foreach ($matches as $match) {
        $lat = floatval($match[1]);
        $lon = floatval($match[2]);

        // Validate latitude and longitude ranges
        if ($lat >= -90 && $lat <= 90 && $lon >= -180 && $lon <= 180) {
            $key = "$lat,$lon"; // Create a unique key for the coordinate pair
            if (isset($coordinatesCount[$key])) {
                $coordinatesCount[$key]['count']++; // Increment count if the coordinate already exists
            } else {
                $coordinatesCount[$key] = [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'count' => 1 // Initialize count for new coordinate
                ];
            }
        }
    }
    if (preg_match($pattern, $url, $matches)) {
        $lat = floatval($match[1]);
        $lon = floatval($match[2]);
        $key = "$lat,$lon"; // Create a unique key for the coordinate pair
        $coordinatesCount[$key] = [
            'latitude' => $lat,
            'longitude' => $lon,
            'count' => 1
        ];
    }
    // Sort coordinates by frequency (most occurred first)
    usort($coordinatesCount, function ($a, $b) {
        return $b['count'] - $a['count']; // Sort in descending order of count
    });

    // Prepare the final result
    $coordinates = [];
    foreach ($coordinatesCount as $coord) {
        $coordinates[] = [
            'status' => true,
            'latitude' => $coord['latitude'],
            'longitude' => $coord['longitude'],
        ];
    }

    return !empty($coordinates) ? $coordinates[0] :  ['status' => false, 'response' => 'MAP URL does not contain valid coordinates'];
}


function validateName($name, $placeholder, $max = 50, $lang = 'en', $required = true)
{
    // Trim whitespace from the name
    $name = trim($name);

    // Define language responses
    $messages = [
        'en' => [
            'required' => "$placeholder is required",
            'length' => "$placeholder must be between 3 and $max characters",
            'invalid' => "Invalid $placeholder format",
            'valid' => "Valid $placeholder"
        ],
        'ar' => [
            'required' => "$placeholder مطلوب",
            'length' => "$placeholder يجب أن يكون بين 3 و $max حرفًا",
            'invalid' => "صيغة $placeholder غير صالحة",
            'valid' => "$placeholder صالح"
        ]
    ];
    if ($name == '' && !$required) {
        return ['status' => true, 'response' => $messages[$lang]['valid']];
    }

    if ($name == '' && $required) {
        return ['status' => false, 'response' => $messages[$lang]['required']];
    }

    if (strlen($name) < 3 || strlen($name) > $max) {
        return ['status' => false, 'response' => $messages[$lang]['length']];
    }

    if (!preg_match('/^[\p{Arabic}a-zA-Z\s\'\-]+$/u', $name)) {
        return ['status' => false, 'response' => $messages[$lang]['invalid']];
    }

    return ['status' => true, 'response' => $messages[$lang]['valid']];
}

function validateEmail($email)
{
    $email = trim($email);

    // Check valid email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['status' => false, 'response' => 'Invalid email format.'];
    }

    if (strlen($email) > 100) {
        return ['status' => false, 'response' => 'Email is too long.'];
    }

    return ['status' => true, 'response' => 'Valid email.'];
}

function validatePassword($password)
{
    if (strlen($password) >= 6 && preg_match('/\d/', $password)) {
        return ['status' => true, 'response' => 'Valid password.'];
    }
    return ['status' => false, 'response' => 'INValid password.'];
}


function validateEgyptianPhoneNumber($phone, $ccode = null)
{

    $phoneNumberUtil = PhoneNumberUtil::getInstance();
    $phone = preg_replace('/\s+|-/', '', $phone);
    $internationalNumber = "+" . $ccode . $phone;

    try {
        $phoneNumberProto = $phoneNumberUtil->parse($internationalNumber);
        if ($phoneNumberUtil->isValidNumber($phoneNumberProto)) {
            return ['status' => true, 'response' => 'Valid Mobile Number.'];
        } else {
            return ['status' => false, 'response' => 'InValid Mobile Number.'];
        }
    } catch (\libphonenumber\NumberParseException $e) {
        return ['status' => false, 'response' => 'InValid Mobile Number.'];
    }
}


function validateCheckInDate($booking_id, $timestamp)
{
    $data = getBookingStatus($booking_id);
    $cairoTimezone = new DateTimeZone('Africa/Cairo');
    // Convert to timestamps if they're strings
    $check_in_str =  $data['check_in'];
    if (strlen($check_in_str) <= 10) {
        $check_in_str .= ' 12:00:00'; // Add default time
    }
    $check_out_str =  $data['check_out'];

    $check_in = new DateTime($check_in_str, $cairoTimezone);
    $check_out = new DateTime($check_out_str, $cairoTimezone);
    $timestamp = new DateTime($timestamp, $cairoTimezone);

    return ($timestamp >= $check_in && $timestamp <= $check_out);
}

function getBookingStatus($booking_id)
{
    $query = "SELECT b.id ,b.book_status ,b.check_in ,b.check_out
    FROM tbl_book b
    WHERE b.id = $booking_id";
    $result = $GLOBALS['rstate']->query($query)->fetch_assoc();
    return $result;
}


function validateCoupon($cid, $orderTotal)
{
    $query = "SELECT min_amt,cdate,max_amt ,status,c_value
    FROM tbl_coupon 
    WHERE c_title = '" . $cid . "'";
    $result = $GLOBALS['rstate']->query($query)->fetch_assoc();
    if (!$result) {
        return ['status' => false, 'value' => 0];
    }

    $minAmt = (float)$result['min_amt'];
    $maxAmt = (float)$result['max_amt'];
    $expiryDate = $result['cdate'];
    $status = $result['status'];
    $value = $result['c_value'];

    // 1. Check if coupon is expired
    if ($status !== '1') {
        return ['status' => false, 'value' => 0];
    }

    // 1. Check if coupon is expired
    $currentDate = date('Y-m-d');
    if ($expiryDate < $currentDate) {

        return ['status' => false, 'value' => 0];
    }

    // 2. Check if order meets minimum amount requirement

    if ($orderTotal < $minAmt) {

        return ['status' => false, 'value' => 0];
    }

    // 3. Check if order exceeds maximum allowed amount (if max_amt is set)
    if ($maxAmt > 0 && $orderTotal > $maxAmt) {
        return ['status' => false, 'value' => 0];
    }

    // All checks passed → coupon is valid
    return ['status' => true, 'value' => $value];
}


function add_specific_ranges_increased_value($lang, $uid, $prop_id, $date_ranges)
{
    $lang_ = load_specific_langauage($lang);
    $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
    $timestamp = $date->format('Y-m-d');
    $h = new Estate();
    $table = "tbl_increased_value";

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($prop_id  == null) {
        $returnArr    = generateResponse('false', $lang_["property_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', 'is_deleted =0') === false) {
        $returnArr    = generateResponse('false', $lang_["property_not_available"], 400);
    } else if ($date_ranges === null || !is_array($date_ranges)) {
        $returnArr    = generateResponse('false', $lang_["date_ranges_required"], 400);
    } else if (empty($date_ranges)) {
        $res  = $h->restateDeleteData_Api_fav(" where prop_id = $prop_id", $table);

        $returnArr    = generateResponse('true', '', 200);
    } else {
        $res  = $h->restateDeleteData_Api_fav(" where prop_id = $prop_id", $table);

        // Validate each date range in the array
        [$valid, $message] = validateDateRanges($date_ranges);
        if (!$valid) {
            $returnArr = generateResponse('false', $message, 400);
        } else {
            // Insert each date range separately
            $success_count = 0;

            foreach ($date_ranges as $range) {
                $from_date = $range[0];
                $to_date = $range[1];
                $value = $range[2];

                $field_values = ["prop_id", "from_date", "to_date", 'increase_value'];
                $data_values = [$prop_id, $from_date, $to_date, $value];

                $res_id = $h->restateinsertdata_Api($field_values, $data_values, $table);
                if ($res_id) {
                    $success_count++;
                }
            }

            if ($success_count == 0) {
                throw new Exception("Insert failed for all date ranges");
            }

            $returnArr = generateResponse('true', "increased value Ranges Added Successfully", 200, array(
                "increased_value_ranges_added" => $date_ranges,
                "count" => $success_count
            ));
        }
    }

    return   $returnArr;
}


function exclude_ranges($lang, $uid, $prop_id, $date_ranges)
{
    $lang_ = load_specific_langauage($lang);
    $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
    $timestamp = $date->format('Y-m-d');
    $h = new Estate();
    $table = "tbl_book";

    if ($uid == '') {
        $returnArr = generateResponse('false', $lang_["user_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["invalid_user_id"], 400);
    } else if (checkTableStatus($uid, 'tbl_user') === false) {
        $returnArr = generateResponse('false', $lang_["account_deleted"], 400);
    } else if (!in_array($lang, ['en', 'ar'])) {
        $returnArr    = generateResponse('false', $lang_["unsupported_lang_key"], 400);
    } else if ($prop_id  == null) {
        $returnArr    = generateResponse('false', $lang_["property_id_required"], 400);
    } else if (validateIdAndDatabaseExistance($prop_id, 'tbl_property', 'is_deleted =0') === false) {
        $returnArr    = generateResponse('false', $lang_["property_not_available"], 400);
    } else if ($date_ranges === null || !is_array($date_ranges)) {
        $returnArr    = generateResponse('false', $lang_["date_ranges_required"], 400);
    } else if (empty($date_ranges)) {
        $res  = $h->restateDeleteData_Api_fav(" where prop_id = $prop_id and book_status = 'Excluded'", $table);

        $returnArr    = generateResponse('true', '', 200);
    } else {
        $res  = $h->restateDeleteData_Api_fav(" where prop_id = $prop_id and book_status = 'Excluded'", $table);

        [$valid, $message] = validateDateRanges($date_ranges);
        if (!$valid) {
            $returnArr = generateResponse('false', $message, 400);
        } else {
            [$date_list, $check_in_list] = get_dates($prop_id, $uid, $GLOBALS['rstate']);
            [$status, $status_message] = validateDateRangesAgainstBookings($date_ranges, $date_list, $lang_);
            [$status1, $status_message1] = validateDateRangesAgainstBookingsCheckin($date_ranges, $check_in_list, $lang_);

            if ($status  == false) {
                $returnArr    = generateResponse('false', $status_message, 400);
            } else if ($status1  == false) {
                $returnArr    = generateResponse('false', $status_message1, 400);
            } else {
                // Insert each date range separately
                $success_count = 0;


                foreach ($date_ranges as $range) {
                    $from_date = $range[0];
                    $to_date = $range[1];

                    $field_values = ["prop_id", "check_in", "check_out", "uid", "book_date", "book_status", "add_user_id"];
                    $data_values = [$prop_id, $from_date, $to_date, '0', $timestamp, "Excluded", '0'];
                    $table = "tbl_book";

                    $book_id = $h->restateinsertdata_Api($field_values, $data_values, $table);
                    if ($book_id) {
                        $success_count++;
                    }
                }

                if ($success_count == 0) {
                    throw new Exception("Insert failed for all date ranges");
                }

                $returnArr = generateResponse('true', "Date Ranges Excluded Successfully", 200, array(
                    "date_ranges_excluded" => $date_ranges,
                    "count" => $success_count
                ));
            }
        }
    }
    return   $returnArr;
}



/**
 * Validate an array of date ranges (each range is [from_date, to_date])
 * Returns [bool $success, string $message]
 */
function validateDateRanges(array $date_ranges): array
{
    if (empty($date_ranges)) {
        return [false, "At least one date range is required"];
    }

    foreach ($date_ranges as $range) {
        // Check if range has exactly 2 elements
        if (!is_array($range) || count($range) < 2) {
            return [false, "Each date range must be an array with exactly 2 dates [from_date, to_date]"];
        }

        // Extract dates + optional value
        $from_date = $range[0];
        $to_date   = $range[1];
        $value     = $range[2] ?? null; // Optional
        // Check presence
        if (empty($from_date) || empty($to_date)) {
            return [false, "Both from_date and to_date are required in each range"];
        }

        // Check format
        if (!strtotime($from_date) || !strtotime($to_date)) {
            return [false, "Invalid date format in range. Use YYYY-MM-DD for all dates"];
        }

        // Validate date order
        if (strtotime($from_date) > strtotime($to_date)) {
            return [false, "from_date must be before  to to_date in each range"];
        }
        // Validate value if provided (must be integer)
        if ($value !== null && !is_int($value)) {
            return [false, " the value must be an integer"];
        }
    }

    return [true, "All date ranges are valid"];
}

/**
 * Validate date ranges against existing bookings
 */
function validateDateRangesAgainstBookings(array $date_ranges, array $booked_dates, $lang_): array
{
    foreach ($date_ranges as $range) {
        [$from_date, $to_date] = $range;

        $current = strtotime($from_date);
        $end = strtotime($to_date);

        while ($current <= $end) {
            $current_date = date('Y-m-d', $current);
            if (array_key_exists($current_date, $booked_dates)) {
                return [
                    false,
                    sprintf($lang_["booked_already_excluded"], $current_date)
                ];
            }
            $current = strtotime('+1 day', $current);
        }
    }

    return [true, "All date ranges are available"];
}
function validateDateRangesAgainstBookingsCheckin(array $date_ranges, array $booked_dates, $lang_): array
{
    foreach ($date_ranges as $range) {
        [$from_date, $to_date] = $range;

        $current = strtotime($from_date);
        $end = strtotime($to_date);

        $current_date = date('Y-m-d', $current);
        if (array_key_exists($current_date, $booked_dates)) {
            return [
                false,
                sprintf($lang_["booked_already_excluded"], $current_date)
            ];
        }
    }

    return [true, "All date ranges are available"];
}

function get_holding_property_dates(string $pro_id, $uid, $rstate)
{
    date_default_timezone_set('Africa/Cairo');

    // Calculate the timestamp 3 hours ago in Cairo time
    $thirty_minutes_ago = date('Y-m-d H:i:s', strtotime('-30 minutes'));

    // Build the SQL query
    $sql = "SELECT f1 as check_in, f2 as check_out
    FROM tbl_non_completed 
    WHERE prop_id = " . (int)$pro_id . " 
    AND uid != " . (int)$uid . "  -- Exclude records from the given user ID
    AND (
        (status = 1 AND created_at > '" . $GLOBALS['rstate']->real_escape_string($thirty_minutes_ago) . "')
        OR 
        (active = 1)
    )";
    $result = $rstate->query($sql);
    $check_in_list = [];

    $date_list = [];
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $check_in = $row['check_in'];
        $check_out = $row['check_out'];
        $check_in_list[date('Y-m-d',  strtotime($check_in))] = [
            'check_in' => $check_in,
            'check_out' => $check_out,
            'type' => 'holding' // Added type to distinguish between booking and holding
        ];

        // Get all dates in the range (including check_in and check_out)
        $dates_in_range = getDatesFromRange($check_in, $check_out);

        // Store check_in and check_out for each date in the range
        foreach ($dates_in_range as $date) {
            $date_list[$date] = [
                'check_in' => $check_in,
                'check_out' => $check_out,
                'type' => 'holding' // Added type to distinguish between booking and holding
            ];
        }
    }
    return [$date_list, $check_in_list];
}

function get_dates(string $pro_id, $uid, $rstate)
{
    $sql = "SELECT check_in, check_out FROM tbl_book where prop_id=" . $pro_id . " and book_status != 'Cancelled'";
    $result = $rstate->query($sql);
    $date_list = [];
    $check_in_list = [];
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $check_in = $row['check_in'];
        $check_out = $row['check_out'];
        $check_in_list[date('Y-m-d',  strtotime($check_in))] = [
            'check_in' => $check_in,
            'check_out' => $check_out,
            'type' => 'booked' // Added type to distinguish between booking and holding
        ];

        // Get all dates in the range (including check_in and check_out)
        $dates_in_range = getDatesFromRange($check_in, $check_out);

        // Store check_in and check_out for each date in the range
        foreach ($dates_in_range as $date) {
            $date_list[$date] = [
                'check_in' => $check_in,
                'check_out' => $check_out,
                'type' => 'booked' // Added type to distinguish between booking and holding
            ];
        }
    }
    [$date_hold, $new_check_list] = get_holding_property_dates($pro_id, $uid, $rstate);
    // Remove duplicate dates
    // Merge the two date arrays, giving priority to booked dates over holding dates
    foreach ($date_hold as $date => $info) {
        if (!isset($date_list[$date])) {
            $date_list[$date] = $info;
        }
    }
    foreach ($new_check_list as $date => $info) {
        if (!isset($check_in_list[$date])) {
            $check_in_list[$date] = $info;
        }
    }
    // Sort the dates chronologically
    ksort($date_list);
    ksort($check_in_list);


    // Return both arrays
    return [
        $date_list,   // All booked & holding dates (ranges)
        $check_in_list // Only check-in dates
    ];
}

function getDatesFromRange($start, $end)
{
    $dates = [];
    $current = strtotime($start);
    $end = strtotime($end);

    // Move current to the next day to exclude the start date
    $current = strtotime('+1 day', $current);

    while ($current < $end) {
        $dates[] = date('Y-m-d', $current);
        $current = strtotime('+1 day', $current);
    }

    return $dates;
}

function validatePeriod($booking_id)
{
    // Set the timezone to Cairo, Egypt
    $cairoTimezone = new DateTimeZone('Africa/Cairo');

    // Database query to get booking information
    $sql = "SELECT confirmed_at, check_in FROM tbl_book WHERE id = " . $booking_id;
    $booking = $GLOBALS['rstate']->query($sql)->fetch_assoc();

    if (!$booking) {
        return false; // Booking not found
    }

    $confirmed_at = new DateTime($booking['confirmed_at'], $cairoTimezone);
    $check_in_str = $booking['check_in'];
    if (strlen($check_in_str) <= 10) {
        $check_in_str .= ' 12:00:00'; // Add default time
    }
    $check_in = new DateTime($check_in_str, $cairoTimezone);
    // Current time in Cairo
    $current_time = new DateTime('now', $cairoTimezone);

    // Calculate difference between confirmed_at and check_in (days)
    $confirmation_to_checkin = $confirmed_at->diff($check_in);
    $confirmation_to_checkin_days = $confirmation_to_checkin->days;

    if ($confirmation_to_checkin_days > 14) {
        // Case 1: Booking made more than 14 days before check-in
        // Must validate at least 14 days remaining before check-in
        $current_to_checkin = $current_time->diff($check_in);
        $valid_period = ($current_to_checkin->days > 14 && !$current_to_checkin->invert);
    } else {
        // Case 2: Booking made ≤14 days before check-in
        // Must validate within 24 hours of confirmation
        $current_to_confirmation = $current_time->diff($confirmed_at);
        $current_to_confirmation_hours = $current_to_confirmation->h + ($current_to_confirmation->days * 24);
        $valid_period = ($current_to_confirmation_hours < 24 && $current_to_confirmation->invert);
    }


    return $valid_period;
}


function cancel_booking($booking_id , $cancel_by , $cancel_id)
{
    $h = new Estate();
    $where_conditions = [$booking_id];
    $field = array('book_status' => 'Cancelled','cancel_by' => $cancel_by,'cancle_reason' => $cancel_id);
    $where = "where  id=" . '?' . "";

    $check = $h->restateupdateData_Api($field, 'tbl_book', $where, $where_conditions);
    return $check > 0;
}
function refundMoney($uid, $booking_id , $cancel_by , $cancel_id)
{
    $updateSql = "Select total ,reminder_value , method_key , pay_status , check_in from  tbl_book 
                      WHERE id = $booking_id";
    $data = $GLOBALS['rstate']->query($updateSql)->fetch_assoc();
    if (!$data) {
        return false; // Booking not found

    }
    $cairoTimezone = new DateTimeZone('Africa/Cairo');

    $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
    $updated_at = $date->format('Y-m-d H:i:s');
    $check_in_str = $data['check_in'];
   
    $where_conditions = [$booking_id];
    $field = array('book_status' => 'Cancelled', 'refunded' => 1 ,'cancel_by' => $cancel_by,'cancle_reason' => $cancel_id);
    $where = "where  id=" . '?' . "";

    $partial_value = ($data['pay_status'] == 'Completed') ? number_format($data['total'], 2, '.', '') : number_format($data['total'] - $data['reminder_value'], 2, '.', '');

    $notes = "Refund Added successfully!!";
    $status = 'Adding';
    $field_values = array("uid", "EmployeeId", "message", "status", "amt", "tdate");
    $h = new Estate();

    $data_values = array("$uid", "0", "$notes", "$status", "$partial_value", "$updated_at");
    $GLOBALS['rstate']->begin_transaction();
    $check = $h->restateupdateData_Api($field, 'tbl_book', $where, $where_conditions);
    if (!$check) {
        throw new Exception("update failed");
    }
    $wallet_id = $h->restateinsertdata_Api($field_values, $data_values, 'wallet_report');
    if (!$wallet_id) {
        throw new Exception("Insert failed");
    }
    $GLOBALS['rstate']->commit();

    return $wallet_id > 0;
}

function validateCancelBooking($booking_id)
{
    $cairoTimezone = new DateTimeZone('Africa/Cairo');

    // Database query to get booking information
    $sql = "SELECT check_in FROM tbl_book WHERE id = " . $booking_id;
    $booking = $GLOBALS['rstate']->query($sql)->fetch_assoc();

    if (!$booking) {
        return false; // Booking not found
    }

    $check_in_str = $booking['check_in'];
    if (strlen($check_in_str) <= 10) {
        $check_in_str .= ' 12:00:00'; // Add default time
    }

    $check_in = new DateTime($check_in_str, $cairoTimezone);
    $current_time = new DateTime('now', $cairoTimezone);

    // Check if check_in time has passed or is equal to current time
    return ($check_in <= $current_time);
}

function validateBookingConflict($from_date, $to_date, $prop_id)
{
    // Connect to database (assuming you have a connection already)
    // $db = your database connection;

    // Set timezone to Cairo
    date_default_timezone_set('Africa/Cairo');

    // Convert input dates to proper format for comparison
    $from_date = date('Y-m-d H:i:s', strtotime($from_date));
    $to_date = date('Y-m-d H:i:s', strtotime($to_date));

    // Calculate the timestamp 30 minutes ago in Cairo time
    $thirty_minutes_ago = date('Y-m-d H:i:s', strtotime('-30 minutes'));

    // Build the SQL query (using mysqli for modern PHP)
    $sql = "SELECT COUNT(*) as conflict_count 
        FROM tbl_non_completed 
        WHERE prop_id = " . (int)$prop_id . " 
        AND (
            (
                -- Standard overlap conditions (modified to exclude the f2=from_date case)
                ('" . $GLOBALS['rstate']->real_escape_string($from_date) . "' < f2 AND 
                 '" . $GLOBALS['rstate']->real_escape_string($to_date) . "' > f1)
            )
            AND NOT (
                -- Explicitly allow the case where new starts exactly when old ends
                '" . $GLOBALS['rstate']->real_escape_string($from_date) . "' = f2
            )
        )
        AND created_at > '" . $GLOBALS['rstate']->real_escape_string($thirty_minutes_ago) . "'";
    try {
        // Execute the query
        $result = $GLOBALS['rstate']->query($sql);

        if (!$result) {
            throw new Exception("Query failed: " . $GLOBALS['rstate']->error);
        }

        // Fetch the result
        $row = $result->fetch_assoc();

        // If conflict_count > 0, there's a conflict
        return ($row['conflict_count'] == 0);
    } catch (Exception $e) {
        // Handle errors
        error_log("Error in validateBookingConflict: " . $e->getMessage());
        return false; // Assume conflict exists if there's an error
    }
}

function get_property_price($period, $price, $prop_id, $from_date, $to_date)
{
    $price_ranges = array();

    // Fetch increased value ranges from database
    $inc_ranges = $GLOBALS['rstate']->query("SELECT * FROM tbl_increased_value WHERE prop_id = " . $prop_id);
    while ($row = $inc_ranges->fetch_assoc()) {
        array_push($price_ranges, array(
            'from_date' => $row['from_date'],
            'to_date' => $row['to_date'],
            'value' => $row['increase_value']
        ));
    }

    // Convert input dates to DateTime objects
    $current_date = new DateTime($from_date);
    $end_date = new DateTime($to_date);
    $end_date->modify('-1 day'); // Adjust to_date to be inclusive

    $total_price = 0;

    // Process each day in the range
    while ($current_date <= $end_date) {
        if ($period == 'd') {
            $daily_price = $price; // Base price

        } elseif ($period == 'm') {
            $daily_price = $price / 30; // Base price
        }
        $current_date_str = $current_date->format('Y-m-d');
        // Check if current day falls in any increased price range
        foreach ($price_ranges as $range) {
            $range_start = new DateTime($range['from_date']);
            $range_end = new DateTime($range['to_date']);

            if ($current_date >= $range_start && $current_date <= $range_end) {
                $daily_price += $range['value']; // Apply specific increase for this day

                break; // Stop checking other ranges once we find a match
            }
        }

        // Add to total based on period type
        $total_price += $daily_price;



        $current_date->modify('+1 day');
    }

    return $total_price;
}
