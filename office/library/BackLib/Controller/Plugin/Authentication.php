<?php

class BackLib_Controller_Plugin_Authentication
    extends Zend_Controller_Plugin_Abstract
{
    protected static $_whitelisted = array(
        "dashboard",
        "default",
    );

    public function preDispatch( Zend_Controller_Request_Abstract $request )
    {
        $requestURI = $_SERVER["REQUEST_URI"];
        array_walk(self::$_whitelisted, function(&$uri)
        {
            $uri = str_replace(array("/", "-"), array("\/", "\-"), $uri);
        });

        $reg_allowed = "\/(" . implode("|", self::$_whitelisted) . ")(\/.+)?";

        if (! AppLib_Model_User::is_auth()
            && ! preg_match("/^{$reg_allowed}/", $requestURI)) {

            /* Authentication NEEDED */
            $this->getResponse()
                 ->setRedirect("/default/login/login")
                 ->sendResponse();
        }
    }

}
