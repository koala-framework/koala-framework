<?php
abstract class Kwf_Exception_Abstract extends Exception
{
    public abstract function getHeader();

    public abstract function log();

    public function getTemplate()
    {
        return 'Error';
    }

    public static function isDebug()
    {
        try {
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


        require_once 'Kwf/Trl.php';
        $view = Kwf_Debug::getView();
        $view->exception = $msg;
        $view->message = $exception->getMessage();
        $view->requestUri = isset($_SERVER['REQUEST_URI']) ?
            htmlspecialchars($_SERVER['REQUEST_URI']) : '' ;
        $view->debug = Kwf_Exception::isDebug();
        $header = $this->getHeader();
        $template = $this->getTemplate();
        $template = strtolower(Zend_Filter::filterStatic($template, 'Word_CamelCaseToDash').'.tpl');
        $this->log();

        if (!headers_sent()) {
            header($header);
            header('Content-Type: text/html; charset=utf-8');
        }

        while(@ob_end_flush()) {} //end all output buffers to avoid exception output getting into output buffer
        try {
            echo $view->render($template);
        } catch (Exception $e) {
            echo '<pre>';
            echo $this->__toString();
            echo "\n\n\nError happened while handling exception:";
            echo $e->__toString();
            echo '</pre>';
        }

   }
}
