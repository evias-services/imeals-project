<?php

class Default_MenuMealsController
    extends ServicesLib_Controller_Rest
{
    public function indexAction()
    {
        $o = new AppLib_Model_Item;
        $conditions = array();
        $parameters = array();
        $joins      = array();
        $fields     = $o->fieldNames();

        array_walk($fields, function(&$item, $key) {
            $item = "e_menu_item." . $item;
        });

        /* filter by menu id */
        if (null !== $this->_getParam("category_id", null)) {
            $conditions[] = "e_menu_item.id_category = :categoryid";
            $parameters["categoryid"] = $this->_getParam("category_id");
            $joins[] = "JOIN e_menu_category USING (id_category)";
            $fields[] = "e_menu_category.title as category_title";
        }

        /* filter by restaurant id */
        if (null !== $this->_getParam("menu_id", null)) {
            $conditions[] = "e_menu_category.id_menu = :menuid";
            $parameters["menuid"] = $this->_getParam("menu_id");
            $joins[] = "JOIN e_menu_category USING (id_category)";
            $fields[] = "e_menu_category.title as category_title";
        }

        $items = array();
        try {
            $items = AppLib_Model_Item::getList(array(
                "conditions" => $conditions,
                "parameters" => $parameters,
                "joins"      => $joins,
                "fields"     => $fields,
            ));
        }
        catch(Exception $e) { /*die($e->getMessage());*/ }

        $this->view->items = $items;
        $this->_response->ok();
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        $id     = $this->_getParam('mid', 0);
        $title  = $this->_getParam('title', "");

        if (! empty($title)) {
            $c = new AppLib_Service_Crypto();
            $dec = $c->decode($title);
            $this->view->item = AppLib_Model_Item::loadByTitle($dec);
        }
        else
            $this->view->item = AppLib_Model_Item::loadById($id);

        $this->_response->ok();
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $this->view->params = $this->_request->getParams();

        /** XXX **/

        $this->view->message = 'Resource Created';
        $this->_response->created();
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        /** XXX **/

        $this->view->message = sprintf('Resource #%s Updated', $id);
        $this->_response->ok();
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        /** XXX **/

        $this->view->message = sprintf('Resource #%s Deleted', $id);
        $this->_response->ok();
    }

}
