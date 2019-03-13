<?php

class AppLib_Model_RestaurantAccess
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_restaurant_access";
    protected $_pk        = "id_restaurant_access";
    protected $_sequence  = "e_restaurant_access_id_restaurant_access_seq";
    protected $_fields    = array(
        "id_restaurant",
        "id_e_user",
        "date_created",
        "date_updated",);

    static public function loadByRestaurantIdAndUserId($restaurant_id, $user_id)
    {
        if (!is_numeric($restaurant_id) || !is_numeric($user_id))
            throw new AppLib_Model_Exception("Invalid arguments provided for RestaurantAccess load.");

        $rows = self::getList(array(
            "conditions" => array("id_restaurant = " . $restaurant_id,
                                  "id_e_user = " . $user_id)));
        return !empty($rows) ? $rows[0] : false;
    }

    static public function loadFirstByUserId($user_id)
    {
        if (!is_numeric($user_id))
            throw new AppLib_Model_Exception("Invalid arguments provided for RestaurantAccess load.");

        $rows = self::getList(array(
            "order"      => "date_created DESC",
            "conditions" => array("id_e_user = " . $user_id)));

        return !empty($rows) ? $rows[0] : false;
    }
}

