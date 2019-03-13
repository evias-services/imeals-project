<?php

class IndexController
    extends FrontLib_Controller_Action
{
    private $_service = null;

    public function init()
    {
	    parent::init();
    }

    public function indexAction()
    {
    }

    public function loginAction()
    {
        $this->view->logError = false;

        if ($this->_hasParam('submitted')) {
            $log = $this->_getParam('access_name');
            $pass = $this->_getParam('access_cred');

            $params = array(
                'login' => $log,
                'password' => $pass
            );
            $result = $this->_tryAuth($params);

            if ($result === false) {
                $this->view->logError = true;
            }
            else {
                $this->_forward('index');
            }
        }
    }

    public function logoutAction()
    {

    }

    public function integrateAction()
    {
	    $this->view->layout()->setLayout("newlayout");
    }
}



