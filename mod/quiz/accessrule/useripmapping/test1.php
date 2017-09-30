<?php

require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/accessrule/useripmapping/useripmapping_form.php');

global $CFG, $PAGE ,$DB,$OUTPUT;

$quizid = optional_param('quizid',0,PARAM_INT);

require_login();

$returnurl = new moodle_url('/mod/quiz/accessrule/useripmapping/test1.php',array ('quizid' =>$quizid));

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('SIP Mapping List');
$PAGE->set_heading('View and Edit Student IP Mapping List');
$PAGE->set_url($CFG->wwwroot.'/mod/quiz/accessrule/useripmapping/test1.php');
echo $OUTPUT->header();

$mform = new quizaccess_view_edit_useripmapping_list();
$quizdata = array ('quizid' => $quizid);
$mform->set_data($quizdata);
$mform->display();

if ($formdata = $mform->is_cancelled()) {	
	redirect($returnurl);	
} elseif ($data = $mform->get_data()) {

if(empty($data->username) || empty($data->idnumber))
{
$sql1="select id from mdl_user where firstname LIKE '%".$data->username."%' OR idnumber LIKE '%".$data->idnumber."%' " ;
}
else
{
$sql1="select id from mdl_user where firstname LIKE '%".$data->username."%' AND idnumber LIKE '%".$data->idnumber."%' " ;
}
$userid=$DB->get_fieldset_sql($sql1);
//$DB->get_fieldset_select('user', 'id', $select, array($data->username,$data->idnumber));
	
$timestampsql= "SELECT userid, MAX(timestamp) FROM mdl_user_ip_mapping group by userid having userid IN (".implode(',',$userid).")";
$timestamp=$DB->get_records_sql_menu($timestampsql);

$table = new html_table();
echo "<p>To Edit,Click on IP Address field.</p>";
$table->head = array('ID','Quiz ID','User ID','Roll Number', 'User Name','TimeStamp','IP Address');

foreach ($timestamp as $key => $value) {
$sql="SELECT * FROM mdl_user_ip_mapping WHERE quizid=$quizid AND userid=$key AND timestamp='$value'";

$result = $DB->get_records_sql($sql);

foreach ($result as $record) {
		
		$ip = $record->ip;
		$quizid = $record->quizid;
		$userid=$record->userid;
		$studentname = $DB->get_field('user','firstname', array('id'=>$userid));
 		$studentid= $DB->get_field('user','idnumber', array('id'=>$userid));
		$rowid=$record->id;
		$timestamp=$record->timestamp;
		$row = new html_table_row();
		$cell0 = new html_table_cell($rowid);
		$cell1 = new html_table_cell($quizid);
		$cell2 = new html_table_cell($userid);
		$cell3 = new html_table_cell($studentid);
		$cell4 = new html_table_cell($studentname);		
		$cell5 = new html_table_cell($timestamp);
		$cell6 = new html_table_cell($ip);
		$cell6->attributes['contenteditable']='true';
		$cell6->id=$rowid;
		$cell6->text=$ip." ".$OUTPUT->pix_icon('i/edit', 'To Edit IP');
		$row->cells[] = $cell0;
		$row->cells[] = $cell1;
		$row->cells[] = $cell2;
		$row->cells[] = $cell3;
		$row->cells[] = $cell4;		
		$row->cells[] = $cell5;	
		$row->cells[] = $cell6;	
		$table->data[] = $row;	
	}
}
echo html_writer::table($table);
}
echo $OUTPUT->footer();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js" type="text/javascript"></script>

<script>
jQuery(document).ready(function($){



 $('td').focus(function(e) {
$(this).find('.fa-pencil').hide();
});

	$('td').keydown(function (e) {

 if (e.keyCode == 13 || e.keyCode == 9){
	var id= $(this).attr("id") ;
	var value=$(this).text() ;

   var url = "ajax.php"; 
    $.ajax({
           type: "POST",
           url: url,
           data: {
               recordid : id,
	       ip : value
           }              
         });

$(this).blur();  
  $('.fa-pencil').show();
}
});
});

</script>




