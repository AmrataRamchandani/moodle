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
 * Post-install script for the quizaccess_useripmapping plugin.
 * 
 * @package    quizaccess
 * @subpackage useripmapping
 * @copyright  2017 Indian Institute Of Technology,Bombay,India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post-install script
 */
function xmldb_quizaccess_useripmapping_install() {
	global $DB;
	
	$dbman = $DB->get_manager();
	
	$record1 = new stdClass();
	$record1->userid = 2;
	$record1->ip = '127.0.0.1';
	
	$record2 = new stdClass();
	$record2->userid = 3;
	$record2->ip = '127.0.0.3';
	
	$records = array($record1, $record2);
		
	if ($dbman->table_exists('user_ip_mapping')) {
		$DB->insert_records('user_ip_mapping', $records);
	} 
}

