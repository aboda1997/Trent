<?php
require dirname(dirname(__FILE__)) . '/include/reconfig.php';
require dirname(dirname(__FILE__)) . '/include/helper.php';
require_once dirname(dirname(__FILE__)) . '/user_api/error_handler.php';

header('Content-Type: application/json');
try {
    $lang_code = isset($_GET['lang']) ? $rstate->real_escape_string($_GET['lang']) : 'en';

    $compound_name = isset($_GET['compound_name']) ? $rstate->real_escape_string($_GET['compound_name']) : null;
    $city_name = isset($_GET['city_name']) ? $rstate->real_escape_string($_GET['city_name']) : null;
    $government_id = isset($_GET['government_id']) ? $rstate->real_escape_string($_GET['government_id']) : null;
    $compound_list= array();
    $city_list= array();
    $cat_list= array();
    $c = array();
    $ccc = array();
    $period = array();
    if ($government_id == null) {
        $returnArr = generateResponse('false', "You must enter government id!", 400);
    } else if ($city_name == null && $compound_name != null) {
        $returnArr = generateResponse('false', "You must enter city name!", 400);
    } else {
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
        $query = "
    SELECT  JSON_UNQUOTE(JSON_EXTRACT(p.compound_name, '$.$lang_code')) AS compound_name
    ,  JSON_UNQUOTE(JSON_EXTRACT(p.city, '$.$lang_code')) AS city_name
     ,p.price,
     JSON_UNQUOTE(JSON_EXTRACT(c.title, '$.$lang_code')) AS title,
      p.ptype
     

    FROM tbl_property p
    LEFT JOIN 
		tbl_category c ON p.ptype = c.id 
    WHERE 
    p.government = $government_id
    and JSON_UNQUOTE(JSON_EXTRACT(p.compound_name, '$.$lang_code')) IS NOT NULL
    and JSON_UNQUOTE(JSON_EXTRACT(p.city, '$.$lang_code')) IS NOT NULL
    and p.status=1
";
        // If a search term is provided, add a LIKE condition for partial matching
        if ($compound_name) {
            $query .= " and 
        (JSON_UNQUOTE(JSON_EXTRACT(p.compound_name, '$.en')) LIKE '%$compound_name%' 
        OR JSON_UNQUOTE(JSON_EXTRACT(p.compound_name, '$.ar')) LIKE '%$compound_name%')";
        }
        if ($city_name) {
            $query .= " and 
        (JSON_UNQUOTE(JSON_EXTRACT(p.city, '$.en')) LIKE '%$city_name%' 
        OR JSON_UNQUOTE(JSON_EXTRACT(p.city, '$.ar')) LIKE '%$city_name%')";
        }

        $sel = $rstate->query($query);
        var_dump($query);
        // Initialize arrays
        $compounds = [];
        $cities = [];
        $ptypes = [];
        $prices = [];

        // Process results
        while ($row = $sel->fetch_assoc()) {
            // Collect distinct values
            if (!in_array($row['compound_name'], $compounds) && $row['compound_name'] !='' ) {
                $compounds[] = $row['compound_name'];
                $compound_list[] = ['name'=>$row['compound_name'] ];
            }

            if (!in_array($row['city_name'], $cities)) {
                $cities[] =  $row['city_name'];
                $city_list[] = ['name'=>$row['city_name'] ];
            }

            if (!in_array($row['title'], $ptypes)) {
                $ptypes[] = $row['title'];
                $cat_list[] = ['title'=>$row['title'] , 'id' =>$row['ptype'] ];
            }

            // Collect all prices for min/max calculation
            $prices[] = $row['price'];
        }

        // Calculate min and max prices
        $minPrice = min($prices);
        $maxPrice = max($prices);
        $c['min_price']  =  $minPrice ;
        $c['max_price']  = $maxPrice;
        $returnArr    = generateResponse('true', "Api Filiters Founded!", 200, array(

            "price_range" => $c,
            "compound_list" => $compound_list,
            "city_list" => $city_list,
            "category_list" => $cat_list,
            "period_list" => $ccc
        ));
    }

    echo $returnArr;
} catch (Exception $e) {
    // Handle exceptions and return an error response
    $returnArr = generateResponse('false', "An error occurred!", 500, array(
        "error_message" => $e->getMessage()
    ), $e->getFile(),  $e->getLine());
    echo $returnArr;
}
