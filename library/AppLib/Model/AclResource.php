<?php

class AppLib_Model_AclResource
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_acl_resource";
    protected $_pk        = "id_acl_resource";
    protected $_sequence  = "e_acl_resource_id_acl_resource_seq";
    protected $_fields    = array(
        "module",
        "controller",
        "action",
        "date_created",
        "date_updated",);

    public function toURI()
    {
        return sprintf("/%s/%s/%s", $this->module,
                                    $this->controller,
                                    $this->action);
    }
}
