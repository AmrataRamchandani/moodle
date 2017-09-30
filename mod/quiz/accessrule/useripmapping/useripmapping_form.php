<?php


defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';


class quizaccess_upload_useripmapping_list extends moodleform {

	
	function definition () {
		
		$mform = $this->_form;
				
		$mform->addElement('filepicker', 'file', get_string('file','quizaccess_useripmapping'));
		$mform->addRule('file', null, 'required');
		
		$choices = csv_import_reader::get_delimiter_list();
		$mform->addElement('select', 'csvdelimiter', get_string('csvdelimiter', 'quizaccess_useripmapping'), $choices);
		if (array_key_exists('cfg', $choices)) {
			$mform->setDefault('csvdelimiter', 'cfg');
		} else if (get_string('listsep', 'langconfig') == ';') {
			$mform->setDefault('csvdelimiter', 'semicolon');
		} else {
			$mform->setDefault('csvdelimiter', 'comma');
		}
		
		$choices = core_text::get_encodings();
		$mform->addElement('select', 'encoding', get_string('encoding', 'tool_uploaduser'), $choices);
		$mform->setDefault('encoding', 'UTF-8');
		
		$choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
		$mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'tool_uploaduser'), $choices);
		$mform->setType('previewrows', PARAM_INT);
		
		$mform->addElement('hidden', 'quizid');
		
		$this->add_action_buttons(true, get_string('uploadmappings', 'quizaccess_useripmapping'));
		
				
	}
}


class quizaccess_view_edit_useripmapping_list extends moodleform {
	
	
	function definition () {
		
		$mform = $this->_form;
		
		$mform->addElement('header', 'viewheader', get_string('viewheader','quizaccess_useripmapping'));
		
		$mform->addElement('text', 'username', get_string('username','quizaccess_useripmapping'));				
// 		$mform->addRule('quizid', null, 'required');
		
		$mform->addElement('text', 'idnumber', get_string('idnumber','quizaccess_useripmapping'));
// 		$mform->addRule('quizid', null, 'required');
		
		$mform->addElement('hidden', 'quizid');
		
 		$this->add_action_buttons(true, get_string('viewthelist', 'quizaccess_useripmapping'));
		
		
	}
}

class quizaccess_add_useripmapping extends moodleform {
	
	
	function definition () {
		
		$mform = $this->_form;
		$this->add_action_buttons(false, get_string('addip', 'quizaccess_useripmapping'));
		
		
	}
}

class quizaccess_edit_useripmapping_list extends moodleform {
	
	
	function definition () {
		
		$mform = $this->_form;
		
		$mform->addElement('header', 'editheader', 'Edit Mappings');
		
		$mform->addElement('text', 'userid','UserID');
		$mform->addRule('userid', null, 'required');
		$mform->setDefault('userid', $userid);
		
		$mform->addElement('text', 'ip','IP');
		$mform->addRule('ip', null, 'required');
		$mform->setDefault('ip',$ip);
		
		$this->add_action_buttons(true, 'Save');
		
		
	}
}