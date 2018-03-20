<?php
error_reporting ( E_ALL | E_STRICT );
date_default_timezone_set ( 'America/New_York' );
set_include_path ( '.' . PATH_SEPARATOR . './library/' . PATH_SEPARATOR . './models/' . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . get_include_path () . PATH_SEPARATOR . '/var/www/ZendFramework-1.5.0/library' );
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
$ptMain = new PtMain($dbAdapter);
$rtRowSets = $dbAdapter->fetchAll ( "select * from mainBackup09052008" );

$i = 0;
foreach ( $rtRowSets as $row ) {
	$currentId = $row ['clinicid'];
	$lastName = $row ['l_name'];
	$firstName = $row ['f_name'];
	$dt_surg = $row ['dt_surg'];
	
	$pr_iraaa = $row['pr_iraaa'];
	$pr_typad = $row['pr_typad'];
	$pr_typbd = $row['pr_typbd'];
	$ed_syndm = $row['ed_syndm'];
	$conn_tis = $row['conn_tis'];
	$pre_man = $row['pre_man'];
	$pre_bicu = $row['pre_bicu'];
	$pre_ehle = $row['pre_ehle'];
	$pre_lowe = $row['pre_lowe'];
	$pre_coar = $row['pre_coar'];
	$pre_othe = $row['pre_othe'];
	$aux_acan = $row['aux_acan'];
	$tm_ischm = $row['tm_ischm'];
	$dd_anstm = $row['dd_anstm'];
	$td_anstm = $row['td_anstm'];
	$etd_anst = $row['etd_anst'];
	$etd_lsca = $row['etd_lsca'];
	$etp_lsca = $row['etp_lsca'];
	$da_type = $row['da_type'];
	$da_site = $row['da_site'];
	$sp_brcph = $row['sp_brcph'];
	$bg_brcph = $row['bg_brcph'];
	$ba_type = $row['ba_type'];
	$pa_grfto = $row['pa_grfto'];
	$pa_avrr = $row['pa_avrr'];
	$pa_root = $row['pa_root'];
	$pa_type = $row['pa_type'];
	$pa_site = $row['pa_site'];
	$tr_nrdfc = $row['tr_nrdfc'];
	$needreop = $row['needreop'];
	$dsch_lcn = $row['dsch_lcn'];
	$po_nyha = $row['po_nyha'];
	$ltrp_aop = $row['ltrp_aop'];
	$where = "clinicid='$currentId' and dt_surg='$dt_surg'";
	
	$newData = array ('pr_iraaa' => $pr_iraaa ,'pr_typad' => $pr_typad,
	 'pr_typbd'=>$pr_typbd,
	'ed_syndm'=>$ed_syndm,
	 'conn_tis'=>$conn_tis,
	 'pre_man'=>$pre_man,
	'pre_bicu'=>$pre_bicu ,
	'pre_ehle'=>$pre_ehle ,
	 'pre_lowe'=>$pre_lowe,
	 'pre_coar'=>$pre_coar,
	'pre_othe'=>$pre_othe,
	'aux_acan'=>$aux_acan,
	 'tm_ischm'=>$tm_ischm,
	'dd_anstm'=>$dd_anstm,
	 'td_anstm'=>$td_anstm,
	 'etd_anst'=>$etd_anst,
	 'etd_lsca'=>$etd_lsca,
	 'etp_lsca'=>$etp_lsca,
	 'da_type'=>$da_type,
	 'da_site'=>$da_site,
	 'sp_brcph'=>$sp_brcph,
	 'bg_brcph'=>$bg_brcph,
	 'ba_type'=>$ba_type,
	 'pa_grfto'=>$pa_grfto,
	 'pa_avrr'=>$pa_avrr,
	'pa_root'=>$pa_root,
	 'pa_type'=>$pa_type,
	'pa_site'=>$pa_site,
	 'tr_nrdfc'=>$tr_nrdfc,
	 'needreop'=>$needreop,
	 'dsch_lcn'=>$dsch_lcn,
	 'po_nyha'=>$po_nyha,
	 'ltrp_aop'=>$ltrp_aop);
	$affectedRow = $ptMain->update( $newData, $where );

}


?>