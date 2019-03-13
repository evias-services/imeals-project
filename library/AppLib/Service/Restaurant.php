<?php

class AppLib_Service_Restaurant
    extends AppLib_Service_Abstract
{
    /**
     * getRestaurants
     *
     * @params array $params
     * @return array
     * @throws AppLib_Service_Fault on application error
     **/
    public function getRestaurants(array $params = array())
    {
        try {
            /* Configure query */
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "id_restaurant" => array("operator" => "in"),
                    "title"         => array("operator" => "ilike", "pattern" => "%?%"),
                    "address"       => array("operator" => "ilike", "pattern" => "%?%"),
                    "zipcode"       => array("operator" => "ilike", "pattern" => "%?%"),
                    "city"          => array("operator" => "ilike", "pattern" => "%?%")));

            if (isset($params["filter_type"]) && $params["filter_type"] == "anyof") {
                /* Pre-format filter to give 1 condition. (or logic for filters..) */
                $filter = array("( " . implode(" OR ", $filter) . " ) ");
            }


            /* XXX
               add triangulation algorithm implementation
               for geolocalized searches and results.
               XXX */

            $conditions = array();

            /* Execute query */
            $restaurants = AppLib_Model_Restaurant::getList(array(
                "as_array"   => !$this->getReturnObjects(),
                "parameters" => $sql_params,
                "conditions" => array_merge($filter, $conditions)));

            return $restaurants;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("restaurant/getRestaurants: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("restaurant/getRestaurants: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * editRestaurant
     *
     * @params array $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function editRestaurant(array $params)
    {
        $this->checkFields($params, array(
            "title",
            "address",
            "zipcode",
            "city",
            "country",
            "phone",
            "email",
            "numtav"));

        try {
            $restaurant = new AppLib_Model_Restaurant;
            $restaurant->title = $params['title'];
            $restaurant->address = $params['address'];
            $restaurant->zipcode = $params['zipcode'];
            $restaurant->city    = $params['city'];
            $restaurant->country = $params['country'];
            $restaurant->phone   = $params['phone'];
            $restaurant->email   = $params['email'];
            $restaurant->numtav  = $params['numtav'];

            $isUpdate = !empty($params["id_restaurant"]) && is_numeric($params["id_restaurant"]);

            if ($isUpdate) {
                $restaurant->id_restaurant = (int) $params["id_restaurant"];
                $restaurant->update();
            }
            else {
            /* no sequence set on e_restaurant ORM, pk must be set manually. */
                $restaurant->id_restaurant = AppLib_Model_Restaurant::getNextId();
                $restaurant->insert();
            }

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("restaurant/editRestaurant: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("restaurant/editRestaurant: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteRestaurant
     *
     * @params integer  $restaurant_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteRestaurant($restaurant_id)
    {
        try {
            $restaurant = AppLib_Model_Restaurant::loadById($restaurant_id);

            $condition  = sprintf("id_restaurant = %d", $restaurant_id);
            $restaurant->delete($condition);
            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("restaurant/deleteRestaurant: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("restaurant/deleteRestaurant: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * saveSettings
     *
     * @params integer  $restaurant_id
     * @params array    $params
     * @return boolean
     **/
    public function saveSettings($restaurant_id, array $settings)
    {
        try {
            $restaurant = AppLib_Model_Restaurant::loadById($restaurant_id);

            foreach ($settings as $setting_field => $setting_value)
                if (null !== $setting_value)
                    $restaurant->saveSetting($setting_field, $setting_value);

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("restaurant/saveSettings: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("restaurant/saveSettings: '%s'.", $e2->getMessage()));
        }
    }

}
