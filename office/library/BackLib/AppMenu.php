<?php

class BackLib_AppMenu
{
    private $_pages = array();

    /* XXX set selected. */

    static private $_instances = null;
    static public function getInstance($title, $baseUrl = "")
    {
        if (! isset(self::$_instances))
            self::$_instances = array();
        if (! isset(self::$_instances[$title]))
            self::$_instances[$title] = new self($baseUrl);

        return self::$_instances[$title];
    }

    private function __construct($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
    }

    /**
     * Build the subnavigation pages tree from request.
     *
     * @param Zend_Controller_Request_Http $request
     */
    public function setPagesByRequest(Zend_Controller_Request_Http $request)
    {
        switch ($request->getModuleName()) {
        default:
        case "default":
            $this->_pages = array();
            break;

        case "manage":
            if ("menu" == $request->getControllerName()) {
                $this->_pages = array(
                    array("label" => BackLib_Lang::tr("txt_link_menu_list_category"),
                          "module"=> "manage", "controller" => "menu",
                          "action"=> "list-categories"),
                    array("label" => BackLib_Lang::tr("txt_link_menu_list_item"),
                          "module"=> "manage", "controller" => "menu",
                          "action"=> "list-items"),);
            }
            elseif ("customers" == $request->getControllerName()) {

            }
            elseif ("orders" == $request->getControllerName()) {

            }
            elseif ("platform" == $request->getControllerName()) {

            }
            break;
        }
    }

    public function isEmpty()
    {
        return empty($this->_pages);
    }

    public function toHTML()
    {
        $html = "";
        if (! empty($this->_pages)) {
            $html .= "<ul>";
            foreach ($this->_pages as $page) {
                $uri = sprintf("%s/%s/%s/%s",
                              $this->_baseUrl,
                              $page["module"],
                              $page["controller"],
                              $page["action"]);

                $html .= sprintf("<li class='rounded-corners'><a href='%s'>%s</a></li>",
                                 $uri, $page["label"]);
            }

            $html .= "</ul>";
        }

        return $html;
    }
}
