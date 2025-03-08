<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';

header('Content-type: text/json');
try{
$lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

$search_term = isset($_GET['compound_name']) ? $rstate->real_escape_string($_GET['compound_name']) : null;

$pol = array();
$c = array();
$cc = array();
$ccc = array();
$period = array();

$sel = $rstate->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM tbl_property WHERE status=1")->fetch_assoc();
$c['min_price']  = $sel['min_price'];
$c['max_price']  = $sel['max_price'];
$loop = 1;
$periods = [
    ["name" => ["ar" => "يومي", "en" => "daily"], "id" => 'd'],
    ["name" => ["ar" => "شهري", "en" => "monthly"],  "id" => 'm']
];
while ($loop >= 0) {
    $period['id'] = $periods[$loop]['id'];
    $period['name'] = $periods[$loop]['name'][$lang_code];
    $loop -= 1;
    $ccc[] = $period;
}
// Build query dynamically
$query = "SELECT DISTINCT JSON_UNQUOTE(JSON_EXTRACT(compound_name, '$.$lang_code')) AS name, id FROM tbl_property";

// If a search term is provided, add a LIKE condition for partial matching
if ($search_term) {
    $query .= " WHERE 
        (JSON_UNQUOTE(JSON_EXTRACT(compound_name, '$.en')) LIKE '%$search_term%' 
        OR JSON_UNQUOTE(JSON_EXTRACT(compound_name, '$.ar')) LIKE '%$search_term%')";
}
$sel = $rstate->query($query);

while ($row = $sel->fetch_assoc()) {

    $pol['name'] = $row['name'];

    $cc[] = $pol;
}
$returnArr    = generateResponse('true', "Api Filiters Founded!", 200 , array(
    
    "price_range" => $c,
    "compound_list" =>$cc,
    "period_list" => $ccc
 ));


echo $returnArr;

} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ));
    echo $returnArr;
}
?>