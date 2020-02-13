<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");

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

function get_param($str) {
	$str = $_GET[$str];
	$str = str_replace("'","",$str);
	$str = str_replace('"',"",$str);
	return $str;
}

$lesson_id			= get_param('lesson');
$lesson_grade		= get_param('grade');
$lesson_count		= get_param('lesson_week_count');
$lesson_length	= get_param('course_minutes');
$course_id			= get_param('course_type');
$jyukensei_flag	= get_param('jyukensei_flag');

try {

	$sql = "SELECT lesson_fee FROM tbl_lesson_fee ".
			"WHERE lesson_id=? AND course_id=? AND lesson_grade<=? AND lesson_count=? AND lesson_length=? AND end_month='' ".
			"AND jyukensei_flag=? AND (?=0 OR ? IN(5,6,7)) ".
			"ORDER BY lesson_grade DESC";
	$stmt = $db->prepare($sql);
	$stmt->execute(array($lesson_id, $course_id, $lesson_grade, $lesson_count, $lesson_length, $jyukensei_flag, $jyukensei_flag, $lesson_grade));
	$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($rslt[0]) {
		$res = array(
			'status'=>'success',
			'message'=>'',
			'description'=>'',
			'data'=>$rslt[0]
			);
	} else {
		$res = array(
			'status'=>'error',
			'message'=>'データが存在しません',
			'description'=>'',
			'data'=>NULL
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
