<?php

class FrontLib_Controller_Plugin_User
    extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        /* Load potential session cart */
        AppLib_Model_Cart::getInstance()
              ->loadBySession($this->getSession());

        return parent::preDispatch($request);
    }

    public function getSession()
    {
        /* XXX session is application-specific */
        $session = new Zend_Session_Namespace("e-restaurant.eu");

        if (! isset($session->sess_id) || null == $session->sess_id) {
            /* New session */
            Zend_Session::regenerateId();
            $session->sess_id = Zend_Session::getId(); 
        }

        return $session;
    }

}
