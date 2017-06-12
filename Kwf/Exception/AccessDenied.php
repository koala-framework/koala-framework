<?php
class Kwf_Exception_AccessDenied extends Kwf_Exception_Abstract
{
    public function __construct($message = "Access denied")
    {
        parent::__construct($message, 403);
    }

    public function getHeader()
    {
        return 'HTTP/1.1 403 Forbidden';
    }

    public function getTemplate()
    {
        return 'Error403';
    }

    public function getComponentClass()
    {
        return 'Kwc_Errors_AccessDenied_Component';
    }

    public function log()
    {
        if (!self::isLogEnabled()) {
            return false;
        }

        $body = '';
        $body .= $this->_format('REQUEST_URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '(none)');
        $body .= $this->_format('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '(none)');
        $body .= $this->_format('Time', date('H:i:s'));
        $body .= $this->_format('_GET', print_r($_GET, true));
        $body .= $this->_format('_POST', print_r($_POST, true));

        Kwf_Exception_Logger_Abstract::getInstance()->log($this, 'accessdenied', $body);
    }
}
