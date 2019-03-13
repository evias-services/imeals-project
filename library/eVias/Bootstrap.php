<?php

/**
 *	Class: eVias_Bootstrap
 *	@author: Grégory Saive (saive.gregory@gmail.com)
 *  @brief:
 *		Bootstrap of any eVias application,
 *		should implement any initialization of the application
 *		such that the application is completly configured
 *		after setting up the bootstrap.
 *
 */

class eVias_Bootstrap 
	extends Zend_Application_Bootstrap_Bootstrap
{

	public function _initAutoloader() {
		// register default namespace
		$moduleAutoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath'  => APPLICATION_PATH));

		// register namespace for eVias Framework
		$libAutoloader = Zend_Loader_Autoloader::getInstance();
		$libAutoloader->registerNamespace('eVias_');

		return $moduleAutoloader;
	}
	
	/**
	 * Create connection
	 *
	 * @todo should use pgsql driver ..
	 *	@todo install pgsql
	 */
	protected function _initDatabaseConnection() {
	/*	$args = array(
			'host'		=> 'localhost',
			'username'	=> 'root',
			'password'  => 'xaJae7uu',
			'dbname'	=> 'evias'
		);

		eVias_ArrayObject_Db::setDefaultAdapter(new Zend_Db_Adapter_Pdo_Mysql($args));
	 */}
	public function _initViewHelpers() {
		$view = new eVias_View();

		$view->doctype('XHTML1_STRICT');
		$view->headMeta()->appendHttpEquiv('Content-type', 'text/html;charset=utf-8');
		$view->headTitle()->setSeparator(' - ');
		$view->headTitle('eVias Web Application');


		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer->setView($view);

		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
	}


	/**
	 * _initRouter
	 * initiliazes default routes to several modules,
	 * controllers and actions.
	 *
	 * @todo : read from configuration
	 *
	 * @return void
	 */
	protected function _initRouter() {
		$frontCtl 	= Zend_Controller_Front::getInstance();
		$router 	= $frontCtl->getRouter();

		foreach ($this->_getRoutes() as $routeTitle => $route) {
			$router->addRoute($routeTitle, $route);
		}
	}


	private function _getRoutes() {
		return eVias_Routes::fetch();
	}

	/**
	 * List of methods to implement
	 *
	 * @todo : _initOptions
	 *		   @brief: load configuration
	 *
	 * @todo : _initView
	 *		   @brief: identify styles according to request (ajax,..)
	 */
}
