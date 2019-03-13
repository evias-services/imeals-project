<?php

class Default_MenuCategoriesController
    extends ServicesLib_Controller_Rest
{
    public function indexAction()
    {
        $o = new AppLib_Model_Category;
        $conditions = array();
        $parameters = array();
        $joins      = array();
        $fields     = $o->fieldNames();

        array_walk($fields, function(&$item, $key) {
            $item = "e_menu_category." . $item;
        });

        /* filter by menu id */
        if (null !== $this->_getParam("menu_id", null)) {
            $conditions[] = "e_menu_category.id_menu = :menuid";
            $parameters["menuid"] = $this->_getParam("menu_id");
        }

        /* filter by restaurant id */
        if (null !== $this->_getParam("restaurant_id", null)) {
            $conditions[] = "e_restaurant.id_restaurant = :restaurantid";
            $parameters["restaurantid"] = $this->_getParam("restaurant_id");
            $joins[] = "JOIN e_menu USING (id_menu)";
            $joins[] = "JOIN e_restaurant USING (id_restaurant)";
            $fields[] = "e_menu.title as menu_title";
        }

        $items = array();
        try {
            $items = AppLib_Model_Category::getList(array(
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
        $id     = $this->_getParam('cid', 0);
        $title  = $this->_getParam('title', "");

        if (! empty($title)) {
            $c = new AppLib_Service_Crypto();
            $dec = $c->decode($title);

            if ((bool) preg_match("/^[A-Za-z0-9 ]+: /", $dec))
                /* Remove available menu_part. */
                $dec = preg_replace("/^[A-Za-z0-9 ]+: /", "", $dec);

            $this->view->item = AppLib_Model_Category::loadByTitle($dec);
        }
        else
            $this->view->item = AppLib_Model_Category::loadById($id);

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
