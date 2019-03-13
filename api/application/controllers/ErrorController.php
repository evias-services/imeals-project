<?php

class Default_ErrorController
	extends ServicesLib_Controller_Rest
{
    public function errorAction()
    {
        if ($this->_request->hasError()) {
            $error = $this->_request->getError();
            $this->view->message = $error->message;
            $this->getResponse()->setHttpResponseCode($error->code);
            return;
        }

        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }

        $this->view->exception = $errors->exception;

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->view->message = 'Page not found';
                $this->getResponse()->setHttpResponseCode(404);
                break;

            default:
                $this->view->message = 'Application error';
                $this->getResponse()->setHttpResponseCode(500);
                break;
        }
    }

    /**
     * Catch-All
     * useful for custom HTTP Methods
     *
     **/
    public function __callAction()
    {
    }

    /**
     * Index Action
     *
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * GET Action
     *
     * @return void
     */
    public function getAction()
    {
    }

    /**
     * POST Action
     *
     * @return void
     */
    public function postAction()
    {
    }

    /**
     * PUT Action
     *
     * @return void
     */
    public function putAction()
    {
    }

    /**
     * DELETE Action
     *
     * @return void
     */
    public function deleteAction()
    {
    }

}

