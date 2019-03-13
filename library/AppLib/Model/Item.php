<?php

class AppLib_Model_Item
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_menu_item";
    protected $_pk        = "id_item";
    protected $_sequence  = "e_menu_item_id_item_seq";
    protected $_fields    = array(
        "id_category",
        "title",
        "small_desc",
        "description",
        "meal_menu_number",
        "can_be_customized",
        "date_created",
        "date_updated",);

    private $_category;

    public function getTitle()
    {
        return $this->title;
    }

    public function getSmallDesc()
    {
        return $this->small_desc;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCategory()
    {
        if (! isset($this->_category)) 
            $this->_category = AppLib_Model_Category::loadById($this->id_category);

        return $this->_category;
    }

    public function setPrice($price)
    {
        if (! is_numeric($price))
            throw new InvalidArgumentException;

        $this->getAdapter()
             ->query("
            INSERT INTO e_menu_item_price
                (id_item, price)
            VALUES (:iid, :price)",
            array("iid"     => $this->id_item,
                  "price"   => $price));

        return $this;
    }

    public function getPrice()
    {
        return $this->getAdapter()
                ->fetchOne("
            SELECT 
                price
            FROM
                e_menu_item_price
            WHERE
                id_item = :iid
            ORDER BY date_created DESC
            LIMIT 1",
            array("iid" => $this->id_item));
    }
}
