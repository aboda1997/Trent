<?php
require 'reconfig.php';


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

function validateFacilityIds($idString)
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

    $query = "SELECT id FROM tbl_facility WHERE id IN ($idList)";
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

    
    // Check for Google Maps coordinates (works for both search and place links)
    $googlePatterns = [
         '/(\-?\d+\.\d+),\s*\+?(\-?\d+\.\d+)/'

    ];

    foreach ($googlePatterns as $item) {
        if (preg_match($item, $url, $matches)) {
            return [
                'status' => true,
                'latitude' => $matches[1],
                'longitude' => $matches[2]
            ];
        }
    }


    return ['status' => false, 'response' => 'MAP URL does not contain valid coordinates'];
}


function validateName($name, $placeholder, $max = 50)
{
    // Trim whitespace from the name
    $name = trim($name);
    if ($name == '') {
        return ['status' => false, 'response' => "" . $placeholder . " are required"];
    }
    // Check length and allow only letters, spaces, and basic punctuation
    if (strlen($name) <= 3 || strlen($name) > $max) {
        return ['status' => false, 'response' => "" . $placeholder . " must be between 3 and $max characters."];
    }

    // Ensure the name contains only valid characters (letters, spaces, hyphens, apostrophes)
    if (!preg_match('/^[a-zA-Z\s\'\-]+$/u', $name)) {
        return ['status' => false, 'response' => 'Invalid' . $placeholder . ' format.'];
    }

    return ['status' => true, 'response' => 'Valid ' . $placeholder . '.'];
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
