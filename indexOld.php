<?php
//$callingFromMybicServer = true;error_reporting ( E_ALL | E_STRICT ) ;
date_default_timezone_set ( 'America/New_York' ) ;
if (empty ( $callingFromMybicServer )) {
	$callingFromMybicServer = false ;
}

if ($callingFromMybicServer) {
	
	set_include_path ( '.' . PATH_SEPARATOR . '../library/' . PATH_SEPARATOR . '../models/' . PATH_SEPARATOR . '../lib/' . PATH_SEPARATOR . get_include_path () ) ;

} else {
	set_include_path ( '.' . PATH_SEPARATOR . './library/' . PATH_SEPARATOR . './models/' . PATH_SEPARATOR . './lib/' . PATH_SEPARATOR . get_include_path () ) ;

}


/*
* Register an autoload() callback.  This is optional but very handy.

*/
require_once 'Zend/Loader/AutoLoader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->setFallbackAutoloader(true);
$loader->suppressNotFoundWarnings(false);

require_once('stdlib.php') ;
require_once('BuildAllTables.php') ;




$inputArgv = nvl($argv);

// load configurationif ($callingFromMybicServer) {
	$configMozart = new Zend_Config_Ini ( '../config.ini', 'mozart' ) ;
} else {
	$configMozart = new Zend_Config_Ini ( './config.ini', 'mozart' ) ;
}

Zend_Registry::set ( 'config', $configMozart ) ;
// load LDAP Authentication configuration paramaters into Zend_Config_Ini Object instance,// and save it into Zend Registry tableif ($callingFromMybicServer) {
	$configLdap = new Zend_Config_Ini ( '../config.ini', 'ldap' ) ;
	$ldapSearch = new Zend_Config_Ini('../config.ini', 'ldapSearch');
	$msExchange = new Zend_Config_Ini('../config.ini', 'ExchangeServer');
} else {
	$configLdap = new Zend_Config_Ini ( './config.ini', 'ldap' ) ;
	$ldapSearch = new Zend_Config_Ini('./config.ini', 'ldapSearch');
	$msExchange = new Zend_Config_Ini('./config.ini', 'ExchangeServer');
}

Zend_Registry::set ( 'ldapconfig', $configLdap ) ;
Zend_Registry::set ( 'ldapSearch', $ldapSearch);
Zend_Registry::set ( 'MsExchange', $msExchange);

// setup database$dbAdapter = Zend_Db::factory ( $configMozart->db->adapter, $configMozart->db->config->toArray () ) ;
$dbAdapterMozart = Zend_Db::factory ( $configMozart->db->adapter, $configMozart->db->config->toArray () ) ;
//Zend_Db_Table::setDefaultAdapter($dbAdapter);Zend_Registry::set ( 'defaultDb', $dbAdapter ) ;
Zend_Registry::set ( 'dbAdapter', $dbAdapter ) ;
Zend_Registry::set ( 'dbAdapterMozart', $dbAdapterMozart ) ;

/**
 *
 * Setup Exchange Server
 */
 
$ExchangeServerVar = Zend_Registry::get ( 'MsExchange' );
$exchangeServerArray = $ExchangeServerVar->toArray ();
$exchangeServer = $exchangeServerArray['MsExchange'];
$tr = new Zend_Mail_Transport_Smtp($exchangeServer);
Zend_Mail::setDefaultTransport($tr);
//print_r($tr);

/**
 * Initialized $db variable for the Ajax application
 */
$db = $dbAdapterMozart ;

// setup RewriteBase Nameif ($callingFromMybicServer) {
	$RewriteBase = new Zend_Config_Ini ( '../config.ini', 'SiteRelated' ) ;
	Zend_Registry::set ( 'RewriteBase', $RewriteBase ) ;

} else {
	$RewriteBase = new Zend_Config_Ini ( './config.ini', 'SiteRelated' ) ;
	Zend_Registry::set ( 'RewriteBase', $RewriteBase ) ;
}
if (! $callingFromMybicServer) {
	
	//Create Zend_Acl object using Access Control List
	$acl = new Zend_Acl();
	$acl->add(new Zend_Acl_Resource('index'))
            ->add(new Zend_Acl_Resource('login'))
            ->add(new Zend_Acl_Resource('logout'))
            ->add(new Zend_Acl_Resource('edit'))
            ->add(new Zend_Acl_Resource('add'))
            ->add(new Zend_Acl_Resource('delete'))
            ->add(new Zend_Acl_Resource('ldapsearch'))
            ->add(new Zend_Acl_Resource('addperson'))
            ->add(new Zend_Acl_Resource('insertperson'))
            ->add(new Zend_Acl_Resource('updateperson'))
            ->addRole(new Zend_Acl_Role('user'))
            ->addRole(new Zend_Acl_Role('visitor'), 'user')
            ->addRole(new Zend_Acl_Role('editor'), 'user')
            ->addRole(new Zend_Acl_Role('admin'), 'editor')
            ->allow()
            ->deny(null, 'edit')
            ->deny(null, 'index')
            ->deny(null, 'add')
            ->deny(null, 'ldapsearch')
            ->deny(null, 'addperson')
            ->allow('user', 'add')
            ->allow('user', 'index')
            ->allow('user', 'edit')
            ->allow('admin', 'addperson')
            ->allow('admin', 'ldapsearch');
     Zend_Registry::set ( 'Acl', $acl ) ;       
	
	// setup controller	$frontController = Zend_Controller_Front::getInstance () ;
	$frontController->throwExceptions ( true ) ;
	$frontController->setControllerDirectory ( './controllers' )->registerPlugin ( new MyPlugin ( ) ) ;
	// run!
	$currentUser = Zend_Auth::getInstance()->getIdentity();	if (count ( $inputArgv ) == 1) {
		try {
			$response = $frontController->dispatch () ;
		} catch ( Exception $e ) {
			// handle exceptions yourself			if ($_SERVER [ 'HTTP_HOST' ] == 'mercx.bio.ri.ccf.org' or $_SERVER [ 'HTTP_HOST' ] == 'test.access.ccf.org') {
				echo $e ;
				echo "<br/> Current Session User: <br/>";
				print_r ($currentUser);
			} else {
				//$personTable = new PersonnelTableEdit ( );
                //$admin = $personTable->getModuleContents(1);
                //send mail to System Admin
                echo "Error report has been sent to Support Team, they will handle it as soon as possible.\n" ;
                echo $e;
                //if (substr_count ($e, "Invalid controller specified (externals)" ) == 0) {
                //$mail = new Zend_Mail( ) ;
                //$mail->setBodyText ($e . "\nCurrent Session User: \n" . serialize($currentUser) );
                //$mail->setBodyText (print_r($currentUser));
                //$mail->setFrom ($admin->email, 'System Admin') ;
                //$mail->addTo ($admin->email, 'System Admin');
                //$mail->setSubject ('DataEntry Error Report');
                //$mail->send () ;
				}
			}
	}
}
