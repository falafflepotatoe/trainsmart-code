<?php
/**
 * Main bootstrap file for ITech app
 * Fuse IQ
 * 
 */
require_once('../sites/globals.php');

try {
    Zend_Loader::loadClass('Zend_Controller_Front');
    $frontController = Zend_Controller_Front::getInstance(); 
    //$frontController->throwExceptions(true);
    $frontController->setControllerDirectory(Globals::$BASE_PATH.'app/controllers/');
    
    $frontController->setDefaultControllerName('index');
    $frontController->setDefaultAction('index');
    
 	$frontController->returnResponse(true);
    $response = $frontController->dispatch();
    $response->sendHeaders();
    $response->outputBody();
} catch (exception $e) {
	ob_start();
	var_export($e);
	error_log(ob_get_clean());
}
?>