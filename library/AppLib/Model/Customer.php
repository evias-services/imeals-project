<?php

class AppLib_Model_Customer
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_customer";
    protected $_pk        = "id_customer";
    protected $_sequence  = "e_customer_id_customer_seq";
    protected $_fields    = array(
        "realname",
        "phone",
        "email",
        "date_created",
        "date_updated",);
    
    public function getTitle()
    {
        $contact = array();
        if (!empty($this->phone))
            $contact[] = "Tel.: " . $this->phone;

        if (!empty($this->email))
            $contact[] = "Mail: " . $this->email;

        return sprintf("%s (%s)", $this->realname, implode(", ", $contact));
    }
}