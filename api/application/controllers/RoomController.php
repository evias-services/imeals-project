<?php

class Default_RoomController
    extends ServicesLib_Controller_Rest
{

    public function indexAction()
    {
        $os = new AppLib_Service_Order;
        $this->view->items = $os->getTableOrderSummary("hahahah", 2);

     //   $this->view->items = AppLib_Model_Room::getList(array());
        $this->_response->ok();
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        $id = $this->_getParam('rid', 0);

        $this->view->item = AppLib_Model_Room::loadById($id);
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