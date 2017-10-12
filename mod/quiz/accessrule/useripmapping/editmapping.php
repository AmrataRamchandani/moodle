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
 * Edits the mapped IP Address for the quizaccess_useripmapping plugin.
 *
 * @package    quizaccess_useripmapping
 * @author     Amrata Ramchandani <ramchandani.amrata@gmail.com>
 * @copyright  2017 Indian Institute Of Technology,Bombay,India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../../config.php');
require_once($CFG->dirroot . '/mod/quiz/accessrule/useripmapping/useripmapping_form.php');

global $CFG, $PAGE, $DB, $OUTPUT;

$quizid       = required_param('quizid', PARAM_INT);
$courseid     = required_param('courseid', PARAM_INT);
$cmid         = required_param('cmid', PARAM_INT);
$coursemodule = get_coursemodule_from_id('quiz', $cmid);
$context      = context_module::instance($cmid);

require_login($courseid, false, $coursemodule);
require_capability('mod/quiz:manage', $context);

$returntomanageurl = new moodle_url('/mod/quiz/accessrule/useripmapping/managemappings.php', array(
    'quizid' => $quizid,
    'courseid' => $courseid,
    'cmid' => $cmid
));

$returnurl         = new moodle_url('/mod/quiz/accessrule/useripmapping/editmapping.php', array(
    'quizid' => $quizid,
    'courseid' => $courseid,
    'cmid' => $cmid
));

$PAGE->requires->jquery();
$PAGE->set_title('Edit Student IP Mappings');
$PAGE->set_heading('Edit Student IP Mappings');
$PAGE->set_url($CFG->wwwroot . '/mod/quiz/accessrule/useripmapping/editmapping.php', array(
    'quizid' => $quizid,
    'courseid' => $courseid,
    'cmid' => $cmid
));
$PAGE->navbar->add("Edit Settings", new moodle_url('/course/modedit.php',array ('update'=>$cmid,'return'=>1)));
$PAGE->navbar->add("Manage Student-IP Mappings", $returntomanageurl);
$PAGE->navbar->add("Edit Mappings", $returnurl);

echo $OUTPUT->header();

$form             = new quizaccess_edit_useripmapping_list();
$quizdata         = array(
    'quizid' => $quizid
);
$coursedata       = array(
    'courseid' => $courseid
);
$coursemoduledata = array(
    'cmid' => $cmid
);
$form->set_data($quizdata);
$form->set_data($coursedata);
$form->set_data($coursemoduledata);
$form->display();

if ($formdata = $form->is_cancelled()) {
    redirect($returntomanageurl);
} elseif ($data = $form->get_data()) {
    if (empty($data->idnumber) && !empty($data->username)) {
        $sql1 = "select username from mdl_user where CONCAT_WS(' ', firstname, lastname)  LIKE '%" . $data->username . "%'  ";
    } elseif (empty($data->username) && !empty($data->idnumber)) {
        $sql1 = "select username from mdl_user where idnumber LIKE '%" . $data->idnumber . "%' ";
    } elseif (!empty($data->username) && !empty($data->idnumber)) {
        $sql1 = "select username from mdl_user where CONCAT_WS(' ', firstname, lastname)  LIKE '%" . $data->username . "%' AND idnumber LIKE '%" . $data->idnumber . "%' ";
    } else {
        echo "Please enter either username or roll number";
    }
    if (!empty($sql1)) {
        $username     = $DB->get_fieldset_sql($sql1);
        $timestampsql = 'SELECT username, MAX(timecreated) FROM mdl_quizaccess_useripmappings where quizid=' . $quizid . ' group by username having username IN ("' . implode('", "', $username) . '")';
        $timestamp    = $DB->get_records_sql_menu($timestampsql);
        if (!empty($username) && !empty($timestamp)) {
            $table = new html_table();
            echo "<p>Press enter when finished editing,Focus out of the edit box to Cancel.</p>";
            $table->head = array(
                'Sr. No',
                'Student ID',
                'User Name',
                'Student Name',
                'Time Stamp',
                'IP Address'
            );
            $srno        = 0;
            foreach ($timestamp as $key => $value) {
                $srno++;
                $sql    = "SELECT * FROM mdl_quizaccess_useripmappings WHERE quizid=$quizid AND username='$key' AND timecreated='$value'";
                $result = $DB->get_records_sql($sql);
                foreach ($result as $record) {
                    $ip                                   = $record->ip;
                    $recordid                             = $record->id;
                    $username                             = $record->username;
                    $firstname                            = $DB->get_field('user', 'firstname', array(
                        'username' => $username
                    ));
                    $lastname                             = $DB->get_field('user', 'lastname', array(
                        'username' => $username
                    ));
                    $fullname                             = "$firstname $lastname";
                    $studentid                            = $DB->get_field('user', 'idnumber', array(
                        'username' => $username
                    ));
                    $timestampinms                        = $record->timecreated;
                    $timestamp                            = date('Y-m-d H:i:s', $timestampinms);
                    $row                                  = new html_table_row();
                    $cell0                                = new html_table_cell($srno);
                    $cell1                                = new html_table_cell($studentid);
                    $cell2                                = new html_table_cell($username);
                    $cell3                                = new html_table_cell($fullname);
                    $cell4                                = new html_table_cell($timestamp);
                    $cell5                                = new html_table_cell($ip);
                    $cell5->attributes['contenteditable'] = 'true';
                    $cell5->text                          = $ip . " " . $OUTPUT->pix_icon('i/edit', 'To Edit IP');
                    $cell6                                = new html_table_cell($quizid);
                    $cell6->attributes['hidden']          = 'true';
                    $row->cells[]                         = $cell0;
                    $row->cells[]                         = $cell1;
                    $row->cells[]                         = $cell2;
                    $row->cells[]                         = $cell3;
                    $row->cells[]                         = $cell4;
                    $row->cells[]                         = $cell5;
                    $row->cells[]                         = $cell6;
                    $table->data[]                        = $row;
                }
            }
            echo html_writer::table($table);
        } else {
            echo "No record found with the provided details";
        }
    }
}
echo $OUTPUT->footer();
?>

<style>
#userlist {
    list-style: none;
    margin-top: -14px;
    margin-left: -21px;
    width: 205px;
}
#userlist li {
    padding: 3px;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, .15);
}
#userlist li:hover {
    background: #e6e6e6;
    cursor: pointer;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('form input[name=username]').val('');
    $('td').focus(function(e) {
        $(this).find('.fa-pencil').hide();
    });
    $('td').keydown(function(e) {
        if (e.keyCode == 13 || e.keyCode == 9) {
            var quizid = $(this).closest('tr').find('td:eq(6)').text();
            var username = $(this).closest('tr').find('td:eq(2)').text();
            var value = $(this).text();
            var url = "updateip.php";
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    quizid: quizid,
                    username: username,
                    ip: value
                }

            });
            $(this).blur();
            $('.fa-pencil').show();
        }
    });
    $("#userlist").on("click", "li", function() {
        $("#id_username").val($(this).text());
        $("#userlist").hide();
    });
    $("#id_username").keyup(function() {
        $.ajax({
            type: "POST",
            url: "suggestusernames.php",
            data: 'keyword=' + $(this).val(),
            beforeSend: function() {
                $("#id_username").css("background", "#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data) {
                $("#userlist").show();
                $("#userlist").html(data);
                $("#id_username").css("background", "#FFF");
            }
        });
    });
});
</script>
