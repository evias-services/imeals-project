<?php

class GraphController
    extends FrontLib_Controller_Action
{
    private $_service = null;

    public function init()
    {
        parent::init();
    }

    public function searchAction()
    {
        $this->getHelper("viewRenderer")
            ->setNoRender(true);

        $this->view->layout()->disableLayout();

        $query = $this->_getParam("term", "");

        $proxy = new AppLib_Service_Proxy;
        $proxy->setService("search", array("return_objects" => true));

        $result = $proxy->__call("query", array(
                "query" => $query));

        /* XXX refactor json encoding */

        $c = 0;
        $json = "[";
        foreach ($result as $item) {
            $json .= ($c++ > 0 ? ', ' : '')
                . '{"id": "' . $item->id_restaurant . '", '
                . '"value": "' . $item->title . '",'
                . '"desc": "' . $item->address . ' - ' . $item->zipcode . ' ' . $item->city . '",'
                . '"label": "' . $item->title . '"}';
        }
        $json .= "]";

        header('Content-Type: text/json; charset=utf-8');
        echo $json;
        exit;
    }
}
