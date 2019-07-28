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

$request_id = $_GET['id'];
$request_id = str_replace("'","",$request_id);
$request_id = str_replace('"',"",$request_id);

$request_target_id = $_GET['target_id'];
$request_target_id = str_replace("'","",$request_target_id);
$request_target_id = str_replace('"',"",$request_target_id);

$request_teacher_id = $_GET['teacher_id'];
$request_teacher_id = str_replace("'","",$request_teacher_id);
$request_teacher_id = str_replace('"',"",$request_teacher_id);

$request_student_no = $_GET['student_no'];
$request_student_no = str_replace("'","",$request_student_no);
$request_student_no = str_replace('"',"",$request_student_no);

$request_lecture_id = $_GET['lecture_id'];
$request_lecture_id = str_replace("'","",$request_lecture_id);
$request_lecture_id = str_replace('"',"",$request_lecture_id);

$request_startdate = $_GET['startdate'];
$request_startdate = str_replace("'","",$request_startdate);
$request_startdate = str_replace('"',"",$request_startdate);

$now = date('Y/m/d');

try {

	$sql = "SELECT ".
		"id,".
		"user_id,".
		"teacher_id,".
		"student_no,".
		"group_lesson_id,".
		"subject_expr,".
		"kind,".
		"dayofweek,".
		"dayofmonth,".
		"startdate,".
		"enddate,".
		"starttime,".
		"endtime,".
		"lecture_id,".
		"work_id,".
		"free,".
		"place_id,".
		"recess,".
		"entrytime,".
		"updatetime,".
		"updateuser,".
		"comment".
		" FROM tbl_schedule_repeat WHERE delflag!=1";

		if ($request_startdate) {
			// check date format.
			sscanf($request_startdate,'%d-%d-%d',$year,$month,$day);
			if (!checkdate($month, $day, $year)){
				// illeagal date format.
				$res = array(
				'status'=>'2',
				'data'=>$rslt
				);
				goto exit_label;
			} 
			$sql .= " AND startdate < '$request_startdate' ";
		}
		
		if ($request_id) {
			$sql .= " AND id='$request_id'";
		}
		if ($request_target_id) {
			$sql .= " AND user_id='$request_target_id'";
		}
		if ($request_teacher_id) {
			$sql .= " AND teacher_id='$request_teacher_id'";
		}
		if ($request_student_no) {
			$sql .= " AND student_no='$request_student_no'";
		}
		if ($request_lecture_id) {
			$sql .= " AND lecture_id='$request_lecture_id'";
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
			'data'=>$request_target
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
