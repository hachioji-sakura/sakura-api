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
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}

$lecture_id = $_GET[id];
if ($lecture_id) {
	$lecture_id   = str_replace('"',"",str_replace("'","",$lecture_id));
}

try {

	$sql = "SELECT lecture_id, course_id, subject_id, lesson_id FROM tbl_lecture";
	if ($lecture_id) {
		$sql .= " WHERE lecture_id='$lecture_id'";
	}
	$stmt = $db->query($sql);
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($lecture_id) {
		$str1 = "id=$lecture_id Found";
		$str2 = "id=$lecture_id Not Found";
	} else {
		$str1 = "All data";
		$str2 = "No data";
	}

	if ($rslt) {
		$res = array(
			'status'=>'success',
			'message'=>'',
			'description'=>$str1,
			'data'=>$rslt
			);
	} else {
		$res = array(
			'status'=>'error',
			'message'=>'データが存在しません',
			'description'=>$str2,
			'data'=>$rslt
			);
	}

} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		'message'=>'exception',
		'description'=>$e->getMessage(),
		'data'=>''
		);

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($http_header);
//var_dump($token);

echo json_encode($res);

?>