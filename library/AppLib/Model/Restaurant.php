<?php

class AppLib_Model_Restaurant
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_restaurant";
    protected $_pk        = "id_restaurant";
    protected $_fields    = array(
        "title",
        "address",
        "zipcode",
        "city",
        "country",
        "phone",
        "email",
        "numtav",
        "date_created",
        "date_updated",);

    static private $_defaults_values = array(
        "id_restaurant" => 1,
        "title"   => "eRestaurant",
        "address" => "742 Evergreen terrace",
        "zipcode" => "95370",
        "city"    => "Springfield, CA",
        "country" => "us",
        "phone"   => "001 555-3223",
        "email"   => "default@e-restaurant.eu",
        "numtav"  => "TVA BE 2988815294");

    static public function getNextId()
    {
        $obj = new self;
        return $obj->getAdapter()
                   ->fetchOne("
            select
                (coalesce(sub.the_max, 0)) + 1
            from (
                select
                    max(id_restaurant) as the_max
                from
                    e_restaurant
            ) sub
        ");
    }

    protected function _preDelete($where)
    {
        /*
         * XXX
         * XXX
db_erestaurant=# select table_name from information_schema.columns where column_name = 'id_restaurant';
      table_name
-----------------------
 e_booking
 e_room
 e_user_session
 e_order
 e_customer
 e_cart
 e_customization_item
 e_menu
 e_restaurant_access
 e_restaurant_settings
         * XXX
         * XXX
         */

        if (empty($where) || !is_string($where))
            throw new InvalidArgumentException("Model_Restaurant::_preDelete: Invalid where statement provided.");

        /* Delete dependencies:
            - e_restaurant_access
         **/
        $access_where = "id_restaurant IN (SELECT id_restaurant FROM e_restaurant WHERE $where)";

        $raccess = new AppLib_Model_RestaurantAccess;
        $raccess->delete($access_where);
    }

    public function insert()
    {
        parent::insert();

        /* JiT creation of admin restaurant_access. */
        $access = new AppLib_Model_RestaurantAccess;
        $access->id_restaurant = $this->id_restaurant;
        $access->id_e_user     = 1;
        $access->acl_role      = "admin";
        $access->save();

        return $this;
    }

    public function setDefaultValues()
    {
        $this->bind(self::$_defaults_values);
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLegalAddress($as_html = true)
    {
        $eol = "<br />";
        if (! $as_html)
            $eol = "\n";

        return sprintf("%s{$eol}%s-%s %s{$eol}Tel. Num.: %s{$eol}%s{$eol}",
            $this->address,
            strtoupper($this->country), $this->zipcode, $this->city,
            $this->phone,
            $this->numtav);
    }

    public function getSettings()
    {
        $rows = $this->getAdapter()->fetchAll("
            SELECT
                setting_field,
                setting_value
            FROM
                e_restaurant_settings
            WHERE
                id_restaurant = :rid
        ", array("rid" => $this->id_restaurant));

        $settings = array();
        if (! empty($rows)) {
            foreach ($rows as $row) {
                $settings[$row["setting_field"]] = $row["setting_value"];
            }
        }
        return $settings;
    }

    public function getSetting($field)
    {
        $row = $this->getAdapter()->fetchOne("
            SELECT
                setting_value
            FROM
                e_restaurant_settings
            WHERE
                id_restaurant = :rid
                AND setting_field = :field
            LIMIT 1
        ", array("rid" => $this->id_restaurant,
                 "field" => $field));

        if (empty($row))
            return "";

        return $row;
    }

    public function saveSetting($field, $value)
    {
        /* First check if setting exists */
        $row = $this->getAdapter()->fetchOne("
            SELECT
                TRUE
            FROM
                e_restaurant_settings
            WHERE
                id_restaurant = :rid
                AND setting_field = :field
                ", array("rid" => $this->id_restaurant,
                         "field" => $field));

        if ($row) {
            $this->getAdapter()->query("
               UPDATE e_restaurant_settings
               SET setting_value = :new_value
               WHERE
                   id_restaurant = :rid
                   AND setting_field = :field",
               array("new_value" => $value,
                     "rid" => $this->id_restaurant,
                     "field" => $field));
        }
        else {
            $this->getAdapter()->query("
                INSERT INTO e_restaurant_settings
                    (id_restaurant, setting_field, setting_value)
                VALUES
                    (:rid, :field, :value)",
                array(
                    "rid"   => $this->id_restaurant,
                    "field" => $field,
                    "value" => $value));
        }
    }

    public function getMenu()
    {
        $sql = "
            SELECT
                menu.*
            FROM
                e_menu menu
            WHERE
                id_restaurant = :rid
            ORDER BY date_created DESC
            LIMIT 1
        ";

        $row  = $this->getAdapter()->fetchRow($sql, array("rid" => $this->id_restaurant));

        if (false === $row) {
            /* JiT menu creation */
            $this->getAdapter()->query("
                INSERT INTO e_menu
                    (id_menu, id_restaurant, title)
                VALUES ((select coalesce(sub.cnt_menu + 1, 1) from (select count(id_menu) as cnt_menu from e_menu) as sub),
                        :rid, :title)", array(
                "rid" => $this->id_restaurant,
                "title" => $this->getTitle() . " Menu"));

            $row  = $this->getAdapter()->fetchRow($sql, array("rid" => $this->id_restaurant));
        }

        $menu = new AppLib_Model_Menu;
        $menu->bind($row);

        return $menu;
    }
}
