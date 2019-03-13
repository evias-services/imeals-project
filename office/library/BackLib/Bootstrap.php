<?php

class BackLib_Bootstrap
    extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initAutoloader()
    {
	// register default namespace
	$moduleAutoloader = new Zend_Application_Module_Autoloader(array(
		'namespace' => '',
		'basePath'  => APPLICATION_PATH));

	// register namespace for eVias Framework
	$libAutoloader = Zend_Loader_Autoloader::getInstance();
	$libAutoloader->registerNamespace('BackLib_');
	$libAutoloader->registerNamespace('AppLib');
	$libAutoloader->registerNamespace('eVias_');

	return $moduleAutoloader;
    }

    public function _initDatabaseConnection()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini",
                                      APPLICATION_ENV);

        $adapter = new Zend_Db_Adapter_Pdo_Pgsql($config->db->toArray());
		eVias_ArrayObject_Db::setDefaultAdapter($adapter);
        Zend_Registry::set("db", $adapter);
    }

    public function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);

        return $config;
    }

    public function _initRegisterPlugins()
    {
        Zend_Controller_Front::getInstance()
             ->registerPlugin(new BackLib_Controller_Plugin_AccessControl())
             ->registerPlugin(new BackLib_Controller_Plugin_Restaurant());
    }

    public function _initViewHelpers()
    {
	$this->bootstrap('layout');
	$layout = $this->getResource('layout');
	$layout->setInflectorTarget(':script.:suffix');
	$layout->setViewSuffix('php');

	$view = new eVias_View();

	$view->doctype('XHTML1_STRICT');
	$view->headMeta()->appendHttpEquiv('Content-type', 'text/html;charset=utf-8');
	$view->headTitle()->setSeparator(' - ');
	$view->headTitle('Back Office');
/* XXX Restaurant Title */
	$view->headTitle('e-restaurant.eu');
	$view->addHelperPath(dirname(__FILE__) . '/View/Helper/', 'BackLib_View_Helper');

	$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
	$viewRenderer->setView($view)
		     ->setViewSuffix('php');

	Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

}
