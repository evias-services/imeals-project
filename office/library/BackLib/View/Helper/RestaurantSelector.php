<?php

class BackLib_View_Helper_RestaurantSelector
    extends Zend_View_Helper_Abstract
{
    public function restaurantSelector()
    {
        try {
            $restaurant  = Zend_Registry::get("restaurant");
        }
        catch (Zend_Exception $e) {
            $id_user  = Zend_Auth::getInstance()->hasIdentity() ? Zend_Auth::getInstance()->getIdentity()->id_e_user : 0;
            $accesses = AppLib_Model_RestaurantAccess::loadFirstByUserId($id_user);

            if (false == $accesses) {
                $restaurant = null;
                return "<span>No restaurant currently selected.</span>";
            }

            $id_restaurant = $accesses->id_restaurant;
            $session = new Zend_Session_Namespace("e-restaurant.current" . $id_user);
            if (null !== $session->rid)
                $id_restaurant = $session->rid;

            $restaurant = AppLib_Model_Restaurant::loadById($id_restaurant);
        }

        $restaurants = AppLib_Model_Restaurant::getList(array());
        $baseUrl     = Zend_Controller_Front::getInstance()->getBaseUrl();

        $html = "<form action='$baseUrl/manage/restaurant/set-current' name='set-current-restaurant' method='post'>";
        $html .= "<strong>Restaurant:</strong> ";
        $html .= "<select onchange='document.forms[\"set-current-restaurant\"].submit();' id='restaurant_selector' name='current_restaurant'>";
        $html .= "<option value=''>Please select a restaurant here...</option>";
        foreach ($restaurants as $restaurant_entry) {
            $label    = $restaurant_entry->title;
            $value    = $restaurant_entry->id_restaurant;
            $location = sprintf("%s %s: %s",
                                    $restaurant_entry->zipcode,
                                    strtoupper($restaurant_entry->city),
                                    $restaurant_entry->address);

            $selected = "";
            if (null !== $restaurant
                && $restaurant->id_restaurant == $restaurant_entry->id_restaurant)
                $selected = " selected='selected'";

            $html .= "<option{$selected} value='{$value}'>{$label}: {$location}</option>";
        }
        $html .= "</select>";
        $html .= "</form>";
        return $html;
    }
}

