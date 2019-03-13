<?php

class AppLib_Service_User
    extends AppLib_Service_Abstract
{

    /**
     * getUsers
     *
     * @params array  $params
     * @return array
     * @throws AppLib_Service_Fault on invalid argument
     **/
    public function getUsers(array $params)
    {
        try {
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "user_id" => array("name" => "id_e_user", "operator" => "in"),
                    "login" => array("operator" => "like", "pattern" => "%?%"),
                    "email" => array("operator" => "ilike", "pattern" => "%?%")));

            $users = AppLib_Model_User::getList(array(
                "as_array"   => (! $this->getReturnObjects()),
                "parameters" => $sql_params,
                "conditions" => $filter
            ));

            return $users;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("user/getUsers: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("user/getUsers: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * addUser
     *
     * @params array    $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addUser(array $params)
    {
        $this->checkFields($params, array("id_acl_role", "login", "email", "realname", "restaurants"));

        $modeNew = empty($params["id_e_user"]);
        if ($modeNew && (empty($params["password"]) || empty($params["password_re"])))
            /* Missing password in addition mode. */
            throw new AppLib_Service_Fault("Missing password parameter.");

        if ($modeNew && empty($params["public_salt"]))
            /* Missing public_salt in addition mode. */
            throw new AppLib_Service_Fault("Missing public_salt parameter.");

        try {
            if ($modeNew && $params["password"] != $params["password_re"])
                throw new eVias_ArrayObject_Exception;

            if (! is_array($params["restaurants"]))
                throw new eVias_ArrayObject_Exception;

            $user = new AppLib_Model_User;
            $user->id_acl_role = $params["id_acl_role"];
            $user->login = $params["login"];
            $user->email = $params["email"];
            $user->realname = $params["realname"];

            if ($modeNew) {
                $user->salt = $user->generateSalt();
                $user->public_salt = $params["public_salt"];
                $user->password    = md5($user->public_salt . $params["password"] . $user->salt);
            }
            else
                $user->id_e_user = $params["id_e_user"];

            if (! $modeNew)
               $user->update();
            else
               $user->save();

            $accessible = array();
            if (! $modeNew)
                foreach ($user->getAccessibleRestaurants() as $restaurant)
                    $accessible[] = $restaurant->id_restaurant;

            $deleted_restaurants = array_diff($accessible, $params["restaurants"]);
            $added_restaurants   = array_diff($params["restaurants"], $accessible);

            /* Process added restaurant_access entries */
            foreach ($added_restaurants as $idx => $rid) {
                $restaurant = AppLib_Model_Restaurant::loadById($rid);

                $access = new AppLib_Model_RestaurantAccess;
                $access->id_restaurant = $rid;
                $access->id_e_user     = $user->getPK();
                $access->save();
            }

            /* Process deleted restaurant_access entries */
            foreach ($deleted_restaurants as $idx => $rid) {
                $restaurant = AppLib_Model_Restaurant::loadById($rid);

                $raccess = new AppLib_Model_RestaurantAccess;
                $raccess->delete("id_restaurant = " . $rid . " and id_e_user = " . $user->getPK());
            }

            if ($this->getReturnObjects())
                return $user;

            return $user->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("user/addUser: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("user/addUser: '%s'.", $e2->getMessage()));
        }
    }

    /**
     * deleteUser
     *
     * @params integer  $user_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteUser($user_id)
    {
        try {
            $user = AppLib_Model_User::loadById($user_id);

            $condition = sprintf("id_e_user = %d", $user_id);
            $user->delete($condition);

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("user/deleteUser: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("user/deleteUser: '%s'.", $e2->getMessage()));
        }
    }


}
