<?php

class BackLib_Controller_Plugin_AccessControl
    extends Zend_Controller_Plugin_Abstract
{
    protected static $_whitelisted = array(
        "default",
    );

    /**
     *
     */
    public function preDispatch( Zend_Controller_Request_Abstract $request )
    {
        $baseUrl    = Zend_Controller_Front::getInstance()->getBaseUrl();
        $requestURI = $_SERVER["REQUEST_URI"];

        if (! empty($baseUrl)
            && false !== strpos($requestURI, $baseUrl))
            /* remove baseURL from treated URI */
            $requestURI = substr($requestURI, mb_strlen($baseUrl));

        if ($this->isWhiteListed($requestURI))
            return;

        $acl_check = new BackLib_AclCheck(array(
            "identity"   => Zend_Auth::getInstance()->getIdentity(),
            "module"     => $request->getModuleName(),
            "controller" => $request->getControllerName(),
            "action"     => $request->getActionName()));
        if (! $acl_check->checkPermission()) {

            $referer = "";
            if (! Zend_Auth::getInstance()->hasIdentity())
                /* Access denied may be caused by lost of session. */
                $referer = "?referer=" . urlencode(sprintf("/%s/%s/%s",
                                                              $acl_check->getModule(),
                                                              $acl_check->getController(),
                                                              $acl_check->getAction()));
            
            $this->getResponse()
                 ->setRedirect("$baseUrl/default/index/denied" . $referer)
                 ->sendResponse();
        }
    }

    public function isWhiteListed($uri)
    {
        array_walk(self::$_whitelisted, function(&$uri)
        {
            /* Format uris for regular expression */
            $uri = str_replace(array("/", "-"), array("\/", "\-"), $uri);
        });

        $reg_allowed = "\/(" . implode("|", self::$_whitelisted) . ")(\/.*)?";

        return (bool) preg_match("/^{$reg_allowed}/", $uri);
    }


}
