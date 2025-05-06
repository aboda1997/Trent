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
        $query .= " and add_user_id = $uid ";
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
