<?php

class Manage_RestaurantController
    extends BackLib_Controller_Action
{
    public function indexAction()
    {
        $filter  = $this->_getParam("filter", array());
        $page    = $this->_getParam('page', 1);

        $service = AppLib_Service_Factory::getService("restaurant", array("return_objects" => true));
        $result  = $service->getRestaurants(array(
            "filter"     => $filter));

        $icnt = 25;
        $adapter    = new Zend_Paginator_Adapter_Array($result);
        $paginator  = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($icnt);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
        $this->view->filter    = $filter;
    }

    public function setCurrentAction()
    {
        $restaurant_id = $this->_getParam("current_restaurant");

        if (empty($restaurant_id))
            $this->_redirect("/default/index/index");

        try {

            /* Modify session and registry. */
            $this->setRestaurant((int) $restaurant_id);

            /* XXX success message */
        }
        catch (Exception $e) {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage("Wrong restaurant selection.");
        }

        $this->_redirect("/default/index/index");
    }

    public function settingsAction()
    {
        if ($this->getRequest()->isPost())
        {
            $input = $this->getRequest()->getParams();

            $settings = $input['settings'];
            $restaurant = $this->getRestaurant();

            $service    = AppLib_Service_Factory::getService("restaurant");
            $service->saveSettings($restaurant->id_restaurant, $settings);

            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("messages")
                ->addMessage(BackLib_Lang::tr("txt_info_restaurant_save"));

            $this->_redirect("/manage/restaurant/settings");
        }

        $settings = $this->getRestaurant()->getSettings();
        $this->view->settings = $settings;
    }

    public function listRestaurantActionGrabberAction()
    {
        $restaurants = $this->_getParam("restaurants", array());
        $action      = $this->_getParam("selections_action", "");

        if (empty($restaurants) || empty($action))
            $this->_redirect("/manage/restaurant/index");

        $method     = strtolower($action) . "RestaurantAction";
        if (! method_exists($this, $method)) {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));
        }

        $action .= "-restaurant";
        $this->_forward($action, "restaurant", "manage",
                array("restaurants" => $restaurants));
    }

    public function deleteRestaurantAction()
    {
        if ($this->getRequest()->isPost()
            && $this->_getParam("is_confirmed", null) !== null) {

            $restaurants = $this->_getParam("restaurants", array());

            $service = AppLib_Service_Factory::getService("restaurant");
            foreach ($restaurants as $rid)
                $service->deleteRestaurant($rid);

            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("messages")
                ->addMessage(BackLib_Lang::tr("txt_info_restaurant_delete"));

            $this->_redirect("/manage/restaurant/index");
        }
        elseif ($this->getRequest()->isPost()) {

            $rids    = $this->_getParam("restaurants", array());

            $service = AppLib_Service_Factory::getService("restaurant", array("return_objects" => true));
            $restaurants = $service->getRestaurants(array(
                "filter"     => array(
                    "id_restaurant" => $rids)));

            $this->view->restaurants = $restaurants;
        }
        else {
            $this->_helper
                ->getHelper("FlashMessenger")
                ->setNamespace("errors")
                ->addMessage(BackLib_Lang::tr("txt_error_invalidrequest"));

            $this->_redirect("/manage/restaurant/index");
        }
    }

    public function addRestaurantAction()
    {
        if ($this->getRequest()->isPost())
        {
            $input    = $this->getRequest()->getParams();
            $row_data = $input['restaurant'];
            $service  = AppLib_Service_Factory::getService("restaurant");

            try {
                $service->editRestaurant($row_data);

                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("messages")
                     ->addMessage(BackLib_Lang::tr("txt_info_restaurant_save"));
            }
            catch (Exception $e) {
                $this->_helper
                     ->getHelper("FlashMessenger")
                     ->setNamespace("errors")
                     ->addMessage(sprintf(BackLib_Lang::tr("txt_error_restaurant_save"), $e->getMessage()));
            }

            /* Update controller's restaurant instance if necessary. */
            $current_restaurant = $this->getRestaurant();
            if (isset($row_data["id_restaurant"])
                && $current_restaurant->id_restaurant == $row_data["id_restaurant"])
                $this->setRestaurant(AppLib_Model_Restaurant::loadById($row_data["id_restaurant"]));

            $this->_redirect("/manage/restaurant/index");
        }
    }

    public function modifyRestaurantAction()
    {
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isGet()) {

            $itemID      = $this->_getParam("rid");

            $service     = AppLib_Service_Factory::getService("restaurant", array("return_objects" => true));
            $restaurants = $service->getRestaurants(array(
                "filter" => array("id_restaurant" => $itemID)));

            $this->view->restaurant = array_pop($restaurants);
            $this->render("add-restaurant");
        }
    }

}
