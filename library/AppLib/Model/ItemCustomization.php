<?php

class AppLib_Model_ItemCustomization
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_cart_item_customization";
    protected $_pk        = "id_item_customization";
    protected $_sequence  = "e_cart_item_customization_id_item_customization_seq";
    protected $_fields    = array(
        "id_item",
        "id_cart",
        "index",
        "id_customization_item",
        "operator",);

    public function getCustom()
    {
        return AppLib_Model_CustomizationItem::loadById($this->id_customization_item);
    }

    static public function loadByItemAndCart($item_id, $cart_id)
    {
        $obj    = new self;
        $fields = implode(", ", array_map(function($i) { return "cm." . $i; }, $obj->fieldNames()));
        $sql    = "
            select
                $fields  
            from 
                e_cart_item_customization cm
                join e_menu_item i using (id_item)
                join e_cart c on (c.id_cart = cm.id_cart)
            where
                cm.id_item = :iid
                and cm.id_cart = :cid
        ";
        $rows = $obj->getAdapter()
                   ->fetchAll($sql, array("iid" => $item_id,
                                          "cid" => $cart_id));

        $objs = array();
        foreach ($rows as $ix => $row) :
            unset($obj);
            $obj = new self;
            $obj->bind($row);

            if (! isset($objs[$obj->index]))
                $objs[$obj->index] = array();

            if (! isset($objs[$obj->index][$obj->operator]))
                $objs[$obj->index][$obj->operator] = array();

            $objs[$obj->index][$obj->operator][] = $obj;
        endforeach;

        return $objs;
    }
}
