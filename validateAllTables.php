<?php
error_reporting ( E_ALL | E_STRICT );
date_default_timezone_set ( 'America/New_York' );
set_include_path ( '.' . PATH_SEPARATOR . './library/' . PATH_SEPARATOR . './models/' . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . get_include_path ());
/*
* Register an autoload() callback.  This is optional but very handy.
*/
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload ();
require_once ('stdlib.php');
require_once ('BuildAllTables.php');

$configMozart = new Zend_Config_Ini ( './config.ini', 'mozart' );
// setup database
$dbAdapter = Zend_Db::factory ( $configMozart->db->adapter, $configMozart->db->config->toArray () );
$dbName = trim ( $configMozart->db->config->dbname );
$rtRowSets = $dbAdapter->fetchAll ( "show tables;" );

foreach ( $rtRowSets as $row ) {
	$tableKey = "Tables_in_" . strtolower($dbName);
	$tableName = trim($row[$tableKey]);
	if ($tableName != "Labels" and $tableName != "ParentChild"  and $tableName != "Modules" and $tableName != "Personnel" and $tableName != "NoLookupTable" and substr ( $tableName, 0, 3 ) != 'lk_') {
		$rtAllColumnsArray = $dbAdapter->fetchAll ( "select * FROM $tableName order by id;" );
		foreach ( $rtAllColumnsArray as $rtAllColumns ) {
			foreach ( $rtAllColumns as $fieldName => $fieldValue ) {
				if (substr ( $fieldName, 0, 3 ) != 'dt_' and $fieldName != 'timestmp' and $fieldName != 'disable' and $fieldName != 'complete' and $fieldName != 'name' and $fieldName != 'l_name' and $fieldName != 'f_name') {
					if(!validate_DigitalValue($fieldValue)){
						echo '<h1 style="font-size: 20pt; font-weight: bold; color: #FF0000; text-align: center">';
		                echo "$tableName </h1>";
						echo '<div style="font-size: 20pt; font-weight: bold; color: #FF0000; text-align: left">';
						echo "$fieldName: </div>";
						echo '<th style="font-size: 20pt; font-weight: bold; color: #FF0000; text-align: left">';
						
						echo " $fieldValue </th>\n <br/>";
						print_r($rtAllColumns );
						echo "\n <br/>";
					}
				}
				elseif (substr ( $fieldName, 0, 3 ) == 'dt_'){
					if(!validate_date($fieldValue)){
						echo '<h1 style="font-size: 20pt; font-weight: bold; color: #FF0000; text-align: center">';
		                echo "$tableName </h1>";
						echo '<div style="font-size: 20pt; font-weight: bold; color: #FF0000; text-align: left">';
						echo "$fieldName: </div>";
						echo '<th style="font-size: 20pt; font-weight: bold; color: #FF0000; text-align: left">';
						
						echo " $fieldValue </th>\n <br/>";
						print_r($rtAllColumns );
						echo "\n <br/>";
					}
				}
			}
		}
	}
}


function validate_DigitalValue($value){
	$regexp="/\b[0-9]+:[0-9]+\b/";
	$value = str_replace(' ','',$value);
	if(is_numeric($value) or substr($value, 0, 1) == '.' or $value == 'Empty' or $value == '' 
	or preg_match($regexp, $value)){
		return TRUE;
	}
	else{
		return FALSE;
	}
}

function validate_date($value){
	$validator = new Zend_Validate_Date();
	//$validator ->  setFormat('yyyy-M-d');
	if($validator->isValid($value) or substr($value, 0, 1) == '.' or $value == 'Empty' or $value == ''){
		return true;
	}
	else{
		return false;
	}

}
?>