<?php

class AppLib_Model_Category
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_menu_category";
    protected $_pk        = "id_category";
    protected $_sequence  = "e_menu_category_id_category_seq";
    protected $_fields    = array(
        "id_parent_category",
        "id_menu",
        "title",
        "description",
        "date_created",
        "date_updated",);

    private $_parent = null;
    private $_menu   = null;

    static public function getCount(array $params)
    {
        return count(self::getList(array_diff_key($params, array_flip(array("limit", "offset")))));
    }

    public function getParent()
    {
        if (! isset($this->_parent) && null != $this->id_parent_category)
            $this->_parent = AppLib_Model_Category::loadById($this->id_parent_category);

        return $this->_parent;
    }

    public function getMenu()
    {
        if (! isset($this->_menu) && null != $this->id_menu)
            $this->_menu = AppLib_Model_Menu::loadById($this->id_menu);

        return $this->_menu;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getItems()
    {
        $obj    = new AppLib_Model_Item;
        $fields = implode(", ", $obj->fieldNames());

        $sql = "
            SELECT
                $fields
            FROM
                e_menu_item
            WHERE
                id_category = :cat_id
            ORDER BY 
                meal_menu_number ASC
        ";

        $rows = $this->getAdapter()->fetchAll($sql, array('cat_id' => $this->id_category));
        if (empty($rows))
            return array();

        $items = array();
        foreach ($rows as $row) {
            $item = new AppLib_Model_Item;
            $item->bind($row);

            $items[$item->id_item] = $item;
        }
        
        return $items;
    }

}
