<?php

class ErrorController
	extends FrontLib_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';

                break;
            default:
                // application error
				$this->view->message =  'Application error : <br />Code = ' .
										$this->getResponse()->getHttpResponseCode() . '<br />';
				if (isset($errors->exception)) {
					$exceptionMessage =
						($errors->exception instanceof eVias_Exception) ?
							$errors->exception->getCustomMessage() :
							$errors->exception->getMessage();
					$this->view->message .= '<hr />';
					$this->view->message .= 'EXCEPTION MESSAGE: <br />';
					$this->view->message .= $exceptionMessage. '<br />';
				}
				break;
        }

        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;

/* XXX PRE-RELEASE 
 *  enable redirection to disable error display
        $this->_redirect("/default/index/index");
 */
    }


}

