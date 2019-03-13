<?php

class AppLib_Service_Order
    extends AppLib_Service_Abstract
{
    /**
     * getLastOrder
     *
     * @params integer $restaurant_id
     * @return array
     * @throws AppLib_Service_Fault on application error
     **/
    public function getLastOrder($restaurant_id)
    {
        try {
            $orders = $this->getOrders($restaurant_id, array(
               "filter" => array(
                   "confirmed" => true,
                   "deleted"   => false)));

            return $orders[0];
        }
        catch (Exception $e) {
            throw new AppLib_Service_Fault("order/getLastOrder: " . $e->getMessage());
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("order/getLastOrder: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * getUnseenOrders
     *
     * @params integer  $restaurant_id
     * @return array
     * @throws AppLib_Service_Fault on invalid argument
     */
    public function getOrders($restaurant_id, array $params = array())
    {
        try {
            $filter = $params["filter"];

            $restaurant = AppLib_Model_Restaurant::loadById($restaurant_id);

            $object = new AppLib_Model_Order;

            $confirmed_condition = "o.confirmed = TRUE";
            $deleted_condition   = "o.deleted = FALSE";
            /* default is both 'seen' and 'unseen' */
            $seen_condition      = "TRUE";
            $ids_condition       = "TRUE";

            /* Analyse filter */
            $filter = isset($params["filter"]) ? $params["filter"] : array();
            if (isset($filter["confirmed"]) && ! $filter["confirmed"])
                $confirmed_condition = "o.confirmed = FALSE";

            if (isset($filter["deleted"]) && $filter["deleted"])
                $deleted_condition = "o.deleted = TRUE";

            if (isset($filter["seen"])) {
                $operator = $filter["seen"] ? "exists" : "not exists";
                $seen_condition = "$operator (select true from e_order_treatment where id_restaurant = " . $restaurant_id. "
                                                                                and id_order = o.id_order)";
            }

            if (isset($filter["ids"]))
                $ids_condition = "o.id_order IN (" . implode(", ", $filter["ids"]). ")";


            /* Execute query */
            $orders = AppLib_Model_Order::getList(array(
                "as_array"      => !$this->getReturnObjects(),
                "fields"        => array(
                    "o.id_order",
                    "o.id_cart",
                    "o.id_customer",
                    "o.id_location",
                    "o.confirmed",
                    "o.deleted",
                    "o.date_created",
                    "o.date_updated",
                    "c.realname",
                    "c.phone",
                    "c.email",
                    "cl.address",
                    "cl.zipcode",
                    "cl.city",
                    "cl.country",),
                "alias"         => "o",
                "joins"         => array(
                    "JOIN e_customer c USING (id_customer)",
                    "JOIN e_customer_location cl USING (id_location)",
                    "LEFT JOIN (
                        select
                            e_cart.id_cart,
                            count(e_cart_item.id_cart_item) as counter
                        from e_cart
                             join e_cart_item using (id_cart)
                             join e_menu_item using (id_item)
                        where
                            e_menu_item.id_category IN (select id_category from e_menu_category join e_menu using (id_menu) where id_restaurant =  " . $restaurant_id . ")
                        group by
                            e_cart.id_cart
                     ) sub on (o.id_cart = sub.id_cart)",),
                "conditions"    => array(
                    "sub.id_cart is not null",
                    $confirmed_condition,
                    $deleted_condition,
                    $seen_condition,
                    $ids_condition),
                "order"         => "o.date_created DESC"));

            return $orders;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("order/getOrders: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("order/getOrders: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteOrder
     *
     * @params integer $restaurant_id
     * @params integer $order_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     */
    public function deleteOrder($restaurant_id, $order_id)
    {
        try {
            /* XXX e_archived_order */

            $restaurant = AppLib_Model_Restaurant::loadById($restaurant_id);
            $order      = AppLib_Model_Order::loadById($order_id);

            unset($restaurant);

            $sql = "
                UPDATE
                    e_order
                SET
                    deleted = TRUE,
                    date_updated = now()
                WHERE
                    id_order = :oid
            ";

            $order->getAdapter()
                  ->query($sql, array("oid" => $order->id_order));

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("order/deleteOrder: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("order/deleteOrder: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * createCustomer
     *
     * @params integer  $cart_id
     * @params array    $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid argument
     **/
    public function createOrder($cart_id, array $params)
    {
        $this->checkFields($params, array("name", "zipcode", "city", "address", "country"));

        try {
            $cart = AppLib_Model_Cart::loadById($cart_id);

            $customer = null;
            if (isset($params["customer_id"]))
                /* Load existing customer */
                $customer = AppLib_Model_Customer::loadById($params["customer_id"]);

            $location = null;
            if (isset($params["location_id"]))
                /* Load existing location */
                $location = AppLib_Model_CustomerLocation::loadById($params["location_id"]);

            if (null === $location
                && !isset($params["location_title"]))
                $params["location_title"] = "default";

            if (null === $customer) {
                /* Save new customer */
                $customer = new AppLib_Model_Customer;
                $customer->realname      = trim($params["name"]);

                if (! empty($params["phone"]))
                    $customer->phone = $params["phone"];

                if (! empty($params["email"]))
                    $customer->email = $params["email"];

                $customer->save();
            }

            if (null === $location) {
                /* Save new customer_location */
                $location = new AppLib_Model_CustomerLocation;
                $location->id_customer = $customer->getPK();
                $location->title       = $params["location_title"];
                $location->address     = $params["address"];
                $location->zipcode     = $params["zipcode"];
                $location->city        = $params["city"];
                $location->country     = $params["country"];

                if (! empty($params["comment"]))
                    $location->comments = $params["comment"];

                $location->save();
            }

            /* And finally save un-confirmed order. */
            $order = new AppLib_Model_Order;
            $order->id_cart       = $cart_id;
            $order->id_customer   = $customer->getPK();
            $order->id_location   = $location->getPK();

            if (! empty($params["comment"]))
                $order->comments = $params["comment"];

            $order->save();

            if ($this->getReturnObjects())
                return $order;

            return $order->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("order/createOrder: Invalid arguments provided");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("order/createOrder: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * setConfirmed
     *
     * @params integer $order_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid argument
     **/
    public function setConfirmed($order_id)
    {
        try {
            /* Validate argument. */
            $order = AppLib_Model_Order::loadById($order_id);

            /* Set confirmed to true on order */
            $sql = "
                UPDATE
                    e_order
                SET
                    confirmed = true,
                    date_created = now(),
                    date_updated = now()
                WHERE
                    id_order = :oid
            ";
            $params = array(
                "oid" => $order_id);

            $obj = new AppLib_Model_Order;
            $obj->getAdapter()
                ->query($sql, $params);

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("order/setConfirmed: Invalid arguments provided");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("order/setConfirmed: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * setSeen
     *
     * @params integer $restaurant_id
     * @params integer $order_id
     * @return AppLib_Model_Order | array
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function setSeen($restaurant_id, $order_id)
    {
        try {
            /* Parameters validation */
            $restaurant = AppLib_Model_Restaurant::loadById($restaurant_id);
            $order      = AppLib_Model_Order::loadById($order_id);

            $esql   = "select true from e_order_treatment where id_restaurant = :rid and id_order = :oid";
            $exists = $order->getAdapter()
                            ->fetchOne($esql, array("rid" => $restaurant->id_restaurant,
                                                    "oid" => $order->id_order));

            if (empty($exists)) {

                $sql = "
                insert into e_order_treatment
                  (id_restaurant, id_order)
                values
                  (:rid, :oid)
                ";

                /* insert treatment. */
                $order->getAdapter()
                      ->query($sql, array("rid" => $restaurant->id_restaurant, "oid" => $order->id_order));
            }

            if ($this->getReturnObjects())
                return $order;

            return $order->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("order/setSeen: Invalid arguments provided");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("order/setSeen: '%s'.", $e2->getMessage()));
        }
    }
}
