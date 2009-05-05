<?php
class Vps_Exception_NoLog extends Exception
{
    private $_logFilename;

    public function getHeader()
    {
        return 'HTTP/1.1 500 Internal Server Error';
    }

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

    public function log() {
        return false;
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
        if (!is_dir($path)) mkdir($path);
        $fp = fopen("$path/$filename", 'a');
        fwrite($fp, $content);
        fclose($fp);
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
