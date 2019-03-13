<?php

class FrontLib_Controller_Plugin_ErrorHandler
    extends Zend_Controller_Plugin_ErrorHandler
{
    public function postDispatch()
    {
        die("error handlllerrr");
    }
}
