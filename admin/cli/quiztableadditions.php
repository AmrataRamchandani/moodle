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
 * @package    core
 * @subpackage cli
 * @copyright  2017 Amrata Ramchandani 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(array('help' => false), array('h' => 'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if ($options['help']) {
    $help =
"Adds new column 'enableclear' in moodle quiz table
Adds 2 new records in moodle config plugins.
The records are (quiz,enableclear_adv,1) and (quiz,enableclear,0)

Options:
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/cli/quiztableadditions.php
";

    echo $help;
    exit(0);
}

global $DB;

$dbman = $DB->get_manager();

$table = new xmldb_table('quiz');
$field = new xmldb_field('enableclear',XMLDB_TYPE_INTEGER,'4'); 

if (!$dbman->field_exists($table, $field)) {
	$dbman->add_field($table, $field);
}

set_config('enableclear_adv', '1', 'quiz');
set_config('enableclear', '0', 'quiz');

purge_all_caches();
exit(0);