<?php

class AppLib_Service_Menu
    extends AppLib_Service_Abstract
{
    /**
     * getMenus
     *
     * @params array $params
     * @return array
     * @throws AppLib_Service_Fault on application error
     **/
    public function getMenus(array $params = array())
    {
        try {
            /* Configure query */
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "id_menu"         => array("operator" => "in"),
                    "id_restaurant"   => array("operator" => "in"),
                    "title"     => array("operator" => "ilike", "pattern" => "%?%")));

            $conditions = array();

            /* Execute query */
            $menus = AppLib_Model_Menu::getList(array(
                "as_array"   => !$this->getReturnObjects(),
                "parameters" => $sql_params,
                "conditions" => array_merge($filter, $conditions)));

            return $menus;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/getMenus: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/getMenus: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * addMenu
     *
     * @params array $params
     * @return mixed
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addMenu(array $params)
    {
        $this->checkFields($params, array(
            "id_restaurant",
            "title",
            "categories"));

        try {
            $restaurant = AppLib_Model_Restaurant::loadById($params["id_restaurant"]);

            $menu = new AppLib_Model_Menu;
            $menu->id_restaurant = $params["id_restaurant"];
            $menu->title         = $params["title"];

            if (!empty($params["id_menu"]))
                /* Update mode */
                $menu->id_menu = (int) $params["id_menu"];

            $menu->save();

            foreach ($params["categories"] as $cat_row)
                $this->addCategory($menu->id_menu, $cat_row);

            if ($this->getReturnObjects())
                return $menu;

            return $menu->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/addMenu: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/addMenu: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteMenu
     *
     * @params integer  $restaurant_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteMenu($menu_id)
    {
        try {
            $menu = AppLib_Model_Menu::loadById($menu_id);

            $condition  = sprintf("id_menu = %d", $menu_id);
            $menu->delete($condition);
            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/deleteMenu: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/deleteMenu: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getCustomPrice
     *
     * @params integer $item_id
     * @params array   $wishes
     * @return float
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function getCustomPrice($item_id, array $wishes = array())
    {
        try {
            $item = AppLib_Model_Item::loadById($item_id);

            $custom_price = $item->getPrice();
            $customized   = false;
            foreach($wishes as $wid) {
                $wish = AppLib_Model_CustomizationItem::loadById($wid);

                $custom_price += $wish->getPrice();
                $customized    = true;
            }

            return array("is_custom" => $customized,
                         "price"     => $custom_price);
        }
        catch (eVias_ArrayObject_Exception $e) {
            echo $e->getMessage();die;
            throw new AppLib_Service_Fault("menu/getCustomPrice: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/getCustomPrice: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getCustomizationItems
     *
     * @params integer  $restaurant_id
     * @return array
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function getCustomizationItems($menu_id)
    {
        try {
            $menu = AppLib_Model_Menu::loadById($menu_id);

            $items = AppLib_Model_CustomizationItem::getList(array(
                "as_array"   => !$this->getReturnObjects(),
                "conditions" => array("id_menu = {$menu_id}")));

            return $items;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/getCustomizationItems: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/getCustomizationItems: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getCategories
     *
     * @params integer  $menu_id
     * @return array
     * @throws AppLib_Service_Fault on invalid argument
     **/
    public function getCategories($menu_id, array $params)
    {
        try {
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "title" => array("operator" => "ilike", "pattern" => "%?%"),
                    "category_id" => array("name" => "id_category", "operator" => "in")));

            $menu = AppLib_Model_Menu::loadById($menu_id);

            $categories = AppLib_Model_Category::getList(array(
                "as_array"   => (! $this->getReturnObjects()),
                "parameters" => $sql_params + array("mid" => $menu_id),
                "conditions" => array_merge($filter, array("id_menu = :mid")),
            ));

            return $categories;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/getCategories: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/getCategories: '%s'.", $e2->getMessage()));
        }
    }


    /**
     * addCategory
     *
     * @params integer  $menu_id
     * @params array    $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addCategory($menu_id, array $params)
    {
        try {
            $menu = AppLib_Model_Menu::loadById($menu_id);

            $category = new AppLib_Model_Category;
            $category->id_menu = $menu->id_menu;

            if (!empty($params["id_category"]))
                $category->id_category = (int) $params["id_category"];

            if (!empty($params["id_parent_category"]))
                $category->id_parent_category = $params["id_parent_category"];

            $category->title        = $params["title"];
            $category->description  = $params["description"];

            if (isset($category->id_category))
               $category->update();
            else
               $category->save();

            $meals = empty($params["meals"]) ? array() : $params["meals"];
            foreach ($meals as $meal_row) {
                $meal_row["id_category"] = $category->id_category;

                $this->addItem($menu->id_menu, $meal_row);
            }

            if ($this->getReturnObjects())
                return $category;

            return $category->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/addCategory: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/addCategory: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteCategory
     *
     * @params integer  $menu_id
     * @params integer  $category_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteCategory($menu_id, $category_id)
    {
        try {
            $menu      = AppLib_Model_Menu::loadById($menu_id);
            $category  = AppLib_Model_Category::loadById($category_id);

            $condition = sprintf("id_menu = %d and id_category = %d",
                                 $menu_id, $category_id);
            $category->delete($condition);

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/deleteCategory: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/deleteCategory: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getItems
     *
     * @params integer  $menu_id
     * @return array
     * @throws AppLib_Service_Fault on invalid argument
     **/
    public function getItems($menu_id, array $params)
    {
        try {
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "item_id" => array("name" => "id_item", "operator" => "in"),
                    "title"   => array("prefix" => "e_menu_item", "operator" => "ilike", "pattern" => "%?%")));

            $menu = AppLib_Model_Menu::loadById($menu_id);

            $query_params = array(
                "as_array"    => !$this->getReturnObjects(),
                "fields"      => array(
                    "e_menu_item.id_item",
                    "e_menu_item.id_category",
                    "e_menu_item.title",
                    "e_menu_item.small_desc",
                    "e_menu_item.meal_menu_number",
                    "e_menu_item.can_be_customized",
                    "e_menu_item.date_created",
                    "e_menu_item.date_updated"),
                "parameters" => $sql_params,
                "conditions" => array_merge($filter, array(
                                    "e_menu.id_restaurant = " . $menu->id_restaurant,
                                    "e_menu.id_menu = " . $menu->id_menu)),
                "joins"      => array("JOIN e_menu_category category USING (id_category)",
                    "JOIN e_menu USING (id_menu)"),
                "order"      => "e_menu_item.meal_menu_number ASC");
            $items = AppLib_Model_Item::getList($query_params);

            return $items;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/getItems: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/getItems: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * addItem
     *
     * @params integer  $menu_id
     * @params array    $item
     * @return boolean
     * @throws AppLib_Service_Fault on Invalid argument
     **/
    public function addItem($menu_id, array $params)
    {
        $this->checkFields($params, array(
            "title",
            "small_desc",
            "meal_menu_number"));

        try {
            $menu = AppLib_Model_Menu::loadById($menu_id);
            $category = AppLib_Model_Category::loadById($params["id_category"]);

            $item     = new AppLib_Model_Item;
            $item->id_category = $category->id_category;

            if (is_numeric($params["id_item"]))
                /* Update mode. */
                $item->id_item = $params["id_item"];

            $item->title   = $params["title"];
            $item->small_desc = $params["small_desc"];
            $item->meal_menu_number  = (int) $params["meal_menu_number"];
            $item->can_be_customized = !empty($params["can_be_customized"]) ? 't' : 'f';

            if (! empty($params["description"]))
                $item->description = $params["description"];

            if (isset($item->id_item))
                $item->update();
            else
                $item->save();

            if (! empty($params["price"]) && is_numeric($params["price"]))
                $item->setPrice($params["price"]);

            if ($this->getReturnObjects())
                return $item;

            return $item->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/addItem: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/addItem: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteItem
     *
     * @params integer  $menu_id
     * @params integer  $item_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid argument
     **/
    public function deleteItem($menu_id, $item_id)
    {
        try {
            $menu    = AppLib_Model_Menu::loadById($menu_id);
            $item    = AppLib_Model_Item::loadById($item_id);

            $del_sql = "DELETE FROM %s WHERE id_item = :iid";

            /* Delete dependencies */
            $obj->getAdapter()
                ->query(sprintf($del_sql, "e_cart_item"),
                        array("iid" => $item_id));
            $obj->getAdapter()
                ->query(sprintf($del_sql, "price_for_item"),
                        array("iid" => $item_id));

            /* Delete item */
            $obj->delete("id_item = {$item_id}");

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("menu/deleteItem: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("menu/deleteItem: '%s'.", $e2->getMessage()));
        }
    }

}

