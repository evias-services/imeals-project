<?php
class FrontLib_Bootstrap
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
		$libAutoloader->registerNamespace('FrontLib_');
		$libAutoloader->registerNamespace('AppLib_');
		$libAutoloader->registerNamespace('eVias_');

		return $moduleAutoloader;
	}

    public function _initConfig()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini",
                                      APPLICATION_ENV);
        Zend_Registry::set("config", $config);
    }

	public function _initDatabaseConnection()
    {
        $config  = Zend_Registry::get("config");

        $adapter = new Zend_Db_Adapter_Pdo_Pgsql($config->db->toArray());
		eVias_ArrayObject_Db::setDefaultAdapter($adapter);
	}

	public function _initViewHelpers()
    {
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$layout->setInflectorTarget(':script.:suffix');
		$layout->setViewSuffix('php');

		$view = new eVias_View();
		$view->addHelperPath(dirname(__FILE__) . '/View/Helper/', 'FrontLib_View_Helper');

		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view)
					 ->setViewSuffix('php');

		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
	}

    public function _initPlugins()
    {
        Zend_Controller_Front::getInstance()
            ->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(
                   array("module"     => "default",
                         "controller" => "error",
                         "action"     => "error")))
            ->registerPlugin(new FrontLib_Controller_Plugin_User);

    }

    public function _initRestaurant()
    {
        $config     = Zend_Registry::get("config");
        $restaurant = AppLib_Model_Restaurant::loadById($config->current_restaurant);

        Zend_Registry::set("restaurant", $restaurant);
    }

}
