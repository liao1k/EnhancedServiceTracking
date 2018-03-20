<?php
error_reporting ( E_STRICT );
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

$oneToMany = nvl ( $_GET ['oneToMany'] );
$table = nvl ( $_GET ['table'] );
$parent = nvl ( $_GET ['parent'] );
$child = nvl ( $_GET ['child'] );
$lookup = nvl ( $_GET ['lookup'] );
//$noTableData = nvl($_GET['noTableData']);
$id = nvl ( $_GET ['id'] );
$isPost = count ( nvl ( $_POST ) );

$frm = '';
if ($child == '') {
	$item = $parent;
} else {
	$item = $child;
}
$options = buildComboOptionList ( $id, $table, $oneToMany, $lookup, $item, $frm, $child, 'No', $isPost, $dbAdapter );
header ( "Content-type:text/xml" );
print ( "<?xml version=\"1.0\"?>" );
echo "<complete>";
echo $options;
echo "</complete>";

/**
 * Build dhtmlXCombo option list from the lookup table
 *
 * @param String $general_id
 * @param String $table
 * @param String $item
 * @param Array Post variables $frm
 * @param String $fromPost determine to get the value from the Post or table
 * @return Array option
 */
function buildComboOptionList($general_id, $table, $oneToMany, $lookup, $item, $frm, $child, $fromPost, $isPost, $dbAdapter) {
	$rowset = $dbAdapter->fetchAll ( "select code, descr from $lookup order by id" );
	$options = getOptionsCombo ( $rowset, $general_id, $table, $oneToMany, $lookup, $item, $frm, $child, $fromPost, $isPost, $dbAdapter );
	return $options;
}

function getOptionsCombo($rowset, $general_id, $table, $oneToMany, $lookup, $item, $frm, $child, $fromPost, $isPost, $dbAdapter) {
	$general_item = '';
	$general_item = get_general_item ( $general_id, $table, $oneToMany, $item, $frm,$fromPost, $dbAdapter );
	$generalItemExisted = general_item_exist ( $general_item,$lookup, $dbAdapter );
	
	$options = '';
	if (!$generalItemExisted and $child == '' ) {
		$options = '<option value="' . trim ( $general_item ) . '"' . ' selected = "selected">' . trim ( $general_item ) . '</option>';
		//$options = '<option value="' . trim ( $general_item ) . '"' . ' selected = "selected">' . '   ' . '</option>';
		foreach ( $rowset as $row ) {
			$options .= '<option value="' . trim ( $row ['code'] ) . '">' . $row ['descr'] . '</option>';
		}
	} elseif ($child != '') {
		$options = '<option value="" selected = "selected">   </option>';
		foreach ( $rowset as $row ) {
			$options .= '<option value="' . trim ( $row ['code'] ) . '">' . $row ['descr'] . '</option>';
		}
	} else {
		foreach ( $rowset as $row ) {
			if (trim ( $row ['code'] ) != $general_item) {
				$options .= '<option value="' . trim ( $row ['code'] ) . '">' . $row ['descr'] . '</option>';
			} else {
				$options .= '<option value="' . trim ( $row ['code'] ) . '"' . ' selected="selected" >' . $row ['descr'] . '</option>';
			}
		}
	}
	
	return $options;
}

/**
 * This function is called by getOptionCombo
 * to build dhtmlCombo box from the its lookup table values 
 *
 * @param string $general_id
 * @param string table name
 * @param string $item
 * @param Array Post variables $frm
 * @param string $fromPost Determine to get value from Post or Table
 **/
function get_general_item($general_id, $table, $oneToMany, $item, $frm, $fromPost, $dbAdapter) {
	if ($oneToMany == 'Yes' or $table == 'main') {
		$qid = $dbAdapter->query ( "select $item from $table where
                          id ='$general_id'" );
	} elseif ($oneToMany == 'No' and $table != 'main') {
		$qid = $dbAdapter->query ( "select $item from $table where
                          mainID ='$general_id'" );
	}
	
	$cat = $qid->fetch ();
	if ($fromPost == 'No') {
		return trim ( $cat [$item] );
	} else {
		return nvl ( $frm [$item] );
	}

}

function general_item_exist($general_item, $lookup, $dbAdapter) {
	$general_item = trim ( $general_item );
	$qid = $dbAdapter->fetchAll ( "select code from $lookup where
                          trim(code) ='$general_item'" );
	if (count ( $qid ) == 0) {
		return false;
	} else {
		return true;
	}

}

?>