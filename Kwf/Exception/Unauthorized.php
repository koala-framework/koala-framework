<?php
class Kwf_Exception_Unauthorized extends Kwf_Exception_Abstract
{
    private $_passwordToMask;

    public function __construct($message = "Unauthorized")
    {
        parent::__construct($message, 401);
    }

    public function getHeader()
    {
        return 'HTTP/1.1 401 Unauthorized';
    }

    public function getTemplate()
    {
        return 'Error401';
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

    public function setPasswordToMask($password)
    {
        $this->_passwordToMask = $password;
    }

    public function getPasswordToMask()
    {
        return $this->_passwordToMask;
    }
}
