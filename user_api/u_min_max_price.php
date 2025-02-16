<?php 
require dirname( dirname(__FILE__) ).'/include/reconfig.php';

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);

$pol = array();
$c = array();
$sel = $rstate->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM tbl_property WHERE status=1");
if($sel->num_rows > 0)
{
    $sel = $sel->fetch_assoc();
    $returnArr = array("min_price"=>$sel['min_price'] , "max_price"=>$sel['max_price'] ,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Min-max-price Founded!");

}
else 
{
    
    $sel = $sel->fetch_assoc();
    $returnArr = array("min_price"=>Null , "max_price"=>Null  ,"ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"Min-max-price Not Founded!");

}
echo json_encode($returnArr);
?>