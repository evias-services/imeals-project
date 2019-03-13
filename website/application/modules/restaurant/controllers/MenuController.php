<?php

class Restaurant_MenuController
    extends FrontLib_Controller_Action
{
    public function indexAction()
    {
        $this->_redirect("/restaurant/menu/print");
    }

    public function printAction()
    {
        if ($this->_getParam("rid", null) !== null)
            $restaurant = AppLib_Model_Restaurant::loadById($this->_getParam("rid"));
        else
            $restaurant = $this->getRestaurant();

        $this->view->menu = $restaurant->getMenu();
    }

    public function getCurrentPriceAction()
    {
        $this->getHelper("viewRenderer")->setNoRender(true);
        $this->view->layout()->disableLayout();

        if ($this->getRequest()->isPost()) {
            try {
                $itemID = $this->_getParam("item_id", null);
                $wishes = $this->_getParam("wishes", array());

                $service    = AppLib_Service_Factory::getService("menu");
                $price_data = $service->getCustomPrice($itemID, $wishes); 

                $html = "<p>";
                if ($price_data["is_custom"])
                    $html .= FrontLib_Lang::tr("txt_current_price_custom");
                else
                    $html .= FrontLib_Lang::tr("txt_current_price_default");

                $html .= " :&nbsp;<span>{$price_data["price"]},-&nbsp;&euro;</span>";
                $html .= "</p>";

                echo $html;
            }
            catch (Exception $e) {
                echo "An error occured: " . $e->getMessage();
            }
        }
    }

    public function customMealAction()
    {
        $this->view->layout()->disableLayout();

        $mid  = $this->_getParam("mid", null);
        $menu = AppLib_Model_Menu::loadById($mid);

        $service     = AppLib_Service_Factory::getService("menu", array("return_objects" => true));
        $items       = $service->getCustomizationItems($menu->id_menu);

        $this->view->customization_items = $items;
    }
}
