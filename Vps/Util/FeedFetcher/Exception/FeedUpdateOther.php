<?php
class Vps_Util_FeedFetcher_Exception_FeedUpdateOther extends Vps_Exception_Abstract
{
    public static $logged = array();
    private $_exception;

    public function getHeader()
    {
        return 'HTTP/1.1 500 Internal Server Error';
    }

    public function __construct(Exception $exception)
    {
        $this->_exception = $exception;
    }

    public function getException()
    {
        return $this->_exception;
    }

    public function log()
    {
        self::$logged[] = $this; //f端r unit-tests

        $body = '';
        $exception = $this->getException();
        $body .= $this->_format('Exception', get_class($exception));
        $body .= $this->_format('Thrown', $exception->getFile().':'.$exception->getLine());
        $body .= $this->_format('Message', $exception->getMessage());
        $body .= $this->_format('ExceptionDetail', $exception->__toString());
        $body .= $this->_format('Time', date('H:i:s'));

        $path = 'application/log/errorfeedupdate/' . date('Y-m-d');

        $filename = date('H_i_s') . '_' . uniqid() . '.txt';

        return $this->_writeLog($path, $filename, $body);
    }
}
