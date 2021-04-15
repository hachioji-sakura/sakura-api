<?php
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set( 'display_errors', 0 );
require_once("../sakura/schedule/const/const.inc");
require_once("../sakura/schedule/func.inc");

$http_header = getallheaders();
$token = "";
if(isset($http_header["Api-Token"])){
		$token = $http_header["Api-Token"];
}
if ($token != API_TOKEN) {
	http_response_code(403);
	exit;
}

define('CANCEL_NOREASON','');
define('CANCEL_REASON2','当日');
define('CANCEL_REASON3','規定回数以上');
define('CANCEL_REASON4','休講');
define('CANCEL_REASON5','振替事前連絡あり');
define('CANCEL_ARROWORE','アローレ都合');
define('CANCEL_SELFREASON','自己都合');

define('COURSE_GROUP',2);
define('COURSE_FAMILY',3);

define('PLACE_AROLE',6);

$logfile = '../sakura/schedule/log/api_update_onetime_schedule.log';
$errfile = '../sakura/schedule/log/api_update_onetime_schedule.err';
file_put_contents($logfile, date("----------- Y/m/d H:i:s \n"), FILE_APPEND);
ob_start();var_dump($_POST);echo"\n";$log = ob_get_contents(); ob_end_clean();
file_put_contents($logfile, $log, FILE_APPEND);
//var_dump($_POST['type']);
$request_id = $_POST['id'];
$request_id = str_replace("'","",$request_id);
$request_id = str_replace('"',"",$request_id);
if (!$request_id){
// id is a mandatory parameter.
$res = array(
'status'=>'1',
);
goto exit_label;
} 

$request_updateuser = $_POST['updateuser'];
$request_updateuser = str_replace("'","",$request_updateuser);
$request_updateuser = str_replace('"',"",$request_updateuser);
if (!$request_updateuser){
// update user is a mandatory parameter.
$res = array(
'status'=>'1',
);
goto exit_label;
} 

$request_repetition_id = $_POST['repetition_id'];
$request_repetition_id = str_replace("'","",$request_repetition_id);
$request_repetition_id = str_replace('"',"",$request_repetition_id);

$request_user_id = $_POST['user_id'];
$request_user_id = str_replace("'","",$request_user_id);
$request_user_id = str_replace('"',"",$request_user_id);

$request_teacher_id = $_POST['teacher_id'];
$request_teacher_id = str_replace("'","",$request_teacher_id);
$request_teacher_id = str_replace('"',"",$request_teacher_id);


$request_student_no = $_POST['student_no'];
$request_student_no = str_replace("'","",$request_student_no);
$request_student_no = str_replace('"',"",$request_student_no);

$request_ymd = $_POST['ymd'];
$request_ymd = str_replace("'","",$request_ymd);
$request_ymd = str_replace('"',"",$request_ymd);

if ($request_ymd){
	sscanf($request_ymd,'%d-%d-%d',$year,$month,$day);

	if (!checkdate($month, $day, $year)){
	// illeagal date format.
	$res = array(
	'status'=>'2',
	);
	goto exit_label;
	} 
} 

$request_starttime = $_POST['starttime'];
$request_starttime = str_replace("'","",$request_starttime);
$request_starttime = str_replace('"',"",$request_starttime);

if ($request_starttime){
	sscanf($request_starttime,'%d:%d:%d',$hour,$minute,$second);
	if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
		// illeagal time format.
		$res = array(
		'status'=>'3',
		'data'=>$rslt
		);
	goto exit_label;
	}
}
 
$request_endtime = $_POST['endtime'];
$request_endtime = str_replace("'","",$request_endtime);
$request_endtime = str_replace('"',"",$request_endtime);

if ($request_endtime){
	sscanf($request_endtime,'%d:%d:%d',$hour,$minute,$second);
	if ($hour > 23 || $hour < 0 || $minute > 59 || $minute < 0 || $second > 59 || $second < 0 ){
		// illeagal time format.
		$res = array(
		'status'=>'3',
		'data'=>$rslt
		);
	goto exit_label;
	}
}
 
$request_lecture_id = $_POST['lecture_id'];
$request_lecture_id = str_replace("'","",$request_lecture_id);
$request_lecture_id = str_replace('"',"",$request_lecture_id);

$request_group_lesson_id = $_POST['group_lesson_id'];
$request_group_lesson_id = str_replace("'","",$request_group_lesson_id);
$request_group_lesson_id = str_replace('"',"",$request_group_lesson_id);

$request_subject_expr = $_POST['subject_expr'];
$request_subject_expr = str_replace("'","",$request_subject_expr);
$request_subject_expr = str_replace('"',"",$request_subject_expr);

$request_work_id = $_POST['work_id'];
$request_work_id = str_replace("'","",$request_work_id);
$request_work_id = str_replace('"',"",$request_work_id);

$request_free = $_POST['free'];
$request_free = str_replace("'","",$request_free);
$request_free = str_replace('"',"",$request_free);

$request_cancel = $_POST['cancel'];
$request_cancel = str_replace("'","",$request_cancel);
$request_cancel = str_replace('"',"",$request_cancel);

$request_cancel_reason = $_POST['cancel_reason'];
$request_cancel_reason = str_replace("'","",$request_cancel_reason);
$request_cancel_reason = str_replace('"',"",$request_cancel_reason);

$request_alternate = $_POST['alternate'];
$request_alternate = str_replace("'","",$request_alternate);
$request_alternate = str_replace('"',"",$request_alternate);

$request_altsched_id = $_POST['altsched_id'];
$request_altsched_id = str_replace("'","",$request_altsched_id);
$request_altsched_id = str_replace('"',"",$request_altsched_id);

$request_altlimitdate = $_POST['altlimitdate'];
$request_altlimitdate = str_replace("'","",$request_altlimitdate);
$request_altlimitdate = str_replace('"',"",$request_altlimitdate);

$request_trial_id = $_POST['trial_id'];
$request_trial_id = str_replace("'","",$request_trial_id);
$request_trial_id = str_replace('"',"",$request_trial_id);

$request_repeattimes = $_POST['repeattimes'];
$request_repeattimes = str_replace("'","",$request_repeattimes);
$request_repeattimes = str_replace('"',"",$request_repeattimes);

$request_place_id = $_POST['place_id'];
$request_place_id = str_replace("'","",$request_place_id);
$request_place_id = str_replace('"',"",$request_place_id);

$request_temporary = $_POST['temporary'];
$request_temporary = str_replace("'","",$request_temporary);
$request_temporary = str_replace('"',"",$request_temporary);

$request_recess = $_POST['recess'];
$request_recess = str_replace("'","",$request_recess);
$request_recess = str_replace('"',"",$request_recess);

$request_confirm = $_POST['confirm'];
$request_confirm = str_replace("'","",$request_confirm);
$request_confirm = str_replace('"',"",$request_confirm);

$request_additional = $_POST['additional'];
$request_additional = str_replace("'","",$request_additional);
$request_additional = str_replace('"',"",$request_additional);

$request_comment = $_POST['comment'];
$request_comment = str_replace("'","",$request_comment);
$request_comment = str_replace('"',"",$request_comment);

$request_type = $_POST['type'];
$request_type = str_replace("'","",$request_type);
$request_type = str_replace('"',"",$request_type);

$now = date('Y-m-d H:i:s');

try {
				// getting necessary information of the target data.
	$sql ="SELECT user_id,teacher_id,student_no,ymd,lecture_id,place_id,altsched_id,trial_id,cancel,cancel_reason,work_id,delflag " ;
	$sql .=" FROM tbl_schedule_onetime WHERE id = ? " ;
	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
	$stmt->execute();
	$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
	if (!$rslt){
		$res = array(
			'status'=>'notfound'
			);
		goto exit_label;
	}
	$got_user_id = $rslt['user_id'];
	$got_teacher_id = $rslt['teacher_id'];
	$got_student_no = $rslt['student_no'];
	$got_ymd = $rslt['ymd'];   
	$got_ym = mb_substr($got_ymd,0,7);   // getting year month data like 2020-01 format.
	$got_y = mb_substr($got_ymd,0,4);   // getting year data like 2020 format.
	$got_m = mb_substr($got_ymd,5,2);   // getting month data like 01 format.
	$got_lecture_id = (int)$rslt['lecture_id'];
	$got_place_id = (int)$rslt['place_id'];
	$got_trial_id = (int)$rslt['trial_id'];
	$got_altsched_id = (int)$rslt['altsched_id'];
	$got_cancel = $rslt['cancel'];
	$got_cancel_reason = $rslt['cancel_reason'];
	if ($got_cancel_reason == ' ' ){
		$got_cancel_reason = '';	// change space into null character.
	}
	$got_work_id = $rslt['work_id'];
	$got_delflag = $rslt['delflag'];
				// error check.
	if ($request_type === 'rest_cancel' && ( !$got_cancel  && $got_cancel===' ')) {
				// not set. 
		$res = array(
		'status'=>'notset',
		'cancel'=>$absent_id, 
		);
		goto exit_label;
	}

	if ( $got_lecture_id) {
				// course id の取得
		$sql = "SELECT course_id,lesson_id from tbl_lecture WHERE lecture_id= ?";
		$stmt = $db->prepare($sql);
		$stmt->bindValue(1,$got_lecture_id, PDO::PARAM_INT);
		$stmt->execute();
		$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
						// the data is determined uniquely.
		$got_course_id = $rslt['course_id'];
		$got_lesson_id = $rslt['lesson_id'];
	}

	$sql = "UPDATE tbl_schedule_onetime SET ";
	if ($request_repetition_id){
		$sql .=" repetition_id = '$request_repetition_id', " ;
	}
	if ($request_user_id){
		$sql .=" user_id = '$request_user_id', " ;
	}
	if ($request_teacher_id){
		$sql .=" teacher_id = '$request_teacher_id', " ;
	}
	if ($request_student_no){
		$sql .=" student_no = '$request_student_no', " ;
	}
	if ($request_ymd){
		$sql .=" ymd = '$request_ymd', " ;
	}
	if ($request_starttime){
		$sql .=" starttime = '$request_starttime', " ;
	}
	if ($request_endtime){
		$sql .=" endtime = '$request_endtime', " ;
	}
	if ($request_lecture_id){
		$sql .=" lecture_id = '$request_lecture_id', " ;
	}
	if ($request_group_lesson_id){
		$sql .=" group_lesson_id = '$request_group_lesson_id', " ;
	}
	if ($request_subject_expr){
		$sql .=" subject_expr = '$request_subject_expr', " ;
	}
	if ($request_work_id){
		$sql .=" work_id = '$request_work_id', " ;
	}
	if ($request_free){
		$sql .=" free = '$request_free', " ;
	}
	if ($request_cancel){
		$sql .=" cancel = '$request_cancel', " ;
	}
	if (isset($_POST['cancel_reason'])){
		$sql .=" cancel_reason = '$request_cancel_reason', " ;
	}
	if ($request_alternate){
		$sql .=" alternate = '$request_alternate', " ;
	}
	if ($request_altsched_id){
		$sql .=" altsched_id = '$request_altsched_id', " ;
	}
	if ($request_altlimitdate){
		$sql .=" altlimitdate = '$request_altlimitdate', " ;
	}
	if ($request_trial_id){
		$sql .=" trial_id = '$request_trial_id', " ;
	}
	if ($request_repeattimes){
		$sql .=" repeattimes = '$request_repeattimes', " ;
	}
	if ($request_place_id){
		$sql .=" place_id = '$request_place_id', " ;
	}
	if ($request_temporary){
		$sql .=" temporary = '$request_temporary', " ;
	}
	if ($request_confirm){
		$sql .=" confirm = '$request_confirm', " ;
	}
	if ($request_recess){
		$sql .=" recess = '$request_recess', " ;
	}
	$sql .=" updatetime = ?, updateuser = ? WHERE id = ? " ;

	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(1, $now, PDO::PARAM_STR);
	$stmt->bindValue(2, $request_updateuser, PDO::PARAM_INT);
	$stmt->bindValue(3, $request_id, PDO::PARAM_INT);
	$stmt->execute();

	$absent_id = null; 	// Initialization
	$alternate = 'false'; 	// Initialization
				// if the request is for absent, judge what kind of absent it is.

	$absent_month_start = $got_ym.'-01';  			// beggining of the month.
	$dateObj = new DateTime($absent_month_start);   
	$absent_month_start_ts = $dateObj ->getTimestamp();  
	$absent_year = date('Y',$absent_month_start_ts);  
	$absent_month = date('m',$absent_month_start_ts);  
	$absent_month_end_ts = mktime(0,0,0,$absent_month+1,0,$absent_year);  // the end of the month 
	$month_end = getdate($absent_month_end_ts); 			
	$absent_month_end = $month_end['year'].'-'.$month_end['mon'].'-'.$month_end['mday'];  

	$absent_2month = ($got_m-1)/2;  			
	$absent_2month = (integer)$absent_2month *2 +1;  			// beggining of the odd month.
	$absent_2month_start = $got_y.'-'.$absent_2month.'-01';  			// beggining of the month.
	$dateObj = new DateTime($absent_2month_start);   
	$absent_2month_start_ts = $dateObj ->getTimestamp();  
	$absent_2month_month = date('m',$absent_2month_start_ts);  
	$absent_2month_end_ts = mktime(0,0,0,$absent_month+2,0,$got_y);  // the end of the month 
	$month_end = getdate($absent_2month_end_ts); 			
	$absent_2month_end = $month_end['year'].'-'.$month_end['mon'].'-'.$month_end['mday'];  

	$restcare_inf = get_restcare_inf($dbc,$got_user_id,$got_ymd);
	$holiday_is_a1 = $restcare_inf[0];		// true if the student is specified and the day is holiday.
	$rest_is_a1 = $restcare_inf[1];			// true if the student is specified as rest is a1.
	$first_rest_is_a1 = $restcare_inf[2];		// true if the student is specified as first rest is a1.
	$rest_add_exchange_enable = $restcare_inf[3];	// true if the student is specified as add exchange enable.

	if ($request_type==='rest' ){
		
						// 休み連絡
		if ($got_user_id > 100000 ) { // teacher or staff. No need to check absent category.
			$absent_id = 'a';
			$sql = "UPDATE tbl_schedule_onetime SET cancel = ? WHERE id = ?";	
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1,$absent_id, PDO::PARAM_STR);
			$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
			$stmt->execute();
			goto normal_label;
		}
		if ($request_comment == CANCEL_ARROWORE ) {
			$absent_id = 'a1';
			$request_cancel_reason = CANCEL_ARROWORE ;
			goto updatedb_label;
		}
		if ($request_comment === CANCEL_SELFREASON) {
			$absent_id = 'a2';
			$request_cancel_reason = CANCEL_SELFREASON ;
			goto updatedb_label;
		}
		
		// グループ授業で既に休み登録されている場合
		if (($got_course_id == COURSE_GROUP || $got_course_id == COURSE_FAMILY) && $got_cancel) {
			$request_cancel = $got_cancel;
			$request_cancel_reason = $got_cancel_reason;
			goto normal_label;
		}

		$currentObj = new DateTime();
								// 21:00 + 3 hours makes 0:00 next day.
		$currentObj -> add(DateInterval::createFromDateString('3 hours'));
		$current_timestamp = $currentObj -> getTimestamp();

                $limitdateObj = new Datetime($got_ymd);
                $limitdate_timestamp = $limitdateObj -> getTimestamp();

				// for season class processing.
		switch ($got_course_id){
		case '4':	// Summer seminar.
		case '5':	// Winter seminar.
		case '6':	// Spring seminar.
		case '9':	// Weekend seminar.
			if ( $limitdate_timestamp > $current_timestamp ) { // absent1.
				$absent_id = 'a1';
 				$sql = "UPDATE tbl_schedule_onetime SET cancel=? ,";
                		$sql .=" updatetime = ?, updateuser = ? WHERE id = ? " ;
               			$stmt = $dbh->prepare($sql);
                		$id = (int) $request_id;
                		$stmt->bindValue(1, $absent_id, PDO::PARAM_STR);
                		$stmt->bindValue(2, $now, PDO::PARAM_STR);
                		$stmt->bindValue(3, $request_updateuser, PDO::PARAM_INT);
                		$stmt->bindValue(4, $request_id, PDO::PARAM_INT);
                		$stmt->execute();

        			$sql = "INSERT INTO tbl_schedule_onetime_history (".
               			"id,".
                		"cancel,".
                		"updatetime,".
                		"updateuser)".
                		" VALUES ( ?, ?, ?, ? )";

                		$stmt = $dbh->prepare($sql);
                		$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
                		$stmt->bindValue(2,$absent_id, PDO::PARAM_INT);
                		$stmt->bindValue(3,$now, PDO::PARAM_STR);
                		$stmt->bindValue(4,$request_updateuser, PDO::PARAM_INT);
                		$stmt->execute();
				goto afterupdate_label;
			} else if ( $limitdate_timestamp < $current_timestamp ) {
						// $restcare_inf:'none','a1always','a1iflessthan2','a1onholiday'
				if ($holiday_is_a1 == true || $rest_is_a1 == true || $first_rest_is_a1 == true ){
					$absent_id = 'a1';
					$request_cancel_reason = '';
				} else {
					$absent_id = 'a2';
					$request_cancel_reason = CANCEL_REASON2 ; // Today
				}
				goto updatedb_label;
			}
			break;
		default:
				// not season seminar.
			break;
		}
										// check how many lessons in a week .

		$sql = "SELECT COUNT(*) AS COUNT FROM tbl_schedule_repeat A,tbl_lecture B WHERE A.delflag=0 ";
		$sql .= " AND A.user_id=? AND A.work_id=? AND A.kind='w' AND (A.enddate IS NULL OR A.enddate > ?)";
		$sql .= " AND A.lecture_id=B.lecture_id AND B.lesson_id=? ";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$got_user_id, PDO::PARAM_INT);
		$stmt->bindValue(2,$got_work_id, PDO::PARAM_INT);
		$stmt->bindValue(3,$got_ymd, PDO::PARAM_STR);
		$stmt->bindValue(4,$got_lesson_id, PDO::PARAM_INT);
		$stmt->execute();
		$absent_threshold_weekly1 = (int)$stmt->fetchColumn();

		$absent_threshold_weekly = get_lesson_count($db, str_pad($got_student_no,6,'0',STR_PAD_LEFT), $got_y, $got_m, $got_lesson_id, $got_course_id);
		if (!$absent_threshold_weekly || $absent_threshold_weekly!=$absent_threshold_weekly1) {
			file_put_contents($errfile, date("Y/m/d H:i:s ")."$absent_threshold_weekly,$absent_threshold_weekly1\n", FILE_APPEND);
		}

										// check how many lessons in a month .
		$sql = "SELECT COUNT(*) AS COUNT FROM tbl_schedule_repeat A,tbl_lecture B WHERE A.delflag=0 ";
		$sql .= " AND A.user_id=? AND A.work_id=? AND A.kind='m' AND (A.enddate IS NULL OR A.enddate > ?)";
		$sql .= " AND A.lecture_id=B.lecture_id AND B.lesson_id=? ";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$got_user_id, PDO::PARAM_INT);
		$stmt->bindValue(2,$got_work_id, PDO::PARAM_INT);
		$stmt->bindValue(3,$got_ymd, PDO::PARAM_STR);
		$stmt->bindValue(4,$got_lesson_id, PDO::PARAM_INT);
		$stmt->execute();
		$absent_threshold_monthly = (int)$stmt->fetchColumn();

		if ($got_course_id == COURSE_GROUP || $got_course_id == COURSE_FAMILY || $got_lesson_id == 3/* PIANO */){
			$cancel_kind = 'a1';	// 休み１
		} else {
			$cancel_kind = 'a2';	// 休み２
		}
							// 対象月のcancel_reason なしの休みの数を調べる
		if ($absent_threshold_monthly == 0 ){	
							// weekly chedule.
			$sql = "SELECT COUNT(*) AS COUNT FROM tbl_schedule_onetime A,tbl_lecture B "; 
			$sql .= "WHERE A.lecture_id=B.lecture_id AND A.delflag = 0 AND A.ymd BETWEEN ? AND ? AND A.cancel = ?" ;
			$sql .=" AND A.user_id=? AND A.work_id = ? AND (A.cancel_reason = ' ' OR A.cancel_reason='' OR A.cancel_reason IS NULL)" ; 
			$sql .=" AND B.lesson_id=? " ; 
			if ($first_rest_is_a1 == true && $cancel_kind == 'a2' ) {
				$sql = $sql . " OR cancel=? "; 		// need to check 'a1' also.
			}
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1,$absent_month_start, PDO::PARAM_STR);
			$stmt->bindValue(2,$absent_month_end, PDO::PARAM_STR);
			$stmt->bindValue(3,$cancel_kind, PDO::PARAM_STR);
			$stmt->bindValue(4,$got_user_id, PDO::PARAM_INT);
			$stmt->bindValue(5,$got_work_id, PDO::PARAM_INT);
			$stmt->bindValue(6,$got_lesson_id, PDO::PARAM_INT);
			if ($first_rest_is_a1 == true && $cancel_kind == 'a2' ) {
				$cancel_kind2 = 'a1';			// need to check 'a1' also.
				$stmt->bindValue(6,$cancel_kind2, PDO::PARAM_STR);
			}

			$stmt->execute();
			$absent_cnt = (int)$stmt->fetchColumn();
			$absent_threshold = $absent_threshold_weekly;
		} else {			
							// monthly schedule.
			$sql = "SELECT COUNT(*) AS COUNT FROM tbl_schedule_onetime A,tbl_lecture B "; 
			$sql .= "WHERE A.lecture_id=B.lecture_id AND A.delflag = 0 AND A.ymd BETWEEN ? AND ? AND A.cancel = ?" ;
			$sql .=" AND A.user_id=? AND A.work_id = ? AND (A.cancel_reason = ' ' OR A.cancel_reason='' OR A.cancel_reason IS NULL)" ; 
			$sql .=" AND B.lesson_id=? " ; 
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1,$absent_2month_start, PDO::PARAM_STR);
			$stmt->bindValue(2,$absent_2month_end, PDO::PARAM_STR);
			$stmt->bindValue(3,$cancel_kind, PDO::PARAM_STR);
			$stmt->bindValue(4,$got_user_id, PDO::PARAM_INT);
			$stmt->bindValue(5,$got_work_id, PDO::PARAM_INT);
			$stmt->bindValue(6,$got_lesson_id, PDO::PARAM_INT);
			$stmt->execute();
			$absent_cnt = (int)$stmt->fetchColumn();
			$absent_threshold = $absent_threshold_monthly / 2;	// 月２回のとき２カ月に１回がリミット
		}
ob_start();echo "$absent_cnt,$absent_threshold.\n";$log = ob_get_contents(); ob_end_clean();
file_put_contents($logfile, $log, FILE_APPEND);
		$request_cancel_reason = '' ; 		// Initialization.
		if ($got_trial_id !== 0) {
					// 体験の休み
			$absent_id = 'a2';
			if ( $limitdate_timestamp < $current_timestamp ) {
				$request_cancel_reason = CANCEL_REASON2 ;
			}
		} else if ($got_altsched_id !== 0 ) {
					// 振替の休み
			$absent_id = 'a1';
			if ( $limitdate_timestamp > $current_timestamp ) {
				$request_cancel_reason = CANCEL_REASON5 ;
			} else {
				$request_cancel_reason = '';
			}
		} else if ($absent_cnt < $absent_threshold && $limitdate_timestamp > $current_timestamp ) {
					// 許容範囲の休みで事前連絡の場合
			if ($got_course_id == COURSE_GROUP || $got_course_id == COURSE_FAMILY || $got_lesson_id == 3/* PIANO */ ){
				$absent_id = 'a1';
			} else {
				if ($holiday_is_a1 == true || $rest_is_a1 == true || $first_rest_is_a1 == true ){
					$absent_id = 'a1';
				} else {
					$absent_id = 'a2';
				}
			}
			$request_cancel_reason = '' ;
		} else if ($absent_cnt >= $absent_threshold) {
								// 許容範囲を超えた
			if ($holiday_is_a1 == true || $rest_is_a1 == true ){
				$absent_id = 'a1';
				$request_cancel_reason = '' ;
			} else {
				$absent_id = 'a2';
				if ($rest_add_exchange_enable == true ){
					$request_cancel_reason = '';
				} else {
					$request_cancel_reason = CANCEL_REASON3 ;
				}
			}
		} else {
								//  当日。許容範囲は超えていない
			if ($holiday_is_a1 == true || $rest_is_a1 == true || $first_rest_is_a1 == true ){
				$absent_id = 'a1';
				$request_cancel_reason = '' ;
			} else {
				$absent_id = 'a2';
				$request_cancel_reason = CANCEL_REASON2 ;
			}
		}
updatedb_label:
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ?,altlimitdate=null WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$absent_id, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();

	} else if ($request_type==='special_cancel_reason') {
						 // processing for special cancel reason.
		$null_cancel_reason = '';
		$absent_id = 'a2';
		$request_cancel_reason = '' ;
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ?,altlimitdate=null WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$absent_id, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();

	} else if ($request_type==='rest_cancel') {
							// request for cancelation of the rest.
		if (($got_cancel ==='a2' && $got_cancel_reason === '') ||
		($got_cancel ==='a1' && $got_cancel_reason === '' && $first_rest_is_a1 == true ) ||
		( ($got_course_id == COURSE_GROUP || $got_course_id == COURSE_FAMILY || $got_lesson_id == 3/* PIANO */) && $got_cancel==='a1' && $got_cancel_reason==='')) {
			// 当該月に休み２規定回数以上が既に入力されていないかを調べる
		$cancel_kind = 'a2';
		$sql = "SELECT id FROM tbl_schedule_onetime WHERE delflag = 0 AND ymd BETWEEN ? AND ? AND cancel = ?"  
		." AND user_id = ? AND lecture_id = ? AND cancel_reason = ?" ; 
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$absent_month_start, PDO::PARAM_STR);
		$stmt->bindValue(2,$absent_month_end, PDO::PARAM_STR);
		$stmt->bindValue(3,$cancel_kind, PDO::PARAM_STR);
		$stmt->bindValue(4,$got_user_id, PDO::PARAM_INT);
		$stmt->bindValue(5,$got_lecture_id, PDO::PARAM_INT);
		$target_cancel_reason = CANCEL_REASON3;
		$stmt->bindValue(6,$target_cancel_reason, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$row_cnt = count($result);
		if ($row_cnt > 0) {
					//	最初の休み２規定回数以上のcancel_reasonの値を空白にする	
			foreach ($result as $row ) {
						// 最初の１行のcancel列を更新し、cancel_reasonを空白にする
						// 対象が複数あっても最初の１行のみ
				$sql = "UPDATE tbl_schedule_onetime SET cancel = ?,cancel_reason = '',altlimitdate=null WHERE id=?";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(1,$got_cancel, PDO::PARAM_STR);
				$stmt->bindValue(2,$row['id'], PDO::PARAM_INT);
				$stmt->execute();
				break;		 // foreach
				}
			}
		}
		$request_cancel = ''; 			// reset the field
		$request_cancel_reason = ''; 		// reset the field
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ?,altlimitdate=null WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();
		$absent_id = null;
			
	} else if ($request_type==='lecture_cancel') {
									// 休講
		$request_cancel = 'a1'; 			// 休み１
		$request_cancel_reason = CANCEL_REASON4; 		// 休講
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ? ,altlimitdate=null WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();
		$absent_id = 'a1';

	} else if ($request_type==='absence') {
			// 出欠確認での欠席
		$alternate = 'false' ;
			// 生徒か事務員かで'calcel'列および'cancel_reason'列、Confirm列の値が変わる
		if ($got_user_id > 100000 ) { // Staff or teacher.
			$request_cancel = 'a';
			$request_cancel_reason = '';
			$request_confirm = 'a';
			$absent_id = 'a';
		} else {
			$request_cancel = 'a2';
			$request_cancel_reason = CANCEL_REASON2;
			$request_confirm = 'a2';
			$absent_id = 'a2';
		}
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ? ,cancel_reason = ?, confirm=?  WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_confirm, PDO::PARAM_STR);
		$stmt->bindValue(4,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='presence') {
				// 出欠確認での出席
		$sql = "UPDATE tbl_schedule_onetime SET cancel='',confirm = ? ,cancel_reason=?,altlimitdate=null WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_confirm = 'f'; 
		$request_cancel_reason = CANCEL_NOREASON; 
		$stmt->bindValue(1,$request_confirm, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_cancel_reason, PDO::PARAM_STR);
		$stmt->bindValue(3,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='cancel') {
				// 予定の取り消し
		$sql = "UPDATE tbl_schedule_onetime SET cancel = ?,altlimitdate=null WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_cancel = 'c'; 
		$stmt->bindValue(1,$request_cancel, PDO::PARAM_STR);
		$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='confirm') {
			// 仮スケジュールの先生確認状態
		$sql = "UPDATE tbl_schedule_onetime SET temporary = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_temporary = 10; 
		$stmt->bindValue(1,$request_temporary, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
		$stmt->execute();
	} else if ($request_type==='fix') {
			// 仮スケジュールの確定
		$sql = "UPDATE tbl_schedule_onetime SET temporary = ? WHERE id = ?";
		$stmt = $dbh->prepare($sql);
		$request_temporary = 110; 
		$stmt->bindValue(1,$request_temporary, PDO::PARAM_INT);
		$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
		$stmt->execute();
 
	} 


	switch ($got_course_id){
	case '4':	// Summer seminar.
	case '5':	// Winter seminar.
	case '6':	// Spring seminar.
	case '9':	// Weekend seminar.
			// Do nothing.
			break;

	default:
				// not season seminar.
										// check how many lessons in a week .
		$sql ="SELECT ymd,cancel,cancel_reason " ;
		$sql .=" FROM tbl_schedule_onetime WHERE id = ? " ;
		$stmt = $dbh->prepare($sql);
		$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
		$stmt->execute();
		$rslt = $stmt->fetch(PDO::FETCH_ASSOC);
					// the data is determined uniquely.
		$got_ymd = $rslt['ymd'];
		$got_cancel = $rslt['cancel'];
		$got_cancel_reason = $rslt['cancel_reason'];
		$limitdate = null;
		$next2month = '+2 month';
        	$dateObj = new DateTime($got_ymd);
		$dateObj->add(DateInterval::createFromDateString($next2month));
        	$alt_limit_ts = $dateObj -> getTimestamp();
        	$alt_limit = getdate($alt_limit_ts);
        	$alt_limitdate = $alt_limit['year'].'-'.$alt_limit['mon'].'-'.$alt_limit['mday'];

		$alternate = 'false' ; // Initialization.
		if ($got_cancel==='a2' && $got_cancel_reason != CANCEL_REASON2 && $got_cancel_reason != CANCEL_REASON3 ){
			$alternate = 'true';
               		$limitdate = $alt_limitdate;
			$sql = "UPDATE tbl_schedule_onetime SET altlimitdate = ? WHERE id = ?";
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1,$limitdate, PDO::PARAM_STR);
			$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
			$stmt->execute();
		}
		if ($got_cancel==='a1' && $got_cancel_reason === CANCEL_REASON5 ){
				// もう一回振替可能
			$alternate = 'true';
               		$limitdate = $alt_limitdate;
			$sql = "UPDATE tbl_schedule_onetime SET altlimitdate = ? WHERE id = ?";
			$stmt = $dbh->prepare($sql);
			$stmt->bindValue(1,$limitdate, PDO::PARAM_STR);
			$stmt->bindValue(2,$request_id, PDO::PARAM_INT);
			$stmt->execute();
		}
		break;
	}

normal_label:		
	$sql = "INSERT INTO tbl_schedule_onetime_history (".
       	"id,".
       	"repetition_id,".
       	"user_id,".
       	"teacher_id,".
       	"student_no,".
       	"ymd,". 
       	"starttime,".
       	"endtime,".
       	"lecture_id,".
       	"group_lesson_id,".
       	"subject_expr,".
       	"work_id,".
       	"free,".
       	"cancel,".
       	"cancel_reason,".
       	"alternate,".
       	"altsched_id,".
       	"altlimitdate,".
       	"trial_id,".
       	"repeattimes,".
       	"place_id,".
       	"temporary,".
       	"recess,".
       	"confirm,".
       	"additional,".
       	"updatetime,".
       	"updateuser,".
       	"comment) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )"; 

	$stmt = $dbh->prepare($sql);
	$stmt->bindValue(1,$request_id, PDO::PARAM_INT);
	$stmt->bindValue(2,$request_repetition_id, PDO::PARAM_INT);
	$stmt->bindValue(3,$request_user_id, PDO::PARAM_INT);
	$stmt->bindValue(4,$request_teacher_id, PDO::PARAM_INT);
	$stmt->bindValue(5,$request_student_no, PDO::PARAM_INT);
	$stmt->bindValue(6,$request_ymd, PDO::PARAM_STR);
	$stmt->bindValue(7,$request_starttime, PDO::PARAM_STR);
	$stmt->bindValue(8,$request_endtime, PDO::PARAM_STR);
	$stmt->bindValue(9,$request_lecture_id, PDO::PARAM_STR);
	$stmt->bindValue(10,$request_group_lesson_id, PDO::PARAM_STR);
	$stmt->bindValue(11,$request_subject_expr, PDO::PARAM_STR);
	$stmt->bindValue(12,$request_work_id, PDO::PARAM_INT);
	$stmt->bindValue(13,$request_free, PDO::PARAM_STR);
	$stmt->bindValue(14,$request_cancel, PDO::PARAM_STR);
	$stmt->bindValue(15,$request_cancel_reason, PDO::PARAM_STR);
	$stmt->bindValue(16,$request_alternate, PDO::PARAM_STR);
	$stmt->bindValue(17,$request_altsched_id, PDO::PARAM_INT);
	$stmt->bindValue(18,$limitdate, PDO::PARAM_STR);
	$stmt->bindValue(19,$request_trial_id, PDO::PARAM_STR);
	$stmt->bindValue(20,$request_repeattimes, PDO::PARAM_INT);
	$stmt->bindValue(21,$request_place_id, PDO::PARAM_INT);
	$stmt->bindValue(22,$request_temporary, PDO::PARAM_INT);
	$stmt->bindValue(23,$request_recess, PDO::PARAM_STR);
	$stmt->bindValue(24,$request_confirm, PDO::PARAM_STR);
	$stmt->bindValue(25,$request_additional, PDO::PARAM_STR);
	$stmt->bindValue(26,$now, PDO::PARAM_STR);
	$stmt->bindValue(27,$request_updateuser, PDO::PARAM_INT);
	$stmt->bindValue(28,$request_comment, PDO::PARAM_STR);
	$stmt->execute();

				// 更新イメージの取得
afterupdate_label:
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
               "altlimitdate,".
               "trial_id,".
               "place_id,".
               "temporary,".
               "confirm,".
               "entrytime,".
               "updatetime,".
               "updateuser,".
	       	"delflag,".
	       	"comment".
              " FROM tbl_schedule_onetime WHERE id='$request_id'";

	        $stmt = $dbh->query($sql);
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$response = array_merge($result[0],array('alternate'=>$alternate));

		$res = array(
			'status'=>'0',
			'cancel'=>$absent_id,
			'data'=>$response
			);
exit_label:
		// exit the program.


} catch (Exception $e) {
	
	$res = array(
		'status'=>'error',
		'cancel'=>$absent_id, 
		);

}

function get_restcare_inf(&$dbc,$got_user_id,$got_ymd){
		// this function restcare inf from common.user_tags.
		// $restcare_inf:'none','a1always','exchangeenable','a1onholiday'

				// initialization.
$holiday_is_a1 = false;
$rest_is_a1 = false;
$first_rest_is_a1 = false;
$rest_add_exchange_enable = false;
try {
	$sql = "SELECT user_id from user_tags WHERE tag_key='student_no' AND tag_value=?";
	$stmt = $dbc->prepare($sql);
	$stmt->bindValue(1,$got_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$lms_user_id = $stmt->fetch(PDO::FETCH_COLUMN);
	$sql = "SELECT tag_value from user_tags WHERE user_id=? AND tag_key='student_type'";
	$stmt = $dbc->prepare($sql);
	$stmt->bindValue(1,$lms_user_id, PDO::PARAM_INT);
	$stmt->execute();
	$student_type_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($student_type_array as $student_type) {

		$got_student_type = $student_type['tag_value'];
		if (mb_strpos($got_student_type,'restcare_holiday_is_a1') !== FALSE ){
			$sql = "SELECT is_public_holiday from holidays WHERE date=? ";
			$stmt = $dbc->prepare($sql);
			$stmt->bindValue(1,$got_ymd, PDO::PARAM_STR);
			$stmt->execute();
			$holiday_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($holiday_array as $holiday_row) {
				if($holiday_row['is_public_holiday'] == 1){
					$holiday_is_a1 = true;
				}
			}
		} else if (mb_strpos($got_student_type,'restcare_rest_is_a1')!== FALSE ){
			$rest_is_a1 = true;
		} else if (mb_strpos($got_student_type,'restcare_first_rest_is_a1')!== FALSE ){
			$first_rest_is_a1 = true;
		} else if (mb_strpos($got_student_type,'restcare_rest_add_exchange_enable')!== FALSE ){
			$rest_add_exchange_enable = true;
		} 	// end of switch.
	}
	$restcare_inf = array($holiday_is_a1,$rest_is_a1,$first_rest_is_a1,$rest_add_exchange_enable);
exit_label:
return($restcare_inf);
}catch (PDOException $e){
	return('DB Access Error.');
}

}

header("Content-Type: application/json; charset=utf-8");

//var_dump($token);

$log = json_encode($res);
echo $log;
file_put_contents($logfile, "$log\n", FILE_APPEND);

?>
