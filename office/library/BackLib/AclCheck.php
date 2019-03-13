<?php

class BackLib_AclCheck
{
    const ROLE_ADMIN   = 1;
    const ROLE_MANAGER = 2;
    const ROLE_USER    = 3;
    const ROLE_GUEST   = 4;

    const ACTION_READ   = 1;
    const ACTION_WRITE  = 2;
    const ACTION_DELETE = 4;

    private $_identity   = null;
    private $_module     = null;
    private $_controller = null;
    private $_action     = null;

    public function __construct(array $opts = array())
    {
        $this->setOptions($opts);
    }

    public function setOptions(array $opts)
    {
        foreach ($opts as $option => $value) {
            $method = "set" . str_replace(" ", "", ucwords(str_replace("_", " ", $option)));
            if (method_exists($this, $method))
                $this->$method($value);
        }

        return $this;
    }

    public function getRoleId()
    {
        $role_id = BackLib_AclCheck::ROLE_GUEST;
        if (is_object($this->_identity))
            $role_id  = AppLib_Model_AclConfig::getRoleIdByLogin($this->_identity->login);

        return $role_id;
    }

    public function checkPermission($a_id = BackLib_AclCheck::ACTION_READ)
    {
        $module     = $this->_module;
        $controller = $this->_controller;
        $action     = $this->_action;

        if (empty($action))
            $this->_action = "index";

        $obj        = new AppLib_Model_AclConfig;
        $res_id     = $obj->getAdapter()->fetchOne("
            select
                id_acl_resource
            from
                e_acl_resource
            where
                module = :module
                and controller = :ctrl
                and action = :action
        ", array(
            "module" => $this->_module,
            "ctrl"   => $this->_controller,
            "action" => $this->_action));

        if (false === $res_id)
            /* Access to current module/controller/action combination
             * is not restricted in the ACL. */
            return true;

        $sql = "
            select
                is_allowed
            from
                e_acl_config
            where
                id_acl_role = :role_id
                and id_acl_resource = :res_id
                and id_acl_action = :action_id
        ";

        $result = $obj->getAdapter()->fetchOne($sql, array(
            "role_id"   => $this->getRoleId(),
            "res_id"    => $res_id,
            "action_id" => BackLib_AclCheck::ACTION_READ));

        return $result;
    }

    public function setIdentity($m)
    {
        $this->_identity = $m;
    }

    public function getIdentity()
    {
        return $this->_identity;
    }

    public function setModule($m)
    {
        $this->_module = $m;
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function setController($m)
    {
        $this->_controller = $m;
    }

    public function getController()
    {
        return $this->_controller;
    }

    public function setAction($m)
    {
        $this->_action = $m;
    }

    public function getAction()
    {
        return $this->_action;
    }

}