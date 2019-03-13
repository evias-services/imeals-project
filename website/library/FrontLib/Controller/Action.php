<?php

class FrontLib_Controller_Action
	extends eVias_Controller_Action
{
    protected $_session = null;
    private   $_current_module = "default";
    private   $_cart       = null;
    private   $_restaurant = null;

    public function init()
    {
		$this->getFrontController()
             ->addModuleDirectory(APPLICATION_PATH . '/modules');

        /* Initialize language translation adapter */
        $lang = $this->_getParam("language", null);
        FrontLib_Lang::init($lang);

        /* Check for previous request' messages */
        $messages = $this->_helper
                         ->getHelper("FlashMessenger")
                         ->setNamespace("messages")
                         ->getMessages();
        $errors   = $this->_helper
                         ->getHelper("FlashMessenger")
                         ->setNamespace("errors")
                         ->getMessages();

        /* Inject */
        $this->view->messages = $messages;
        $this->view->errors   = $errors;
        
        $this->initView();

        /* Process request query string format for navigation. */
        $this->view->navigation_classes = $this->getMenuButtonClasses();    
	}

    public function initView()
    {
        $this->view->doctype('XHTML1_STRICT');
		$this->view->headTitle()->setSeparator(' - ');
        $this->view->headTitle("eRestaurant - Restaurants in the Cloud");

		$this->view->headMeta()->appendHttpEquiv('Content-type', 'text/html;charset=utf-8');
        $this->view->headMeta()->appendName("description",
            "Commandes en ligne, livraison, restaurant, online bestellungen, bestellung, lieferung, online order, order, delivery");
        $this->view->headMeta()->appendName("keywords",
            "online bestellung, commande en ligne, order online, speise bestellen, commander plat, livraison plat");
        $this->view->headMeta()->appendName("author", "Grégory Saive (greg@evias.be - http://www.evias.be)");
    }

    public function getMenuButtonClasses() 
    {
        $liHomeCls       = "home";
        $liMenuCls       = "menu";
        $liPromotionsCls = "promotions";
        $liNewsCls       = "news";
        $liContactCls    = "contact";

        $request = $this->getRequest()->getServer("REQUEST_URI");
        $baseUrl = AppLib_Utils::cleanRegExp($this->view->baseUrl());

        if (preg_match("/^$baseUrl\\/restaurant(\\/.*)?$/", $request)) {
            /* Restaurant module */
            $this->_current_module = "restaurant";

            if (preg_match("/^$baseUrl\\/restaurant\\/menu\\/print(\\/.*)?$/", $request)
                || preg_match("/^$baseUrl\\/restaurant\\/menu\\/?$/", $request))
                $liMenuCls .= " active";
            elseif (preg_match("/^$baseUrl\\/restaurant\\/menu\\/offers(\\/.*)?$/", $request))
                $liPromotionsCls .= " active";
            elseif (preg_match("/^$baseUrl\\/restaurant\\/orders(\\/.*)?$/", $request))
                $liNewsCls .= " active";
            elseif (preg_match("/^$baseUrl\\/restaurant\\/info(\\/.*)?$/", $request))
                $liContactCls .= " active";
        }
        elseif (preg_match("/^$baseUrl\\/default.*$/", $request)
                || preg_match("/^$baseUrl\\/(.*)?$/", $request)) {
            /* Default module */
            $this->_current_module = "default";

            $liHomeCls .= " active";
        }

        return array($liHomeCls, 
                     $liMenuCls,
                     $liPromotionsCls,
                     $liNewsCls,
                     $liContactCls);
    }

    public function getCart()
    {
        if (! isset($this->_cart))
            $this->_cart = AppLib_Model_Cart::getInstance();

        return $this->_cart;
    }

    public function getRestaurant()
    {
        if (! isset($this->_restaurant))
            $this->_restaurant = Zend_Registry::get("restaurant");

        return $this->_restaurant;
    }

}
