<?php

class BackLib_Controller_Action
	extends eVias_Controller_Action
{
    private $_restaurant = null;

	public function init()
    {
		$this->getFrontController()
             ->addModuleDirectory(APPLICATION_PATH . '/modules');

        /* Initialize language translation adapter */
        $lang = $this->_getParam("language", null);
        BackLib_Lang::init($lang);

        /* Check for previous request' messages */
        $messages = $this->_helper
                         ->getHelper("FlashMessenger")
                         ->setNamespace("messages")
                         ->getMessages();
        $errors   = $this->_helper
                         ->getHelper("FlashMessenger")
                         ->setNamespace("errors")
                         ->getMessages();

        $this->view->messages      = $messages;
        $this->view->system_errors = $this->checkRequirements();
        $this->view->errors        = $errors;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()
                 ->setLayout("ajax-context");
        }
	}

    public function getRestaurant()
    {
        if (! isset($this->_restaurant))
            /* Get from REGISTRY, Plugin_Restaurant takes care
               of filling it. */
            $this->_restaurant = Zend_Registry::get("restaurant");

        return $this->_restaurant;
    }

    public function setRestaurant($restaurant)
    {
        if (! $restaurant instanceof AppLib_Model_Restaurant) {
            if (is_int($restaurant))
               $restaurant = AppLib_Model_Restaurant::loadById($restaurant);
            else
                throw new BackLib_Exception(get_class($this) . ": Wrong restaurant argument provided.)");
        }

        $id_user = Zend_Auth::getInstance()->getIdentity()->id_e_user;
        $access  = AppLib_Model_RestaurantAccess::loadByRestaurantIdAndUserId($restaurant->id_restaurant, $id_user);

        if (false === $access)
            /* set current not allowed. */
            $this->getResponse()
                 ->setRedirect($this->view->baseUrl() . "/default/index/denied")
                 ->sendResponse();
        else {
            /* Update current controller object,
               Zend_Registry and Session. */
            $this->_restaurant = $restaurant;
            Zend_Registry::set("restaurant", $restaurant);
            $session = new Zend_Session_Namespace("e-restaurant.current" . $id_user);
            $session->rid = $restaurant->id_restaurant;
        }
    }

    public function getRefererUri()
    {
        if (null !== $this->_getParam("referer", null))
            return urldecode($this->_getParam("referer"));

        return "/default/index/index";
    }

    protected function checkRequirements()
    {
        $system_errors = array();

        return $system_errors;
    }

    protected function _processLogin($login, $pass)
    {
        return AppLib_Model_User::login($login, $pass);
    }

}
