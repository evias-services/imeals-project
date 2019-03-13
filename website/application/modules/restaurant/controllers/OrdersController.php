<?php

class Restaurant_OrdersController
    extends FrontLib_Controller_Action
{
    public function printCartAction()
    {
        $this->view->layout()->disableLayout();

        /* Force reload of items for cart display update. */
        $this->getCart()
             ->getItems(true);

        $restaurant = $this->getRestaurant();
        $this->view->cart           = $this->getCart();
        $this->view->cart_min_price = 10;
        $this->view->cart_currency  = "&euro;";
        $this->view->requestURI     = $this->_getParam("ref", "");

        $this->render("display-cart");
    }

    public function displayCartAction()
    {
        $this->view->cart  = $this->getCart();

        $this->view->cart_min_price = 10;
        $this->view->cart_currency  = "&euro;";
    }

    public function itemToCartAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender(true);

        /* XXX Request security */

        $itemID = $this->_getParam("item_id", null);
        $wishes = $this->_getParam("wishes", array('add' => array(),'del' => array()));

        if (null !== $itemID) {
            try {
                $item = AppLib_Model_Item::loadById($itemID);
                $rid  = $item->getCategory()->getMenu()->id_restaurant;

                $cart = $this->getCart();

                $service = AppLib_Service_Factory::getService("cart");
                $service->addItem($cart->id_cart, $itemID, $wishes);
            }
            catch (AppLib_Service_Fault $e) {
            }

            $this->_redirect("/restaurant/menu/print/rid/" . $rid);
        }
    }

    public function delItemFromCartAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender(true);

        if ($this->getRequest()->isPost()) {
            $itemID = $this->_getParam("item_id", null);

            try {
                $cart = $this->getCart();

                $service = AppLib_Service_Factory::getService("cart");
                $service->removeItem($cart->id_cart, $itemID);
            }
            catch (AppLib_Service_Fault $e) {
            }
        }
    }

    public function sendCartAction()
    {
        $values = array();

        if (null !== $this->_getParam("customer", null))
            $values = $values + $this->_getParam("customer", array());

        if (null !== $this->_getParam("location", null))
            $values = $values + $this->_getParam("location", array());
    }

    public function previewOrderAction()
    {
        if (! $this->getRequest()->isPost()
            || (null === $this->_getParam("save_customer", null)
                && null === $this->_getParam("confirm-cart", null))) {
            /* Invalid preview-order request */

            $params = array("customer" => $this->_getParam("customer", array()),
                            "location" => $this->_getParam("location", array()));
            $this->_helper
                 ->redirector("send-cart", "orders", "restaurant", $params);
        }

        if ($this->getRequest()->isPost()
            && null !== $this->_getParam("save_customer", null)) {
            /* Save customer, the customer form has just been sent. */

            $input_customer = $this->_getParam("customer", array());
            $input_location = $this->_getParam("location", array());

            $cart       = $this->getCart();

            $errors   = array();
            try {
                $input_customer["country"] = "b";

                $input   = $input_customer + $input_location;

                $service = AppLib_Service_Factory::getService("order", array("return_objects" => true));
                $order   = $service->createOrder($cart->id_cart, $input);

                $this->view->order = $order;
                $this->view->cart  = $cart;

                $this->view->cart_min_price = 10;
                $this->view->cart_currency  = "&euro;";
            }
            catch (AppLib_Service_Fault $e) {
                /* Detailed error handling */
                if (empty($input_location["name"])
                    || empty($input_location["zipcode"])
                    || empty($input_location["city"])
                    || empty($input_location["address"])) {
                    $errors[] = FrontLib_Lang::tr("mandatory_field_missing");
                }

                if (empty($input_customer["phone"]) && empty($input_customer["email"]))
                    $errors[] = FrontLib_Lang::tr("missing_contact_information");
            }

            if (! empty($errors)) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors");
                foreach ($errors as $error)
                    $this->_helper
                         ->getHelper("FlashMessenger")
                         ->addMessage($error);

                $params = array("customer" => $input_customer,
                                "location" => $input_location);
                $this->_helper
                     ->redirector("send-cart", "orders", "restaurant", $params);
            }
        }
        elseif ($this->getRequest()->isPost()
                && null !== $this->_getParam("confirm-cart", null)) {
            /* Confirm the order, the cart confirmation form was sent. */

            if (null === ($orderID = $this->_getParam("oid", null))) {
                $this->_helper->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(FrontLib_Lang::tr("error_confirm_cart_retry"));

                $this->_redirect("/restaurant/orders/send-cart");
            }

            try {
                $service = AppLib_Service_Factory::getService("order");
                $service->setConfirmed((int) $orderID);

                $this->_redirect("/restaurant/orders/confirm-order");
            }
            catch (AppLib_Service_Fault $e) {
                $this->_helper->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(FrontLib_Lang::tr("error_confirm_cart_retry"));
            }

            $this->_redirect("/restaurant/orders/send-cart");
        }
    }

    public function confirmOrderAction()
    {
        $sess = new Zend_Session_Namespace("e-restaurant.eu");
        $sess->sess_id = null;
    }

    public function clearCartAction()
    {
        try {
            $cart = $this->getCart();

            $service = AppLib_Service_Factory::getService("order");
            $service->clearCart($cart->id_cart);

            $this->_helper->getHelper("FlashMessenger")
                ->setNamespace("messages")
                ->addMessage(FrontLib_Lang::tr("msg_clear_cart"));
        }
        catch (AppLib_Service_Fault $e) {
            $this->_helper->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(FrontLib_Lang::tr("error_clear_cart"));
        }

        $this->_redirect("/default/index/index");
    }
}
