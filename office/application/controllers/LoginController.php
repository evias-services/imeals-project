<?php

class LoginController
	extends BackLib_Controller_Action
{
	public function loginAction()
    {
        if ($this->getRequest()->isPost()) {

            $input  = $this->getRequest()->getParams();

            $login   = $input["identifier"];
            $pass    = $input["credential"];

            $result = $this->_processLogin($login, $pass);

            if ($result) {
                $identity = Zend_Auth::getInstance()->getIdentity();
                $accesses = AppLib_Model_RestaurantAccess::loadFirstByUserId($identity->id_e_user);

                if (false !== $accesses)
                    /* Update current restaurant session */
                    $this->setRestaurant($accesses->id_restaurant);

                $this->_redirect($this->getRefererUri());
            }

            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("errors")
                 ->addMessage(BackLib_Lang::tr("txt_error_login"));
            $this->_redirect("/default/login/login");
        }

        $referer = $this->getRefererUri();
        $this->view->referer = $referer;
	}

    public function logoutAction()
    {
        Zend_Auth::getInstance()
            ->clearIdentity();

        $this->_redirect("/default/index/index");
    }

}
