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
 * Strings for the quizaccess_useripmapping plugin.
 *
 * @package    quizaccess_useripmapping
 * @author     Amrata Ramchandani <ramchandani.amrata@gmail.com>
 * @copyright  2017 Indian Institute Of Technology,Bombay,India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$string['encoding']                         = 'Encoding';
$string['pluginname']                       = 'USER-IP Mapping quiz access rule';
$string['subnetwrong']                      = "This computer's IP Address does not match with the assigned IP Address";
$string['useripmappingrequiredadd']         = 'Use Student-IP Mappings for attempting the quiz';
$string['useripmappingrequiredadd_help']    = 'If you enable this option, students will be able to attempt the quiz only from the assigned IP Address.
Create this quiz and then edit quiz settings to manage student-ip mapping.';
$string['useripmappingrequiredupdate']      = 'Use Student-IP Mappings for attempting the quiz';
$string['useripmappingrequiredupdate_help'] = 'If you enable this option, students will be able to attempt the quiz only from the assigned IP Address.';
$string['allowifunassigned']                = 'Allow Unmapped';
$string['allowifunassigned_help']           = 'If you enable this option,
If not all the enrolled students have been assigned the IP Address,then whether they should be allowed to attempt the quiz ? In short,
Yes -> Allow all,Deny some.
No -> Deny all,Allow some.';
$string['notrequired']                      = 'No';
$string['useripmappingrequiredoption']      = 'Yes';
$string['useriplist']                       = 'Import Student-IP Mappings List';
$string['rowpreviewnum']                    = 'Preview rows';
$string['viewuseriplist']                   = 'View Student-IP Mappings List';
$string['uploadheader']                     = 'Upload Student-IP Address Mappings';
$string['file']                             = 'File';
$string['csvdelimiter']                     = 'CSV delimiter';
$string['trialstatus']                      = 'Trial Status';
$string['useripcsvline']                    = 'User IP CSV line';
$string['uploadmappings']                   = 'Upload Mappings';
$string['uploadmappings_help']              = 'Student-IP mappings should be uploaded via csv file.The format of the file should be as follows:
    
    
* Each line of the file contains one record
* Each record is a series of data separated by commas (or other delimiters)
* The first record contains a list of fieldnames defining the format of the rest of the file
* Required fieldnames are username,ip';
$string['editheader']                       = 'Edit Student-IP Mappings List';
$string['viewthelist']                      = 'View';
$string['username']                         = 'Student Name';
$string['idnumber']                         = 'Roll Number';
$string['addip']                            = 'Add Mapping';
$string['uploaduseripmappingresult']        = 'Uploaded Student-IP Mappings List';
$string['uploaduseripmappingpreview']       = 'Upload Mappings Preview';