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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Implementaton of the quizaccess_useripmapping plugin.
 *
 * @package   quizaccess_useripmapping
 * @author    Amrata Ramchandani <ramchandani.amrata@gmail.com>
 * @copyright 2017 Indian Institute Of Technology,Bombay,India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');

global $DB;

/**
 * A rule implementing the user ip mapping check in order to restrict user to attempt
 * quiz from mapped/assigned IP Address
 *
 * @author    Amrata Ramchandani <ramchandani.amrata@gmail.com>
 * @copyright 2017 Indian Institute Of Technology,Bombay,India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_useripmapping extends quiz_access_rule_base
{
    public static function make(quiz $quizobj, $timenow, $canignoretimelimits)
    {
        if (empty($quizobj->get_quiz()->useripmappingrequired)) {
            return null;
        }
        return new self($quizobj, $timenow);
    }
    public function prevent_new_attempt($numprevattempts, $lastattempt)
    {
        $username = $_SESSION['USER']->username;
        global $DB;
        $quizid            = $this->quiz->id;
        $sql1              = "SELECT allowifunassigned FROM mdl_quizaccess_enable_mappings WHERE quizid=$quizid";
        $allowifunassigned = $DB->get_field_sql($sql1);
        $sql               = "SELECT ip FROM mdl_quizaccess_useripmappings WHERE username='$username' and quizid=$quizid order by timecreated DESC limit 1";
        $mapped_ip_address = $DB->get_field_sql($sql);
        $remoteaddr        = getremoteaddr();
        if ($allowifunassigned) {
            if (empty($mapped_ip_address))
                return false;
                elseif (address_in_subnet(getremoteaddr(), $mapped_ip_address)) {
                    return false;
                } else {
                    return "You are being assigned :<b> $mapped_ip_address </b> IP Address to attempt the quiz and this computer's
IP address does not match with the assigned one.";
                }
        } else {
            if (address_in_subnet(getremoteaddr(), $mapped_ip_address)) {
                return false;
            } else {
                return "You have not been assigned any IP address to attempt this quiz,please contact your instructor
to get it assigned in order to attempt this quiz.";
            }
        }
    }
    public static function add_settings_form_fields(mod_quiz_mod_form $quizform, MoodleQuickForm $mform)
    {
        $buttonarray   = array();
        $buttonarray[] = $mform->createElement('select', 'useripmappingrequired', get_string('useripmappingrequired', 'quizaccess_useripmapping'), array(
            0 => get_string('notrequired', 'quizaccess_useripmapping'),
            1 => get_string('useripmappingrequiredoption', 'quizaccess_useripmapping')
        ));
        $buttonarray[] = $mform->createElement('advcheckbox', 'allowifunassigned', '', 'Allow Unmapped', '', array(
            0,
            1
        ));
        $mform->disabledIf('allowifunassigned', 'useripmappingrequired', 'neq', 1);
        $quizid = $quizform->get_instance();
        if (!empty($quizid)) {
            $manageip           = new moodle_url("/mod/quiz/accessrule/useripmapping/managemappings.php", array(
                'quizid' => $quizid,
                'courseid' => $quizform->get_course()->id,
                'cmid' => $quizform->get_coursemodule()->id
            ));
            $hyperlink_manageip = "&nbsp;&nbsp;&nbsp;&nbsp;<a href=$manageip>Manage Student-IP Mappings</a>";
            $buttonarray[]      = $mform->createElement('static', 'manageiplist', '', $hyperlink_manageip);
            $mform->addGroup($buttonarray, 'buttonar', get_string('useripmappingrequiredupdate', 'quizaccess_useripmapping'), array(
                ' '
            ), false);
            $mform->addHelpButton('buttonar', 'useripmappingrequiredupdate', 'quizaccess_useripmapping');
        } else {
            $mform->addGroup($buttonarray, 'buttonar', get_string('useripmappingrequiredadd', 'quizaccess_useripmapping'), array(
                ' '
            ), false);
            $mform->addHelpButton('buttonar', 'useripmappingrequiredadd', 'quizaccess_useripmapping');
        }
    }
    public static function save_settings($quiz)
    {
        global $DB;
        if (empty($quiz->useripmappingrequired)) {
            $DB->delete_records('quizaccess_enable_mappings', array(
                'quizid' => $quiz->id
            ));
        } else {
            if (!$DB->record_exists('quizaccess_enable_mappings', array(
                'quizid' => $quiz->id
            ))) {
                $record                        = new stdClass();
                $record->quizid                = $quiz->id;
                $record->useripmappingrequired = 1;
                $record->allowifunassigned     = $quiz->allowifunassigned;
                $DB->insert_record('quizaccess_enable_mappings', $record);
            } else {
                $select                    = "quizid=$quiz->id";
                $id                        = $DB->get_field_select('quizaccess_enable_mappings', 'id', $select);
                $record                    = new stdClass();
                $record->id                = $id;
                $record->allowifunassigned = $quiz->allowifunassigned;
                $DB->update_record('quizaccess_enable_mappings', $record);
            }
        }
    }
    public static function delete_settings($quiz)
    {
        global $DB;
        $DB->delete_records('quizaccess_enable_mappings', array(
            'quizid' => $quiz->id
        ));
    }
    public static function get_settings_sql($quizid)
    {
        return array(
            'useripmappingrequired',
            'LEFT JOIN {quizaccess_enable_mappings} enable_mappings ON enable_mappings.quizid = quiz.id',
            array()
        );
    }
}