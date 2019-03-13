<?php

class AppLib_Service_Factory
{
    static private $_class_map = array(
        "application"   => "AppLib_Service_Application",
        "restaurant"    => "AppLib_Service_Restaurant",
        "order"         => "AppLib_Service_Order",
        "menu"          => "AppLib_Service_Menu",
        "cart"          => "AppLib_Service_Cart",
        "customer"      => "AppLib_Service_Customer",
        "user"          => "AppLib_Service_User",
        "acl"           => "AppLib_Service_AccessControl",
        "booking"       => "AppLib_Service_Booking",
        "search"        => "AppLib_Service_Search",
    );

    static private $_instances = array();

    static public function getService($sid, array $options = array())
    {
        if (! in_array($sid, array_keys(self::$_class_map)))
            throw new AppLib_Exception("Wrong service ID provided");

        if (! isset(self::$_instances[$sid])) {
            $class   = self::$_class_map[$sid];
            $service = new $class($options);

            self::$_instances[$sid] = $service;
        }

        self::$_instances[$sid]->setOptions($options);
        return self::$_instances[$sid];
    }

    static public function getResourceTypes()
    {
        return array_keys(self::$_class_map);
    }
}

