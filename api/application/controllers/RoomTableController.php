<?php

class Default_RoomTableController
    extends ServicesLib_Controller_Rest
{

    public function indexAction()
    {
        $o = new AppLib_Model_RoomTable;
        $conditions = array();
        $parameters = array();
        $joins      = array();
        $fields     = $o->fieldNames();

        array_walk($fields, function(&$item, $key) {
            $item = "e_room_table." . $item;
        });

        /* filter by room id */
        if (null !== $this->_getParam("room_id", null)) {
            $conditions[] = "e_room_table.id_room = :roomid";
            $parameters["roomid"] = $this->_getParam("room_id");
        }

        /* filter by restaurant id */
        if (null !== $this->_getParam("restaurant_id", null)) {
            $conditions[] = "e_restaurant.id_restaurant = :restaurantid";
            $parameters["restaurantid"] = $this->_getParam("restaurant_id");
            $joins[] = "JOIN e_room USING (id_room)";
            $joins[] = "JOIN e_restaurant USING (id_restaurant)";

            $fields[] = "e_room.title as room_title";
        }

        $items = array();
        try {
            $items = AppLib_Model_RoomTable::getList(array(
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
        $id = $this->_getParam('tid', 0);
        $rtitle = $this->_getParam('rtitle', "");
        $table_num = $this->_getParam('table_number', 1);
        $rid    = (int) $this->_getParam("restaurant_id", 1);

        if (! empty($rtitle)) {
            $c = new AppLib_Service_Crypto();
            $dec = $c->decode($rtitle);

            $room = AppLib_Model_Room::loadByTitleAndRestaurantId($dec, $rid);
            $this->view->item = AppLib_Model_RoomTable::loadByNumberAndRoomId($table_num, $room->id_room);
        }
        else
            $this->view->item = AppLib_Model_RoomTable::loadById($id);

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