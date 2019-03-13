<?php

class IndexController
	extends BackLib_Controller_Action
{
    private $_service = null;

	public function init()
	{
		parent::init();
	}

	public function indexAction()
    {
		$this->view->headTitle('Pizzeria Da Antonio');
	}

    public function deniedAction()
    {
        $this->view->referer = $this->getRefererUri();
    }

}
