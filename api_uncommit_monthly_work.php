<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
require_once("./const.inc");
require_once("./func.inc");

define(API_TOKEN, '7511a32c7b6fd3d085f7c6cbe66049e7');

$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
// Comment out for temporary.
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}

$request_year = $_POST['year'];
$request_year = str_replace("'","",$request_year);
$request_year = str_replace('"',"",$request_year);
$request_year = (int)$request_year;

$request_month = $_POST['month'];
$request_month = str_replace("'","",$request_month);
$request_month = str_replace('"',"",$request_month);
$request_month = (int)$request_month;

$now = date('Y-m-d H:i:s');

try {

	$sql = "SELECT COUNT(*) AS COUNT FROM tbl_fixed WHERE year=? AND month=? ";
//        $stmt = $db->prepare($sql);
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1,$request_year, PDO::PARAM_INT);
        $stmt->bindValue(2,$request_month, PDO::PARAM_INT);
        $stmt->execute();
        $already_exist = (int)$stmt->fetchColumn();
	if ($already_exist == 0 ) {
			// not found.
		$res = array(
		'status'=>'1',
		'data'=>$rslt
		);
		goto exit_label;
	}
	$fixed = 1;
	$sql = "DELETE FROM tbl_fixed WHERE year=? and month=?";

//		$stmt = $db->prepare($sql);
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_year, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_month, PDO::PARAM_INT);
		$stmt->execute();


		$res = array(
			'status'=>'0'
			);
exit_label:
		// exit the program.


} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>
