<?php
abstract class Vps_Exception_Abstract extends Exception
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
        return !Zend_Registry::get('config')->debug->error->log;
    }

    public function getException()
    {
        return $this;
    }

    protected function _writeLog($path, $filename, $content)
    {
        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if (self::isDebug()
            || substr($requestUri, -12) == '/favicon.ico'
            || substr($requestUri, -10) == '/robots.txt')
        {
            return false;
        }
        $this->_logFilename = $filename;
        if (!is_dir($path)) mkdir($path, 0777);
        $fp = fopen("$path/$filename", 'a');
        fwrite($fp, $content);
        fclose($fp);
        chmod("$path/$filename", 0777);
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
}
