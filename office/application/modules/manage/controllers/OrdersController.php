<?php

class Manage_OrdersController
    extends BackLib_Controller_Action
{
    public function indexAction()
    {
    }

    public function fetchOrdersAction()
    {
        $this->getHelper("viewRenderer")
             ->setNoRender(true);
        $this->view->layout()
                   ->disableLayout();

        $restaurant = $this->getRestaurant();
        $service    = AppLib_Service_Factory::getService("order");
        $unseen     = $service->getOrders($restaurant->id_restaurant, array(
            "filter" => array(
                "seen"      => false,
                "confirmed" => true,
                "deleted"   => false)));

        $cnt_unseen = count($unseen);
        echo "$cnt_unseen";
    }

    public function listOrdersAction()
    {
        $page = $this->_getParam('page', 1);
        $restaurant = $this->getRestaurant();

        $service    = AppLib_Service_Factory::getService("order", array("return_objects" => true));
        $result     = $service->getOrders($restaurant->id_restaurant, array(
            "filter" => array(
                "confirmed" => true,
                "deleted"   => false)));

        $icnt = 25;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
    }

    public function lastOrderDateAction()
    {
        $this->getHelper("viewRenderer")
             ->setNoRender(true);
        $this->view->layout()->disableLayout();

        $restaurant = $this->getRestaurant();
        $service    = AppLib_Service_Factory::getService("order");
        $result     = $service->getLastOrder($restaurant->id_restaurant)->date_created;

        echo $result;
    }

    public function listOrdersTableContentAction()
    {
        $this->view->layout()->disableLayout();

        $page = $this->_getParam('page', 1);
        $restaurant = $this->getRestaurant();

        $service    = AppLib_Service_Factory::getService("order", array("return_objects" => true));
        $result     = $service->getOrders($restaurant->id_restaurant, array(
            "filter" => array(
                "confirmed" => true,
                "deleted"   => false)));


        $icnt = 25;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;

        $this->render("list-orders-table-content");
    }

    /**
     * Sets the order to SEEN state and displays it.
     */
    public function setSeenAction()
    {
        $this->view->layout()->disableLayout();

        $order_id   = $this->_getParam("oid");
        $restaurant = $this->getRestaurant();

        $service = AppLib_Service_Factory::getService("order", array("return_objects" => true));
        $order   = $service->setSeen($restaurant->id_restaurant, $order_id);

        $this->view->action_dialog = true;
        $this->view->order = $order;
        $this->render("display-order");
    }

    public function printOrderAction()
    {
        $this->view->layout()->disableLayout();

        $order_id   = $this->_getParam("oid");
        $restaurant = $this->getRestaurant();

        $service = AppLib_Service_Factory::getService("order", array("return_objects" => true));
        $order   = $service->getOrders($restaurant->id_restaurant, array(
            "filter" => array(
                "ids" => array($order_id))));

        $this->view->order = $order[0];
        $this->render("display-order");
    }

    public function displayOrderAction()
    {
    }

    public function actionGrabberAction()
    {
        $orders = $this->_getParam("orders", array());
        $action = $this->_getParam("selections_action", "");

        if (empty($orders) || empty($action))
            $this->_redirect("/manage/orders/list-orders");

        $method = strtolower($action) . "OrderAction";
        if (! method_exists($this, $method)) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-order";
        $this->_forward($action, "orders", "manage",
                        array("orders" => $orders));

    }

    public function deleteOrderAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $orders     = $this->_getParam("orders", array());
            $restaurant = $this->getRestaurant();

            $service = AppLib_Service_Factory::getService("order");
            foreach ($orders as $oid)
                $service->deleteOrder($restaurant->id_restaurant, $oid);

            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("messages")
                 ->addMessage(BackLib_Lang::tr("txt_info_order_delete"));

            $this->_redirect("/manage/orders/list-orders");
        }
        elseif ($this->getRequest()->isPost()) {

            $oids       = $this->_getParam("orders", array());
            $restaurant = $this->getRestaurant();

            $service = AppLib_Service_Factory::getService("order", array("return_objects" => true));
            $orders  = $service->getOrders($restaurant->id_restaurant, array(
                "filter" => array(
                    "ids" => $oids)));

            $this->view->orders = $orders;
        }
        else {
            $this->_helper
                 ->getHelper("FlashMessenger")
                 ->setNamespace("errors")
                 ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/orders/list-orders");
        }
    }

}
