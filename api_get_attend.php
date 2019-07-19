<?php
ini_set( 'display_errors', 0 );
require_once("/home/hachiojisakura/www/sakura/schedule/const/const.inc");
require_once("/home/hachiojisakura/www/sakura/schedule/func.inc");

define(API_TOKEN, '7511a32c7b6fd3d085f7c6cbe66049e7');

$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
// Comment out for temporary.
//if ($token != API_TOKEN) {
//	http_response_code(403);
//	exit;
//}

$request_schedule_id = $_GET['schedule_id'];
$request_schedule_id = str_replace("'","",$request_schedule_id);
$request_schedule_id = str_replace('"',"",$request_schedule_id);

$user="hachiojisakura";
$pass="20160401sakurasaku";

try {

// For temporary
$dbh=new PDO('mysql:host=mysql720.db.sakura.ne.jp;dbname=hachiojisakura_calendar;charset=utf8',$user,$pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
// For temporary End.

	$sql = "SELECT id,schedule_id,attend,updatetime,updateuser".
		" FROM tbl_attend ";

		if ($request_schedule_id) {
			$sql .= " WHERE schedule_id='$request_schedule_id'";
		}
		
		$stmt = $dbh->query($sql);
		$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($rslt) {
		$res = array(
			'status'=>'0',
			'data'=>$rslt
			);
	} else {
		$res = array(
			// Not found.
			'status'=>'1',
			'data'=>$request_schedule_id
			);
	}
exit_label:
		// exit the program.


} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		'data'=>''
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>
