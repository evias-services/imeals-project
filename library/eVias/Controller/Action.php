<?php

class eVias_Controller_Action extends Zend_Controller_Action
{
    public function init() {
		// Add modules' directory to inject delcared modules
		$this->getFrontController()->addModuleDirectory(APPLICATION_PATH . '/modules');

		// initialize navigation
		$this->_initNavigation();
	}

	// @todo : read configuration
	protected function _initNavigation() {
		$pages = array(
			array(
				'type'		=> 'mvc',
				'label'		=> 'Home',
				'route'		=> 'home'
			),
			array(
				'type'		=> 'mvc',
				'label'		=> 'Customize Home',
				'route'		=> 'home/customize'
			),
			array(
				'type'		=> 'mvc',
				'label'		=> 'Catalogue Module',
				'route'		=> 'catalogue'
			),
			array(
				'type'		=> 'mvc',
				'label'		=> 'Member Module',
				'route'		=> 'member'
			),
		);

		$container = new Zend_Navigation($pages);
		$this->view->navigation($container);
	}
}
