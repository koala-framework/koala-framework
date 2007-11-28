<?p
class Vps_Controller_Action_Error extends Vps_Controller_Acti

    public function errorAction
   
        $errors = $this->getRequest()->error_handle

        if ($this->_getParam('module') == 'component' 
            $this->_getParam('action') == 'jsonIndex' 
            $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLE
       
            $errors->exception = new Vps_ClientException('Für diese Komponente gibt es keinen Editing-Bereich.'
       

        $prefix = substr($this->_getParam('action'), 0, 4
        $isHttpRequest = $_SERVER['REQUEST_METHOD'] == 'POST' 
        			isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest
        if (($prefix == 'ajax' || $prefix == 'json') 
            ($isHttpRequest || $errors->exception instanceof Vps_ClientException
       
            $this->_forward('jsonError'
        } else

            if ($errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER 
                $errors->type == Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION 
                $errors->exception instanceof Vps_Controller_Action_Web_Exceptio
           
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found'
                $file = 'Error404.html
            } else
                $file = 'Error.html
           

            $this->view->setRenderFile($file

            $config = Zend_Registry::get('config'
            $this->view->type = $errors->typ
            $this->view->exception = $errors->exceptio
            if ($config->debug->errormail != '')
                Vps_Debug::sendErrorMail($errors->exception, $config->debug->errormail
                $this->view->debug = fals
            } else
                $this->view->debug = tru
           
       
   

    public function jsonErrorAction
   
        $errors = $this->getRequest()->error_handle
        $exception = $errors->exceptio
        if ($exception instanceof Vps_ClientException)
            $this->view->error = $exception->getMessage(
        } else
            $config = Zend_Registry::get('config'
            if ($config->debug->errormail != '')
                Vps_Debug::sendErrorMail($exception, $config->debug->errormail
                $this->view->error = 'An error occured. Please try again later.
            } else
                $this->view->exception = $exception->__toString(
           
       
   

    public function jsonMailAction
   
        $exception = new Vps_JavaScriptException($this->_getParam('msg')
        Vps_Debug::sendErrorMail($exception, Zend_Registry::get('config')->debug->errormail
   

    public function jsonWrongVersionAction
   
        $this->view->wrongversion = tru
        $this->view->success = fals
   
