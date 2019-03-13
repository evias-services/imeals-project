<?php

class AppLib_Model_Booking
    extends eVias_ArrayObject_Db
{
    const TYPE_ROOM  = 1;
    const TYPE_TABLE = 2;

    protected $_tableName = "e_booking";
    protected $_pk        = "id_booking";
    protected $_sequence  = "e_booking_id_booking_seq";
    protected $_fields    = array(
        "id_booking_type",
        "id_restaurant",
        "id_customer",
        "id_booked_object",
        "count_people",
        "date_book_start",
        "date_book_end",
        "date_created",
        "date_updated",);

    private $_type       = null;
    private $_restaurant = null;
    private $_customer   = null;
    private $_object     = null;

    public function getType()
    {
        if (! isset($this->_type))
            $this->_type = AppLib_Model_BookingType::loadById($this->id_booking_type);

        return $this->_type;
    }

    public function getRestaurant()
    {
        if (! isset($this->_restaurant))
            $this->_restaurant = AppLib_Model_Restaurant::loadById($this->id_restaurant);

        return $this->_restaurant;
    }

    public function getCustomer()
    {
        if (! isset($this->_customer))
            $this->_customer = AppLib_Model_Customer::loadById($this->id_customer);

        return $this->_customer;
    }

    public function getBookedObject()
    {
        if (! isset($this->_object)) {
            switch ($this->getType()->id_booking_type) {
                default:
                case self::TYPE_TABLE:
                    $this->_object = AppLib_Model_RoomTable::loadById($this->id_booked_object);
                    break;

                case self::TYPE_ROOM:
                    $this->_object = AppLib_Model_Room::loadById($this->id_booked_object);
                    break;
            }
        }

        return $this->_object;
    }
}