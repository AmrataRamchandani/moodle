
<?php

require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/accessrule/useripmapping/useripmapping_form.php');
require_once($CFG->libdir.'/csvlib.class.php');


global $CFG, $PAGE;


$iid         = optional_param('iid', '', PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);
$quizid = optional_param('quizid',0,PARAM_INT);
$courseid = optional_param('courseid',0,PARAM_INT);

core_php_time_limit::raise(60*60); // 1 hour should be enough
raise_memory_limit(MEMORY_HUGE);

require_login();

$returnurl = new moodle_url('/mod/quiz/accessrule/useripmapping/test.php');
// $cm = get_coursemodule_from_instance("quiz", $quizid, $courseid);
// echo $cm;
// echo "<br><br><br>";
// print_object($cm);
$PAGE->set_context(context_system::instance());
//$PAGE->navbar->add('SIP Mappings');
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Upload SIP Mapping List');
$PAGE->set_heading('Upload Student IP Mapping List');
$PAGE->set_url($CFG->wwwroot.'/mod/quiz/accessrule/useripmapping/test.php');

echo $OUTPUT->header();

// array of all valid fields for validation
$STD_FIELDS = array('userid', 'ip');


if (empty($iid)) {
	$mform = new quizaccess_upload_useripmapping_list();
	$quizdata = array ('quizid' => $quizid);
	$mform->set_data($quizdata);
	$iid = csv_import_reader::get_new_iid('uploadmappings');
	$cir = new csv_import_reader($iid, 'uploadmappings');
	
	
	
	if ($formdata = $mform->is_cancelled()) {
		$cir->cleanup(true);
		redirect($returnurl);
		
	} elseif ($formdata = $mform->get_data()) {
			
		//In this case you process validated data. $mform->get_data() returns data posted in form.
		$content = $mform->get_file_content('file');
		
		$readcount = $cir->load_csv_content($content, $formdata->encoding, $formdata->delimiter_name);
		
		$csvloaderror = $cir->get_error();
		unset($content);
		
		if (!is_null($csvloaderror)) {
			print_error('csvloaderror', '', $rreturnurl, $csvloaderror);
		}
		// test if columns ok
		$filecolumns = uu_validate_user_upload_columns($cir, $STD_FIELDS, $returnurl);
		
		if($filecolumns)
		{
			// NOTE: this is JUST csv processing preview, we must not prevent import from here if there is something in the file!!
			//       this was intended for validation of csv formatting and encoding, not filtering the data!!!!
			//       we definitely must not process the whole file!
			
			// preview table data
			echo $OUTPUT->heading(get_string('uploaduseripmappingresult', 'quizaccess_useripmapping'));
			
			$data = array();
			$cir->init();
		$linenum = 1; //column header is first line'
			while ($linenum <= $previewrows and $fields = $cir->next()) {
 			
$checkifuserisregistered=$DB->record_exists('user', array ('id' => $fields[0]));
//$checkiftherecordexists=$DB->record_exists('user_ip_mapping', array('quizid'=> $quizid,'userid' => $fields[0],'ip'=> $fields[1]) );
 if( $checkifuserisregistered)
 				{
					$rowcols = array();
					$record = new stdClass();
					$rowcols[srno]=$linenum;
					$rowcols[quizid]=$quizid;
					$record->quizid=$quizid;
					
					foreach($fields as $key => $field) {
						$rowcols[$filecolumns[$key]] = s(trim($field));
						$colname=$filecolumns[$key];
						$record->$colname=trim($field);
					}
					
					$data[] = $rowcols;

					$DB->insert_record('user_ip_mapping',$record, $returnid=false, $bulk=false);
				}
				else {
					continue;
				}
				
				$linenum++;
			}
			if ($fields = $cir->next()) {
				$data[] = array_fill(0, count($fields) + 2, '...');
			}
			$cir->close();
			
			$table = new html_table();
			$table->id = "useriplistpreview";
			$table->attributes['class'] = 'generaltable';
			$table->tablealign = 'center';
			$table->summary = get_string('uploaduseripmappingpreview', 'quizaccess_useripmapping');
			$table->head = array();
			$table->data = $data;
			$table->head = array('Sr. No','Quiz ID','User ID', 'IP Address');
// 			$table->head[]=('Sr. No');
// 			$table->head[]=('Quiz ID');
// 			foreach ($filecolumns as $column) {
// 				$table->head[] = $column;
// 			}
			
			
			echo html_writer::tag('div', html_writer::table($table), array('class'=>'flexible-wrap'));
			
			echo $OUTPUT->continue_button($returnurl);
			
			
			
			
			
		}
		

	} else {
	// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
	// or on the first display of the form.

	$mform->display();
}

}else {
	$cir = new csv_import_reader($iid, 'uploadmappings');
	$filecolumns = uu_validate_user_upload_columns($cir, $STD_FIELDS, $PRF_FIELDS, $returnurl);
}





/**
 * Validation callback function - verified the column line of csv file.
 * Converts standard column names to lowercase.
 * @param csv_import_reader $cir
 * @param array $stdfields standard user fields
 * @param array $profilefields custom profile fields
 * @param moodle_url $returnurl return url in case of any error
 * @return array list of fields
 */
function uu_validate_user_upload_columns(csv_import_reader $cir, $stdfields, moodle_url $returnurl) {
	$columns = $cir->get_columns();
	
	if (empty($columns)) {
		$cir->close();
		$cir->cleanup();
		print_error('cannotreadtmpfile', 'error', $returnurl);
	}
	if (count($columns) < 2) {
		$cir->close();
		$cir->cleanup();
		print_error('csvfewcolumns', 'error', $returnurl);
	}
	
	// test columns
	$processed = array();
	foreach ($columns as $key=>$unused) {
		$field = $columns[$key];
		$lcfield = core_text::strtolower($field);
		if (in_array($field, $stdfields) or in_array($lcfield, $stdfields)) {
			// standard fields are only lowercase
			$newfield = $lcfield;
			
		} else if (preg_match('/^(sysrole|cohort|course|group|type|role|enrolperiod|enrolstatus)\d+$/', $lcfield)) {
			// special fields for enrolments
			$newfield = $lcfield;
			
		} else {
			$cir->close();
			$cir->cleanup();
			print_error('invalidfieldname', 'error', $returnurl, $field);
		}
		if (in_array($newfield, $processed)) {
			$cir->close();
			$cir->cleanup();
			print_error('duplicatefieldname', 'error', $returnurl, $newfield);
		}
		$processed[$key] = $newfield;
	}
	
	return $processed;
}







echo $OUTPUT->footer();

?>
