<?php

class AppLib_Service_AccessControl
    extends AppLib_Service_Abstract
{
    public function listRules(array $params)
    {
        try {
            list($filter, $sql_params) = $this->getFilter($params["filter"], array(
                    "id_acl_config"   => array("operator" => "in"),
                    "id_acl_role"     => array("operator" => "="),
                    "id_acl_action"   => array("operator" => "="),
                    "module"     => array("prefix" => "e_acl_resource", "operator" => "ilike", "pattern" => "?%"),
                    "controller" => array("prefix" => "e_acl_resource", "operator" => "ilike", "pattern" => "?%"),
                    "action"     => array("prefix" => "e_acl_resource", "operator" => "ilike", "pattern" => "?%")));

            $acl_configs = AppLib_Model_AclConfig::getList(array(
                "as_array"   => (! $this->getReturnObjects()),
                "parameters" => $sql_params,
                "conditions" => $filter,
                "alias"      => "cf",
                "fields"     => array(
                    "cf.id_acl_config",
                    "cf.id_acl_resource",
                    "cf.id_acl_action",
                    "cf.id_acl_role",
                    "cf.is_allowed",
                    "cf.date_created",
                    "cf.date_updated"),
                "joins"      => array(
                    "JOIN e_acl_resource USING (id_acl_resource)")
            ));

            return $acl_configs;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("acl/listRules: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("acl/listRules: '%s'.", $e2->getMessage()));
        }
    }

    public function listActions(array $params)
    {
        try {
            $pairs = array();

            $obj = new AppLib_Model_AclConfig;
            foreach ( $obj->getAdapter()->fetchAll("
                SELECT
                    id_acl_action,
                    title
                FROM
                    e_acl_action
            ") as $idx => $row) {
                $pairs[$row["id_acl_action"]] = $row["title"];
            }

            return $pairs;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("acl/listActions: Invalid arguments provided.");
        }
        catch (Exception $e) {
            throw new AppLib_Service_Fault(sprintf("acl/listActions: '%s'.", $e->getMessage()));
        }
    }

    public function listRoles(array $params)
    {
        try {
            $pairs = array();

            $obj = new AppLib_Model_AclConfig;
            foreach ( $obj->getAdapter()->fetchAll("
                SELECT
                    id_acl_role,
                    title
                FROM
                    e_acl_role
            ") as $idx => $row) {
                $pairs[$row["id_acl_role"]] = $row["title"];
            }

            return $pairs;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("acl/listRoles: Invalid arguments provided.");
        }
        catch (Exception $e) {
            throw new AppLib_Service_Fault(sprintf("acl/listRoles: '%s'.", $e->getMessage()));
        }
    }

    public function listResources(array $params)
    {
        try {
            $pairs = array();

            $obj = new AppLib_Model_AclConfig;
            foreach ( $obj->getAdapter()->fetchAll("
                SELECT
                    id_acl_resource,
                    '/' || module || '/' || controller || '/' || action as uri
                FROM
                    e_acl_resource
            ") as $idx => $row) {
                $pairs[$row["id_acl_resource"]] = $row["uri"];
            }

            return $pairs;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("acl/listResources: Invalid arguments provided.");
        }
        catch (Exception $e) {
            throw new AppLib_Service_Fault(sprintf("acl/listResources: '%s'.", $e->getMessage()));
        }
    }

    /**
     * deleteRule
     *
     * @params integer  $rule_id
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function deleteRule($rule_id)
    {
        try {
            $rule = AppLib_Model_AclConfig::loadById($rule_id);

            $condition = sprintf("id_acl_config = %d", $rule_id);
            $rule->delete($condition);

            return true;
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("acl/deleteRule: Invalid arguments provided.");
        }
        catch (Exception $e) {
            throw new AppLib_Service_Fault(sprintf("acl/deleteRule: '%s'.", $e->getMessage()));
        }
    }

    /**
     * addRule
     *
     * @params array    $params
     * @return boolean
     * @throws AppLib_Service_Fault on invalid arguments
     **/
    public function addRule(array $params)
    {
        $this->checkFields($params, array(
            "id_acl_resource",
            "id_acl_role",
            "id_acl_action",
            "is_allowed"));

        try {
            $rule = new AppLib_Model_AclConfig;
            $rule->id_acl_resource  = $params["id_acl_resource"];
            $rule->id_acl_role      = $params["id_acl_role"];
            $rule->id_acl_action    = $params["id_acl_action"];
            $rule->is_allowed       = empty($params["is_allowed"]) ? 'f' : 't';

            if (empty($params["id_acl_config"]))
                $rule->save();
            else {
                $rule->id_acl_config = $params["id_acl_config"];
                $rule->update();
            }

            if ($this->getReturnObjects())
                return $rule;

            return $rule->getArrayCopy();
        }
        catch (eVias_ArrayObject_Exception $e) {
            throw new AppLib_Service_Fault("acl/addRule: Invalid arguments provided.");
        }
        catch (Exception $e2) {
            throw new AppLib_Service_Fault(sprintf("acl/addRule: '%s'.", $e2->getMessage()));
        }
    }
}