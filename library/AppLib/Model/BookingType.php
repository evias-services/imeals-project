<?php

class AppLib_Model_BookingType
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_booking_type";
    protected $_pk        = "id_booking_type";
    protected $_sequence  = "e_booking_type_id_booking_type_seq";
    protected $_fields    = array(
        "title",
        "date_created",
        "date_updated",);

    private $_restaurant = null;

    public function getRestaurant()
    {
        if (! isset($this->_restaurant))
            $this->_restaurant = AppLib_Model_Restaurant::loadById($this->id_restaurant);

        return $this->_restaurant;
    }
}