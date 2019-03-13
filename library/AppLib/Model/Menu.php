<?php

class AppLib_Model_Menu
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_menu";
    protected $_pk        = "id_menu";
    protected $_sequence  = "e_menu_id_menu_seq";
    protected $_fields    = array(
        "id_restaurant",
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

    public function getCategories()
    {
        $sql = "
            SELECT
                category.*
            FROM
                e_menu_category category
            WHERE
                category.id_menu = :mid
        ";

        $rows = $this->getAdapter()
                     ->fetchAll($sql, array("mid" => $this->id_menu));

        $categories = array();
        foreach ($rows as $ix => $row) {
            $category = new AppLib_Model_Category;
            $category->bind($row);

            $categories[] = $category;
        }

        return $categories;
    }
}
