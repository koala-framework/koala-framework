<?php
abstract class Kwf_Exception_Abstract extends Exception
{
    public static $logErrors; //overrides debug.error.log
    protected $_logId;

    public abstract function getHeader();

    public abstract function log();

    public function setLogId($logId)
    {
        $this->_logId = $logId;
    }

    public function getLogId()
    {
        return $this->_logId;
    }

    public function getTemplate()
    {
        return 'Error';
    }

    public function getComponentClass()
    {
        return null;
    }

    public static function isDebug()
    {
        try {
            $isDebug = Kwf_Config::getValue('debug.error.display');
            if (is_null($isDebug)) {
                $isDebug = !self::isLogEnabled();
            }
            return $isDebug;
        } catch (Exception $e) {
            return true;
        }
    }

    public static function isLogEnabled()
    {
        try {
            if (isset(self::$logErrors)) return !self::$logErrors;
            return Kwf_Config::getValue('debug.error.log');
        } catch (Exception $e) {
            return true;
        }
    }

    public function getException()
    {
        return $this;
    }

    protected function _format($part, $text)
    {
        return "** $part **\n$text\n-- $part --\n\n";
    }

    protected function _renderHtml($exception, $msg)
    {
        if ($this->getComponentClass() && Kwf_Component_Data_Root::getComponentClass() && isset($_SERVER['REQUEST_URI']) && isset($_SERVER['HTTP_HOST'])) {

            $uri = $_SERVER['REQUEST_URI'];
            $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
            $data = null;
            while (!$data && $uri) {
                $data = Kwf_Component_Data_Root::getInstance()->getPageByUrl('http://'.$_SERVER['HTTP_HOST'].$uri, $acceptLanguage);
                $uri = substr($uri, 0, strrpos($uri, '/'));
            }
            if (!$data) {
                $data = Kwf_Component_Data_Root::getInstance()->getPageByUrl('http://'.$_SERVER['HTTP_HOST'].'/', $acceptLanguage);
            }
            if (!$data) {
                $data = Kwf_Component_Data_Root::getInstance();
            }

            $notFound = Kwf_Component_Data_Root::getInstance()
                ->getComponentByClass($this->getComponentClass(), array('limit'=>1, 'subroot'=>$data));

            if ($notFound) {
                $notFound->getComponent()->setException($exception);
                $contentSender = Kwc_Abstract::getSetting($notFound->componentClass, 'contentSender');
                $contentSender = new $contentSender($notFound);
                $content = $contentSender->getContent(true);
                $content = $content['content'];
                return str_replace('{logId}', $this->_logId, $content);
            }
        }

        class_exists('Kwf_Trl'); //eventually trigger autoloader
        $view = Kwf_Debug::getView();
        $view->exception = $msg;
        $view->message = $exception->getMessage();
        $view->method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        $view->requestUri = isset($_SERVER['REQUEST_URI']) ?
            htmlspecialchars($_SERVER['REQUEST_URI']) : '' ;
        $view->debug = Kwf_Exception::isDebug();
        try {
            if (Kwf_Registry::get('userModel') && Kwf_Registry::get('userModel')->getAuthedUserRole() == 'admin') {
                $view->debug = true;
            }
        } catch (Exception $e) {}

        if (Kwf_Component_Data_Root::getComponentClass()) {
            $data = null;
            if (isset($_SERVER['HTTP_HOST'])) {
                //try to get the page of current domain to get correct language
                $acceptLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
                try {
                    $data = Kwf_Component_Data_Root::getInstance()->getPageByUrl('http://'.$_SERVER['HTTP_HOST'].'/', $acceptLanguage);
                } catch (Exception $e) {}
            }
            if (!$data) $data = Kwf_Component_Data_Root::getInstance();
            $view->data = $data; //can be used for trl
        } else {
            //no components used, use Kwf_Trl object that also has trl() methods
            //HACK, but will work if only trl is used in template
            $view->data = Kwf_Trl::getInstance();
        }
        $template = $this->getTemplate();
        $template = strtolower(Zend_Filter::filterStatic($template, 'Word_CamelCaseToDash').'.tpl');
        return $view->render($template);
    }

    public function render($ignoreCli = false)
    {
        try {
            $exception = $this->getException();
            $msg = $this->_getSafeExceptionString($exception);

            if (!$ignoreCli && PHP_SAPI == 'cli') {
                $this->log();
                file_put_contents('php://stderr', $msg."\n");
                exit(1);
            }

            $header = $this->getHeader();
            $this->log();

            $format = 'html';
            if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') {
                // Is mainly used for Exceptions in setup or bootstrap if called from native application
                $format = 'json';
            }

            if ($format == 'json') {
                $output = $this->_renderJson($exception, $msg);
            } else {
                $output = $this->_renderHtml($exception, $msg);
            }


            if (!headers_sent()) {
                header($header);
                if ($format == 'json') {
                    header('Content-Type: application/json; charset=utf-8');
                } else {
                    header('Content-Type: text/html; charset=utf-8');
                }
            }

            echo $output;

            Kwf_Benchmark::output();

        } catch (Exception $e) {
            if (Kwf_Exception::isDebug()) {
                echo '<pre>';
                echo $this->_getSafeExceptionString($e);
                echo "\n\n\nError happened while handling exception:";
                echo $this->_getSafeExceptionString($exception);
                echo '</pre>';
            } else {
                if (!headers_sent()) {
                    header('HTTP/1.1 500 Internal Server Error');
                    header('Content-Type: text/html; charset=utf-8');
                }
                echo '<h1>Error</h1>';
                echo '<p>An Error ocurred. Please try again later.</p>';
            }
        }
    }

    private function _getSafeExceptionString($exception)
    {
        $ret = $exception->__toString();
        if ($exception instanceof Zend_Db_Adapter_Exception) {
            try {
                foreach (Kwf_Registry::get('config')->database as $db) {
                    $ret = str_replace($db->password, 'xxxxxx', $ret);
                }
            } catch (Exception $e) {}
        }
        if ($exception instanceof Kwf_Exception_Unauthorized) {
            try {
                $passwordToMask = $exception->getPasswordToMask();
                if ($passwordToMask) {
                    $ret = str_replace($passwordToMask, 'xxxxxx', $ret);
                }
            } catch (Exception $e) {}
        }
        return $ret;
    }

    protected function _renderJson($exception, $msg)
    {
        $data = array(
            'error' => array(
                'code' => $exception->code,
                'errorId' => $exception->getLogId(),
                'message' => 'An Error occured. Please try again later',
            )
        );
        $debug = Kwf_Exception::isDebug();
        try {
            if (Kwf_Registry::get('userModel') && Kwf_Registry::get('userModel')->getAuthedUserRole() == 'admin') {
                $debug = true;
            }
        } catch (Exception $e) {}
        if ($debug) {
            $data = array(
                'error' => array(
                    'code' => $exception->code,
                    'errorId' => $exception->getLogId(),
                    'message' => $exception->message,
                    'exception' => array(array(
                        'message' => $exception->message,
                        'class' => get_class($exception),
                        'trace' => $exception->getTrace()
                    ))
                )
            );
        }
        return json_encode($data);
    }
}
