<?php
class ServicesLib_Bootstrap
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
		$libAutoloader->registerNamespace('ServicesLib_');
		$libAutoloader->registerNamespace('AppLib_');
		$libAutoloader->registerNamespace('eVias_');

		return $moduleAutoloader;
	}

	public function _initDatabaseConnection()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini",
                                      APPLICATION_ENV);

        $adapter = new Zend_Db_Adapter_Pdo_Pgsql($config->db->toArray());
		eVias_ArrayObject_Db::setDefaultAdapter($adapter);
        Zend_Registry::set("config", $config);
        Zend_Registry::set("db", $adapter);
	}

    public function _initREST()
    {
        $frontController = Zend_Controller_Front::getInstance();

        /* Customized request & response objects */
        $frontController->setRequest(new ServicesLib_Controller_Request_Rest);
        $frontController->setResponse(new ServicesLib_Controller_Response_Rest);

        /* Front controller modifications */
        $rest_handler  = new ServicesLib_Controller_Plugin_RestHandler($frontController);
        $rest_switch   = new ServicesLib_Controller_Action_Helper_ContextSwitch;
        $rest_contexts = new ServicesLib_Controller_Action_Helper_RestContexts;

        $frontController->registerPlugin($rest_handler);
        Zend_Controller_Action_HelperBroker::addHelper($rest_switch);
        Zend_Controller_Action_HelperBroker::addHelper($rest_contexts);

        /* Route definition */
        $rest_route = new Zend_Rest_Route($frontController);
        $frontController->getRouter()
                        ->addRoute('rest', $rest_route); 
    }

    public function _initErrorHandler()
    {
        $frontController = Zend_Controller_Front::getInstance();
            
        $error_dispatch  = array(
            "module"     => "default",
            "controller" => "error",
            "action"     => "error");
        $error_handler  = new Zend_Controller_Plugin_ErrorHandler($error_dispatch);

        $frontController->registerPlugin($error_handler);
    }

    protected function _initCrypto()
    {

    }
}
