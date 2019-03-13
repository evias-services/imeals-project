<?php

class AppLib_Model_CustomizationItem
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_customization_item";
    protected $_pk        = "id_customization_item";
    protected $_sequence  = "e_customization_item_id_customization_item_seq";
    protected $_fields    = array(
        "id_menu",
        "title",
        "price",
        "date_created",
        "date_updated",);

    public function getTitle()
    {
        return $this->title;
    }

    public function getPrice()
    {
        return $this->price;
    }

}
