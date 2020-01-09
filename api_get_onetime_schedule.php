<?php
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");
require_once("./const.inc");
require_once("./func.inc");

define(API_TOKEN, '7511a32c7b6fd3d085f7c6cbe66049e7');

$http_header = getallheaders();
$token = "";
//var_dump($http_header);
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}

define('CANCEL_REASON2','当日');
define('CANCEL_REASON3','規定回数以上');
define('UNDEFINEDTEACHER',100000);
define('UNDEFINEDSTAFF',200000);

$request_id = $_GET['id'];
$request_id = str_replace("'","",$request_id);
$request_id = str_replace('"',"",$request_id);

$request_start_id = $_GET['start_id'];
$request_start_id = str_replace("'","",$request_start_id);
$request_start_id = str_replace('"',"",$request_start_id);

$request_end_id = $_GET['end_id'];
$request_end_id = str_replace("'","",$request_end_id);
$request_end_id = str_replace('"',"",$request_end_id);

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

$request_starttime = $_GET['starttime'];
$request_starttime = str_replace("'","",$request_starttime);
$request_starttime = str_replace('"',"",$request_starttime);

$request_enddate = $_GET['enddate'];
$request_enddate = str_replace("'","",$request_enddate);
$request_enddate = str_replace('"',"",$request_enddate);

$request_endtime = $_GET['endtime'];
$request_endtime = str_replace("'","",$request_endtime);
$request_endtime = str_replace('"',"",$request_endtime);

$request_updatestartdate = $_GET['updatestartdate'];
$request_updatestartdate = str_replace("'","",$request_updatestartdate);
$request_updatestartdate = str_replace('"',"",$request_updatestartdate);

$request_updatestarttime = $_GET['updatestarttime'];
$request_updatestarttime = str_replace("'","",$request_updatestarttime);
$request_updatestarttime = str_replace('"',"",$request_updatestarttime);

$request_updateenddate = $_GET['updateenddate'];
$request_updateenddate = str_replace("'","",$request_updateenddate);
$request_updateenddate = str_replace('"',"",$request_updateenddate);

$request_updateendtime = $_GET['updateendtime'];
$request_updateendtime = str_replace("'","",$request_updateendtime);
$request_updateendtime = str_replace('"',"",$request_updateendtime);

$request_work_id = $_GET['work_id'];
$request_work_id = str_replace("'","",$request_work_id);
$request_work_id = str_replace('"',"",$request_work_id);

$defaultstartdate = "2019-01-01";

try {

	$sql = "SELECT ".
		"id,".
		"repetition_id,".
		"user_id,".
		"teacher_id,".
		"student_no,".
		"ymd,".
		"starttime,".
		"endtime,".
		"lecture_id,".
		"subject_expr,".
		"work_id,".
		"cancel,".
		"cancel_reason,".
		"altsched_id,".
		"trial_id,".
		"place_id,".
		"temporary,".
		"confirm,".
		"entrytime,".
		"updatetime,".
		"updateuser,".
		"comment".
		" FROM tbl_schedule_onetime WHERE delflag!=1".
		" AND user_id != 200000 AND user_id != 100000 AND teacher_id != 100000";

		if (!$request_startdate) {
			$request_startdate=$defaultstartdate;
		} else {
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
		}
		if (!$request_enddate) {
			$oneyearlater = new DateTime();
			$oneyearlater->add(DateInterval::createFromDateString('1 year'));
			$request_enddate = $oneyearlater->format('Y-m-d');
		} else {
			// check date format.
			sscanf($request_enddate,'%d-%d-%d',$year,$month,$day);
			if (!checkdate($month, $day, $year)){
				// illeagal date format.
				$res = array(
				'status'=>'2',
				'data'=>$rslt
				);
				goto exit_label;
			} 
		}
		$sql .= " AND ymd BETWEEN '$request_startdate' AND '$request_enddate' ";
		
		if ($request_starttime) {
			// check time format.
			sscanf($request_starttime,'%d:%d:%d',$hour,$minute,$second);
			if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
				// illeagal time format.
				$res = array(
				'status'=>'3',
				'data'=>$rslt
				);
				goto exit_label;
			} 
			$sql .= " AND start >= '$request_starttime' ";
		} 
		if ($request_endtime) {
			// check time format.
			sscanf($request_endtime,'%d:%d:%d',$hour,$minute,$second);
			if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
				// illeagal time format.
				$res = array(
				'status'=>'3',
				'data'=>$rslt
				);
				goto exit_label;
			} 
			$sql .= " AND end <= '$request_endtime' ";
		} 

		if ($request_id) {
			$sql .= " AND id='$request_id'";
		}
		if ($request_start_id) {
			$sql .= " AND id>= '$request_start_id'";
		}
		if ($request_end_id) {
			$sql .= " AND id<= '$request_end_id'";
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
		if ($request_work_id) {
			$sql .= " AND work_id='$request_work_id'";
		}
		if ($request_updatestartdate) {
			// check date format.
			sscanf($request_updatestartdate,'%d-%d-%d',$year,$month,$day);
			if (!checkdate($month, $day, $year)){
				// illeagal date format.
				$res = array(
				'status'=>'2',
				'data'=>$rslt
				);
				goto exit_label;
			} 
		}
		if ($request_updatestarttime) {
			// check time format.
			sscanf($request_updatestarttime,'%d:%d:%d',$hour,$minute,$second);
			if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
				// illeagal time format.
				$res = array(
				'status'=>'3',
				'data'=>$rslt
				);
				goto exit_label;
			} 
		} 
		if ($request_updatestartdate||$request_updatestarttime) {
			$updatestartdatetime = $request_updatestartdate.$request_updatestarttime;
			$sql .= " AND updatetime >= '$updatestartdatetime' ";
		}
 
		if ($request_updateenddate) {
			// check date format.
			sscanf($request_updateenddate,'%d-%d-%d',$year,$month,$day);
			if (!checkdate($month, $day, $year)){
				// illeagal date format.
				$res = array(
				'status'=>'2',
				'data'=>$rslt
				);
				goto exit_label;
			} 
		}
		if ($request_updateendtime) {
			// check time format.
			sscanf($request_updateendtime,'%d:%d:%d',$hour,$minute,$second);
			if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
				// illeagal time format.
				$res = array(
				'status'=>'3',
				'data'=>$rslt
				);
				goto exit_label;
			} 
		} 
		if ($request_updateenddate||$request_updateendtime) {
			$updateenddatetime = $request_updateenddate.$request_updateendtime;
			$sql .= " AND updatetime <= '$updateenddatetime' ";
		}
		
		
		$stmt = $dbh->query($sql);
		$rslt = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if ($rslt) {
		$row_cnt = count($rslt);
		$responce_array = array();

		foreach($rslt as $row ) {
			$alternate = 'false'; // Initialization.
			$altlimitdate = null; // Initialization.
			$limitdate = null;
			$got_ymd = $row['ymd'];
			// $got_ym = mb_substr($got_ymd,0,7); // getting year month data.
			$nextmonth = '+1 month';
			$dateObj = new DateTime($got_ymd);
			$dateObj->add(DateInterval::createFromDateString($nextmonth));
			$alt_limit_ts = $dateObj->getTimestamp();
			$alt_limit = getdate($alt_limit_ts);
			$alt_limitdate = $alt_limit['year'].'-'.$alt_limit['mon'].'-'.$alt_limit['mday'];
			if ($row['cancel']==='a2' && $row['cancel_reason']!=CANCEL_REASON2 && $row['cancel_reason']!= CANCEL_REASON3) {
				$alternate = 'true';
				$altlimitdate = $alt_limitdate;
			}
			if ($row['cancel']==='a1' && $row['cancel_reason']===CANCEL_REASON5) {
				$alternate = 'true';
				$altlimitdate = $alt_limitdate;
			}
			$response = array_merge($row,array('alternate'=>$alternate,'altlimitdate'=>$altlimitdate));
			$response_array[] = $response;
		}
		$res = array(
			'status'=>'0',
			'data'=>$response_array
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
