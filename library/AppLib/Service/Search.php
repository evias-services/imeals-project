<?php

class AppLib_Service_Search
    extends AppLib_Service_Abstract
{
    public function query(array $params)
    {
        $this->checkFields($params, array("query"));

        try {
            $query = $params["query"];
            $words = preg_split("/ /", $query);

            /* looking for direct restaurant matches */
            /* XXX should use tags algorithm */
            $restaurants = AppLib_Service_Factory::getService("restaurant", array("return_objects" => true))
                ->getRestaurants(array(
                    "filter" => array(
                        "title"   => $words,
                        "zipcode" => $words,
                        "city"    => $words,
                    ),
                    "filter_type" => "anyof"));

            return $restaurants;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("seach/query: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("search/query: '%s'.", $e2->getMessage()));
        }
    }
}
