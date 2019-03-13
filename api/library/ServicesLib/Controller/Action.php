<?php

class ServicesLib_Controller_Action
	extends Zend_Controller_Action
{
    protected $_messages = array();
    protected $_errors   = array();

    public function init()
    {
        /* Check for previous request' messages */
        $this->_messages = $this->_helper
                                ->getHelper("FlashMessenger")
                                ->setNamespace("messages")
                                ->getMessages();
        $this->_errors   = $this->_helper
                                ->getHelper("FlashMessenger")
                                ->setNamespace("errors")
                                ->getMessages();
	}

}
