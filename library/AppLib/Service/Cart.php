<?php

class AppLib_Service_Cart
    extends AppLib_Service_Abstract
{
    /**
     * removeItem
     *
     * @params integer $cart_id
     * @params integer $item_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function removeItem($cart_id, $item_id)
    {
        try {
            $cart = AppLib_Model_Cart::loadById($cart_id);
            $item = AppLib_Model_Item::loadById($item_id);

            $cart->rmCustoms($item);
            $cart->rmItem($item);

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("cart/removeItem: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("cart/removeItem: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * addItem
     *
     * @params integer $cart_id
     * @params integer $item_id
     * @params array   $wishes  example of wishes array: array('add' => array(5, 15, 7),
     *                                                         'del' => array(8))
     *                          This example would add 4 item_customizations
     *                          entries, 3 with operator '+' and 1 with operator '-'.
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addItem($cart_id, $item_id, array $wishes = array())
    {
        if (! empty($wishes) && ! isset($wishes["add"]) && ! isset($wishes["del"]))
            throw new AppLib_Service_Fault("cart/addItem: Invalid wishes argument: 'add' or 'del' offset missing in filled array.");

        try {
            /* Validate & Fetch cart and item objects */
            try {
                $cart = AppLib_Model_Cart::loadById($cart_id);
                $item = AppLib_Model_Item::loadById($item_id);
            }
            catch (eVias_ArrayObject_Exception $e) {
                throw new AppLib_Service_Fault("cart/addItem: Invalid cart_id or item_id argument provided.");
            }

            /* Define custom-meal index in cart. (meal-index) */
            $quantities = $cart->getQuantities();
            $index      = 0;
            if (isset($quantities[$item->id_item])
                && $quantities[$item->id_item] >= 1)
                $index  = $quantities[$item->id_item];

            /* Insert item_customizations from wishes array */
            foreach ($wishes as $op_name => $wishes_by_op) {
                $operator = $op_name == 'add' ? '+' : '-';
                foreach ($wishes_by_op as $ix => $wish_item_id) {
                    // make sure id is valid.
                    $wish = AppLib_Model_CustomizationItem::loadById($wish_item_id);

                    $meal = new AppLib_Model_ItemCustomization;
                    $meal->id_item     = $item->getPK();
                    $meal->id_cart     = $cart->getPK();
                    $meal->index       = $index;
                    $meal->id_customization_item = $wish_item_id;
                    $meal->operator    = $operator;
                    $meal->save();
                }
            }

            /* Add item to cart row */
            $cart->addItem($item);

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("cart/addItem: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("cart/addItem: '%s'.", $e2->getMessage()));
        }
    }

}

