<?php

class Manage_CustomersController
    extends BackLib_Controller_Action
{
    public function listCustomersAction()
    {
        $filter     = $this->_getParam("filter", array());
        $page       = $this->_getParam('page', 1);
        $restaurant = $this->getRestaurant();

        $service    = AppLib_Service_Factory::getService("customer", array("return_objects" => true));
        $result     = $service->getCustomers(array(
            "id_restaurant" => $restaurant->id_restaurant,
            "filter"        => $filter));

        $icnt = 25;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function searchCustomerAction()
    {
        $this->getHelper("viewRenderer")
             ->setNoRender(true);

        $this->view->layout()->disableLayout();

        $term = $this->_getParam("term", "");

        $proxy = new AppLib_Service_Proxy;
        $proxy->setService("customer", array("return_objects" => true));

        $result = $proxy->__call("getCustomers", array(
            "id_restaurant" => Zend_Registry::get("restaurant")->id_restaurant,
            "filter" => array(
                "realname" => $term,
                "email" => $term,
                "phone" => $term),
            "filter_type" => "anyof"));

        $c = 0;
        $json = "[";
        foreach ($result as $item) {
            $json .= ($c++ > 0 ? ', ' : '')
                   . '{"id": "' . $item->id_customer . '", '
                   . '"value": "' . $item->realname . '",'
                   . '"desc": "Mail: ' . $item->email . ' - Tél.: ' . $item->phone . '",'
                   . '"label": "' . $item->realname . '"}';
        }
        $json .= "]";
        echo $json;
        exit;
    }

}
