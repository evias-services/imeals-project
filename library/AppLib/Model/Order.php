<?php

class AppLib_Model_Order
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_order";
    protected $_pk        = "id_order";
    protected $_sequence  = "e_order_id_order_seq";
    protected $_fields    = array(
        "id_cart",
        "id_customer",
        "id_location",
        "comments",
        "confirmed",
        "deleted",
        "date_created",
        "date_updated",);

    private $_cart       = null;
    private $_customer   = null;
    private $_location   = null;

    public function getCart()
    {
        if (! isset($this->_cart))
            $this->_cart = AppLib_Model_Cart::loadById($this->id_cart);

        return $this->_cart;
    }

    public function getCustomer()
    {
        if (! isset($this->_customer))
            $this->_customer = AppLib_Model_Customer::loadById($this->id_customer);

        return $this->_customer;
    }

    public function getLocation()
    {
        if (! isset($this->_location))
            $this->_location = AppLib_Model_CustomerLocation::loadById($this->id_location);

        return $this->_location;
    }

    public function isSeenBy($rid)
    {
        $sql = "select true from e_order_treatment where id_restaurant = :rid and id_order = :oid";
        $res = $this->getAdapter()->fetchOne($sql, array("rid" => $rid,
                                                         "oid" => $this->id_order));

        return !empty($res);
    }

}
