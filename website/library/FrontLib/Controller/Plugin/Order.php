<?php

class FrontLib_Controller_Plugin_Order
    extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        return parent::preDispatch($request);
    }
}
