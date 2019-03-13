<?php

class BackLib_Controller_Plugin_Restaurant
    extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch( Zend_Controller_Request_Abstract $request )
    {
        /* usually a user will have access to one restaurant only ;
           managers may have access to several restaurants. (admin has all)*/

        $renderer   = Zend_Controller_Action_HelperBroker::getStaticHelper("viewRenderer");
        $baseUrl    = $renderer->view->baseUrl();

        if ("default" == $request->getModuleName()
            && "index" == $request->getControllerName()
            && in_array($request->getActionName(), array("index", "denied")))
            return ;

        if (! Zend_Auth::getInstance()->hasIdentity())
            return ;

        $identity = Zend_Auth::getInstance()->getIdentity();
        $session  = new Zend_Session_Namespace("e-restaurant.current" . $identity->id_e_user);
        try {
            Zend_Registry::get("restaurant");

            /* Objects loaded already. */
        }
        catch (Zend_Exception $e) {

            $id_user       = $identity->id_e_user;
            $id_restaurant = 1;

            $restaurant = AppLib_Model_RestaurantAccess::loadFirstByUserId($id_user);
            if (false !== $restaurant)
                /* Default selected restaurant is the first accessible. */
                $id_restaurant = $restaurant->id_restaurant;

            if (isset($session->rid))
                /* Session already present, may be a different restaurant. */
                $id_restaurant = $session->rid;

            try {
                $restaurant = AppLib_Model_Restaurant::loadById($id_restaurant);
                $access     = AppLib_Model_RestaurantAccess::loadByRestaurantIdAndUserId($id_restaurant, $id_user);

                if (false === $access) {
                    /* No access for given restaurant and current user.
                     * Need to clean session. */
                    $session->rid = null;

                    $accesses = AppLib_Model_RestaurantAccess::loadFirstByUserId($id_user);
                    if (false !== $accesses)
                        /* run preDispatch once more in order to
                         * set the current restaurant. (seems like session was broken)*/
                        return $this->preDispatch($request);
                    else {
                        /* Access denied. (no restaurant_access entry found) */
                        $this->getResponse()
                             ->setRedirect("$baseUrl/default/index/denied")
                             ->sendResponse();
                        return;
                    }
                }

                /* set registry and update session */
                Zend_Registry::set("restaurant", $restaurant);
                $session->rid = $restaurant->id_restaurant;
            }
            catch (Exception $e) {
                throw new BackLib_Exception(sprintf("No restaurant could be set in registry: '%s'", $e->getMessage()));
            }
        }
    }

}

