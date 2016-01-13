<?php
abstract class Kwf_Exception_Abstract extends Exception
{
    public static $logErrors; //overrides debug.error.log

    public abstract function getHeader();

    public abstract function log();

    public function getTemplate()
    {
        return 'Error';
    }

    public static function isDebug()
    {
        try {
            if (isset(self::$logErrors)) return !self::$logErrors;
            return !Kwf_Config::getValue('debug.error.log');
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

    public function render($ignoreCli = false)
    {
        try {
            $exception = $this->getException();
            $msg = $exception->__toString();
            if ($exception instanceof Zend_Db_Adapter_Exception) {
                try {
                    foreach (Kwf_Registry::get('config')->database as $db) {
                        $msg = str_replace($db->password, 'xxxxxx', $msg);
                    }
                } catch (Exception $e) {}
            }

            if (!$ignoreCli && php_sapi_name() == 'cli') {
                $this->log();
                file_put_contents('php://stderr', $msg."\n");
                exit(1);
            }

            class_exists('Kwf_Trl'); //eventually trigger autoloader
            $view = Kwf_Debug::getView();
            $view->exception = $msg;
            $view->message = $exception->getMessage();
            $view->requestUri = isset($_SERVER['REQUEST_URI']) ?
                htmlspecialchars($_SERVER['REQUEST_URI']) : '' ;
            $view->debug = Kwf_Exception::isDebug() || !Kwf_Registry::get('config')->setupFinished;
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

            $header = $this->getHeader();
            $template = $this->getTemplate();
            $template = strtolower(Zend_Filter::filterStatic($template, 'Word_CamelCaseToDash').'.tpl');
            $this->log();

            if (!headers_sent()) {
                header($header);
                header('Content-Type: text/html; charset=utf-8');
            }

            echo $view->render($template);
        } catch (Exception $e) {
            if (Kwf_Exception::isDebug()) {
                echo '<pre>';
                echo $this->getException()->__toString();
                echo "\n\n\nError happened while handling exception:";
                echo $e->__toString();
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
}
