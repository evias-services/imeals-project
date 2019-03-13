<?php

class AppLib_Model_CustomerLocation
    extends eVias_ArrayObject_Db
{
    protected $_tableName = "e_customer_location";
    protected $_pk        = "id_location";
    protected $_sequence  = "e_customer_location_id_location_seq";
    protected $_fields    = array(
        "id_customer",
        "title",
        "address",
        "zipcode",
        "city",
        "country",
        "gl_latitude",
        "gl_longitude",
        "gl_confirmed",
        "comments",
        "date_created",
        "date_updated",);

}
