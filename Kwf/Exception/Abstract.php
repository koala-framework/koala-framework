<?php
abstract class Kwf_Exception_Abstract extends Exception
{
    private $_logFilename;

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

    protected function _writeLog($path, $filename, $content, $force = false)
    {
        if (self::isDebug() && !$force) {
            return false;
        }
        $this->_logFilename = $filename;
        if (!is_dir($path)) @mkdir($path);
        try {
            $fp = fopen("$path/$filename", 'a');
            fwrite($fp, $content);
            fclose($fp);
        } catch(Exception $e) {
            $to = array();
            foreach (Kwf_Registry::get('config')->developers as $dev) {
                if (isset($dev->sendException) && $dev->sendException) {
                    $to[] = $dev->email;
                }
            }
            mail(implode('; ', $to),
                'Error while trying to write error file',
                $e->__toString()."\n\n---------------------------\n\nOriginal Exception:\n\n".$content
                );
        }
        return true;
    }

    public function getLogFilename()
    {
        return $this->_logFilename;
    }

    protected function _format($part, $text)
    {
        return "** $part **\n$text\n-- $part --\n\n";
    }

    public function render($ignoreCli = false)
    {
        if (!$ignoreCli && php_sapi_name() == 'cli') {
            $this->log();
            file_put_contents('php://stderr', $this->getException()->__toString()."\n");
            exit(1);
        }

        $view = Kwf_Debug::getView();
        $view->exception = $this->getException();
        $view->message = $this->getException()->getMessage();
        $view->requestUri = isset($_SERVER['REQUEST_URI']) ?
            $_SERVER['REQUEST_URI'] : '' ;
        $view->debug = Kwf_Exception::isDebug();
        $header = $this->getHeader();
        $template = $this->getTemplate();
        $template = strtolower(Zend_Filter::filterStatic($template, 'Word_CamelCaseToDash').'.tpl');
        $this->log();

        if (!headers_sent()) {
            header($header);
            header('Content-Type: text/html; charset=utf-8');
        }

        try {
            echo $view->render($template);
        } catch (Exception $e) {
            echo '<pre>';
            echo $this->__toString();
            echo "\n\n\nError happened while handling exception:";
            echo $e->__toString();
            echo '</pre>';
        }
        Kwf_Benchmark::shutDown();
        Kwf_Benchmark::output();
   }
}
