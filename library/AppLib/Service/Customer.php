<?php

class AppLib_Service_Customer
    extends AppLib_Service_Abstract
{
    /**
     * getCustomers
     *
     * @params array $params
     * @return array
     * throws AppLib_Service_Fault on invalid arguments
     **/
    public function getCustomers(array $params = array())
    {
        $this->checkFields($params, array("id_restaurant"));

        try {
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "realname" => array("operator" => "ilike", "pattern" => "%?%"),
                    "phone" => array("operator" => "like", "pattern" => "?%"),
                    "email" => array("operator" => "ilike", "pattern" => "%?%")));

            if (isset($params["filter_type"]) && $params["filter_type"] == "anyof"
                && !empty($filter)) {
                /* Pre-format filter to give 1 condition. */
                $filter = array("( " . implode(" OR ", $filter) . " ) ");
            }

            $restaurant = AppLib_Model_Restaurant::loadById($params["id_restaurant"]);

            $customers  = AppLib_Model_Customer::getList(array(
                "as_array"   => !$this->getReturnObjects(),
                "parameters" => $sql_params,
                "order"      => "date_created DESC",
                "fields"     => array(
                    "id_customer",
                    "realname",
                    "phone",
                    "email",
                    "coalesce(sub.counter, 0)"),
                "joins"      => array(
                    "left join (
                        select
                            id_customer,
                            count(*) as counter
                        from
                            e_order
                        where
                            confirmed
                        group by
                            id_customer
                    ) sub using (id_customer)"
                ),
                "conditions" => array_merge($filter, array(
                    sprintf("id_restaurant = %d", $restaurant->id_restaurant)))
            ));

            return $customers;
        }
        catch (eVias_ArrayObject_Exception $e2) {
            throw new AppLib_Service_Fault("customer/getCustomers: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("customer/getCustomers: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * editCustomer
     *
     * @params integer  $restaurant_id
     * @params array    $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     */
    public function editCustomer($restaurant_id, array $params)
    {
        $this->checkFields($params, array("name"));
        try {
            $restaurant = AppLib_Model_Restaurant::loadById($restaurant_id);

            if (isset($params["id_customer"]))
                /* Validate */
                AppLib_Model_Customer::loadById($params["id_customer"]);

            $customer = new AppLib_Model_Customer;
            $customer->id_restaurant = $restaurant->id_restaurant;
            $customer->realname      = trim($params["name"]);

            if (! empty($params["id_customer"]))
                $customer->id_customer = $params["id_customer"];

            if (! empty($params["phone"]))
                $customer->phone = $params["phone"];

            if (! empty($params["email"]))
                $customer->email = $params["email"];

            $customer->save();

            if ($this->getReturnObjects())
                return $customer;

            return $customer->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("customer/editCustomer: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("customer/editCustomer: '%s'.", $e2->getMessage()));
        }
    }
}

