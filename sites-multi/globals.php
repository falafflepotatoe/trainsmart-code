<?php
/*
 * Created on Feb 11, 2008
 * 
 *  Built for I-Tech  
 *  Fuse IQ -- todd@fuseiq.com
 *  
 */
 
 class Globals {
 	public static $BASE_PATH = '/var/www/';
 	public static $WEB_FOLDER = 'html';
	public static $COUNTRY = null;
	public static $SITE_TITLE = 'TrainSMART';

	public static $DB_TABLE_PREFIX = 'itech_';
	public static $DOMAIN = 'trainingdata.org';

 	public function __construct() {
		//set country
		//lookup country from subdomain
		// must be format like http://country.site.org
		$parts = explode('.', $_SERVER['HTTP_HOST']);
		self::$COUNTRY = $parts[0];
		
		require_once('settings.php');
		$countryLoaded = false;

		if ( $parts[1] == 'trainingdata' ) {
			Settings::$DB_DATABASE = Globals::$DB_TABLE_PREFIX.$parts[0];

			self::$COUNTRY = $parts[0];
			Settings::$COUNTRY_BASE_URL = 'http://'.$parts[0].'.'.Globals::$DOMAIN;
			$countryLoaded = true;
		}
		
		error_reporting( E_ALL 
		// | E_STRICT 
		);
		
		// PATH_SEPARATOR =  ; for windows, : for *nix
		 	$iReturn = ini_set( 'include_path',
					(Globals::$BASE_PATH).PATH_SEPARATOR.
					(Globals::$BASE_PATH).'app'.PATH_SEPARATOR.
					(Globals::$BASE_PATH.'ZendFramework'.DIRECTORY_SEPARATOR.'library').PATH_SEPARATOR.
						ini_get('include_path'));
		require_once 'Zend/Loader.php';
 		
 		if ( $countryLoaded ) {
			//fixes mysterious configuration issue
			require_once('Zend/Db/Adapter/Pdo/Mysql.php');
			require_once 'Zend/Db.php';
			//set a default database adaptor
			$db = Zend_Db::factory('PDO_MYSQL', array(
				'host'     => Settings::$DB_SERVER,
				'username' => Settings::$DB_USERNAME,
				'password' => Settings::$DB_PWD,
				'dbname'   => Settings::$DB_DATABASE
			));
			 require_once 'Zend/Db/Table/Abstract.php';
			Zend_Db_Table_Abstract::setDefaultAdapter($db);
		}
 	}
 }
 
 new Globals();
