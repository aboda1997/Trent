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

function exclude_ranges($lang, $uid, $prop_id, $date_ranges)
{
    $lang_ = load_specific_langauage($lang);
    $date = new DateTime('now', new DateTimeZone('Africa/Cairo'));
    $timestamp = $date->format('Y-m-d');

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
    } else if ($date_ranges === null || !is_array($date_ranges) || empty($date_ranges)) {
        $returnArr    = generateResponse('false', $lang_["date_ranges_required"], 400);
    } else {
        // Validate each date range in the array
        [$valid, $message] = validateDateRanges($date_ranges);
        if (!$valid) {
            $returnArr = generateResponse('false', $message, 400);
        } else {
            $date_list = get_dates($prop_id, $GLOBALS['rstate']);
            [$status, $status_message] = validateDateRangesAgainstBookings($date_ranges, $date_list, $lang_);

            if ($status  == false) {
                $returnArr    = generateResponse('false', $status_message, 400);
            } else {
                // Insert each date range separately
                $success_count = 0;
                $h = new Estate();

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
        if (!is_array($range) || count($range) != 2) {
            return [false, "Each date range must be an array with exactly 2 dates [from_date, to_date]"];
        }

        [$from_date, $to_date] = $range;

        // Check presence
        if (empty($from_date) || empty($to_date)) {
            return [false, "Both from_date and to_date are required in each range"];
        }

        // Check format
        if (!strtotime($from_date) || !strtotime($to_date)) {
            return [false, "Invalid date format in range. Use YYYY-MM-DD for all dates"];
        }

        // Validate date order
        if (strtotime($from_date) >= strtotime($to_date)) {
            return [false, "from_date must be before  to to_date in each range"];
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
            if (in_array($current_date, $booked_dates)) {
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

function validatePeriod($booking_id) {
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
        $valid_cancel = ($current_to_checkin->days > 14 && $current_to_checkin->invert);
    } else {
        // Case 2: Booking made ≤14 days before check-in
        // Must validate within 24 hours of confirmation
        $current_to_confirmation = $current_time->diff($confirmed_at);
        $current_to_confirmation_hours = $current_to_confirmation->h + ($current_to_confirmation->days * 24);
        $valid_cancel = ($current_to_confirmation_hours < 24 && $current_to_confirmation->invert);
    }


    if (!$valid_cancel) {
        $updateSql = "UPDATE tbl_book 
                      SET book_status = 'Cancelled',
                      cancel_by = 'H'
                      WHERE id = $booking_id";
                      $GLOBALS['rstate']->query($updateSql);
    }
    return $valid_cancel;
}

function validateBookingConflict($from_date, $to_date, $prop_id) {
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
                ('" . $GLOBALS['rstate']->real_escape_string($from_date) . "' BETWEEN f1 AND f2) OR
                ('" . $GLOBALS['rstate']->real_escape_string($to_date) . "' BETWEEN f1 AND f2) OR
                (f1 BETWEEN '" . $GLOBALS['rstate']->real_escape_string($from_date) . "' AND '" . $GLOBALS['rstate']->real_escape_string($to_date) . "') OR
                (f2 BETWEEN '" . $GLOBALS['rstate']->real_escape_string($from_date) . "' AND '" . $GLOBALS['rstate']->real_escape_string($to_date) . "')
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