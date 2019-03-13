<?php

class Default_UserController
    extends ServicesLib_Controller_Rest
{

    public function indexAction()
    {
        $this->view->items = AppLib_Model_User::getList(array());
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

        $this->view->item = AppLib_Model_User::loadById($id);
        $this->_response->ok();
    }

    public function loginAction()
    {
        $input  = $this->getRequest()->getParams();

        $login   = $input["identifier"];
        $pass    = $input["credential"];

        $c = new AppLib_Service_Crypto();
        $login = $c->decode($login);
        $pass  = $c->decode($pass);

        $loginResult = $this->_processLogin($login, $pass);
        $identity    = Zend_Auth::getInstance()->getIdentity();

        $this->view->result = $loginResult;

        if ($loginResult === true) {
            $this->view->id     = $identity->id_e_user;
            $this->view->login  = $identity->login;
            $this->view->email  = $identity->email;
            $this->view->realname   = $identity->realname;
        }
        $this->_response->ok();
    }

    private function _processLogin($id, $cred)
    {
        try {
            return AppLib_Model_User::login($id, $cred);
        }
        catch (Exception $e) {
            echo "<pre>" . $e->getTraceAsString();die;
            return false;
        }
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