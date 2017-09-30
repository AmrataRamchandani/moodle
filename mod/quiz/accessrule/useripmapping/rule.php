<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Implementaton of the quizaccess_useripmapping plugin.
 *
 * @package    quizaccess
 * @subpackage useripmapping
 * @copyright  2017 Indian Institute Of Technology,Bombay,India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
 * A rule implementing the user ip mapping check in order to restrict user to attempt
 * quiz from mapped/assigned IP Address
 *
 * @copyright  2017 Indian Institute Of Technology,Bombay,India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_useripmapping extends quiz_access_rule_base {

    public static function make(quiz $quizobj, $timenow, $canignoretimelimits) {
    	if (empty($quizobj->get_quiz()->useripmappingrequired)) {
    		return null;
    	}
    	
    	return new self($quizobj, $timenow);
    }

    public function prevent_new_attempt($numprevattempts, $lastattempt) {
    	global $DB;
    	
    	$userid = $_SESSION[USER]->id;  

    	//$mapped_ip_address = $DB->get_field('user_ip_mapping', 'ip', array('userid'=>$userid));

//$timestampsql= "SELECT MAX(timestamp) FROM mdl_user_ip_mapping WHERE userid=$userid ";
//$timestamp=$DB->get_fieldset_sql($timestampsql);
//$latesttimestamp=$timestamp[0];
//$sql="SELECT ip FROM mdl_user_ip_mapping WHERE userid=$userid AND timestamp='$latesttimestamp'";

$sql="SELECT ip FROM mdl_user_ip_mapping WHERE userid=$userid order by timestamp DESC limit 1";
$mapped_ip_address =$DB->get_field_sql($sql);	
echo "<br><br><br>";
echo "$mapped_ip_address";
    	$remoteaddr = getremoteaddr();
    	if (address_in_subnet(getremoteaddr(), $mapped_ip_address)) {
            return false;
        } else {
            return get_string('subnetwrong', 'quizaccess_useripmapping');
        }
    }
    
    public static function add_settings_form_fields(
    		mod_quiz_mod_form $quizform, MoodleQuickForm $mform) {
    
    		print_object($quizform->get_course()->id);
    			$options = array();
    			$mform->addElement('select', 'useripmappingrequired',
    					get_string('useripmappingrequired', 'quizaccess_useripmapping'),
    					array(
    							0 => get_string('notrequired', 'quizaccess_useripmapping'),
    							1 => get_string('useripmappingrequiredoption', 'quizaccess_useripmapping'),
    							
    					));
    			
    			$mform->addHelpButton('useripmappingrequired',
    					'useripmappingrequired', 'quizaccess_useripmapping');
    			$testurl = new moodle_url("/mod/quiz/accessrule/useripmapping/test.php", 
    					array ('quizid' => $quizform->get_coursemodule()->id ,
    							'courseid'=> $quizform->get_course()->id
    					));
    			$test1url = new moodle_url("/mod/quiz/accessrule/useripmapping/test1.php",
    					array ('quizid' => $quizform->get_coursemodule()->id ));
    			$hyperlink_testurl="<a href=$testurl>Click Here</a>";
				$hyperlink_test1url="<a href=$test1url>Click Here</a>";
    			$mform->addElement('static', 'useriplist', get_string('useriplist', 'quizaccess_useripmapping'),
    					$hyperlink_testurl);
    			$mform->addElement('static', 'viewuseriplist', get_string('viewuseriplist', 'quizaccess_useripmapping'),
    					$hyperlink_test1url);
    		
    }
    
    public static function save_settings($quiz) {
    	global $DB;
    	if (empty($quiz->useripmappingrequired)) {
    		$DB->delete_records('quizaccess_useripmapping', array('quizid' => $quiz->id));
    	} else {
    		if (!$DB->record_exists('quizaccess_useripmapping', array('quizid' => $quiz->id))) {
    			$record = new stdClass();
    			$record->quizid = $quiz->id;
    			$record->useripmappingrequired = 1;
    			$DB->insert_record('quizaccess_useripmapping', $record);
    		}
    	}
    }
    
    
    public static function delete_settings($quiz) {
    	global $DB;
    	$DB->delete_records('quizaccess_useripmapping', array('quizid' => $quiz->id));
    }
    
    public static function get_settings_sql($quizid) {
    	return array(
    			'useripmappingrequired',
    			'LEFT JOIN {quizaccess_useripmapping} useripmapping ON useripmapping.quizid = quiz.id',
    			array());
    }
    
    
}
