<?php

require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/accessrule/useripmapping/useripmapping_form.php');

global $CFG, $PAGE ,$DB;

$quizid = optional_param('quizid',0,PARAM_INT);
$userid = optional_param('userid',0,PARAM_INT);


require_login();

$returnurl = new moodle_url('/mod/quiz/accessrule/useripmapping/test1.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Edit Mapping');
$PAGE->set_heading('Edit Student IP Mapping');
$PAGE->set_url($CFG->wwwroot.'/mod/quiz/accessrule/useripmapping/editmapping.php');
echo $OUTPUT->header();

$mform = new quizaccess_edit_useripmapping_list();
$arrayQU=array('quizid' => $quizid ,'userid' => $userid);
$ip = $DB->get_field('user_ip_mapping','ip', $arrayQU );
$id = $DB->get_field('user_ip_mapping','id', $arrayQU );
$setDataArray =array('userid'=>$userid,'ip'=>$ip);
$mform->set_data($setDataArray);

$mform->display();

if ($formdata = $mform->is_cancelled()) {
	redirect($returnurl);
} elseif ($data = $mform->get_data()) {		
	$rowcols = array();
	$record = new stdClass();	
	$record->id=$id;
	$record->quizid=$quizid;
	$record->userid=$userid;
	$record->ip=$ip;
	$result=$DB->update_record('user_ip_mapping',$record, $returnid=false, $bulk=false);
	
	if($result)
		echo "Mapping Updated Successfully";
	else
		echo "Mapping Updation Failed";

	
		
}




echo $OUTPUT->footer();
?>
