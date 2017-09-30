<?php 
require_once('../../../../config.php');

global $DB;
$id =$_POST['recordid'];
$ip=$_POST['ip'];

$record = new stdClass();
$record->id=$id;
$record->ip=$ip;

$vl=$DB->update_record('user_ip_mapping', $record, $bulk=false);


?>
