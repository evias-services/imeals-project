<?php

class AppLib_Model_AclConfig
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_acl_config";
    protected $_pk        = "id_acl_config";
    protected $_sequence  = "e_acl_config_id_acl_config_seq";
    protected $_fields    = array(
        "id_acl_resource",
        "id_acl_action",
        "id_acl_role",
        "is_allowed",
        "date_created",
        "date_updated",);

    private $_resource  = null;
    private $_roleLabel = null;

    public function getResource()
    {
        if (null === $this->_resource)
            $this->_resource = AppLib_Model_AclResource::loadById($this->id_acl_resource);

        return $this->_resource;
    }

    public function getRoleLabel()
    {
        if (null === $this->_roleLabel)
            $this->_roleLabel = $this->getAdapter()->fetchOne("
                SELECT
                    title
                FROM
                    e_acl_role
                WHERE
                    id_acl_role = :rid", array("rid" => $this->id_acl_role));

        return $this->_roleLabel;
    }

    public function getRightLabel()
    {
        if (null === $this->_rightLabel)
            $this->_rightLabel = $this->getAdapter()->fetchOne("
                SELECT
                    title
                FROM
                    e_acl_action
                WHERE
                    id_acl_action = :aid", array("aid" => $this->id_acl_action));

        return $this->_rightLabel;
    }

    static public function getRoleIdByLogin($login)
    {
        $obj = new self;
        return $obj->getAdapter()
                   ->fetchOne("
            SELECT
                id_acl_role
            FROM
                e_users
            WHERE
                login = :login
            LIMIT 1
        ", array("login" => $login));
    }
}
