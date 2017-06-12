<?php
class Kwf_Exception_NotFound extends Kwf_Exception_Abstract
{
    public function __construct($message = "Not found")
    {
        parent::__construct($message);
    }

    public function getHeader()
    {
        return 'HTTP/1.1 404 Not Found';
    }

    public function getTemplate()
    {
        return 'Error404';
    }

    public function getComponentClass()
    {
        return 'Kwc_Errors_NotFound_Component';
    }

    public function log()
    {
        if (!self::isLogEnabled()) {
            return false;
        }

        $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '(none)';
        $ignore = array(
            '/favicon.ico',
            '/robots.txt',
        );
        if (in_array($requestUri, $ignore)) {
            return false;
        }
        if (substr($requestUri, 0, 7) == '/files/' || substr($requestUri, 0, 12) == '/monitoring/') { //TODO: don't hardcode here
            return false;
        }

        $body = '';
        $body .= $this->_format('REQUEST_URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '(none)');
        $body .= $this->_format('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '(none)');
        $body .= $this->_format('Time', date('H:i:s'));

        Kwf_Exception_Logger_Abstract::getInstance()->log($this, 'notfound', $body);
    }

    public function render($ignoreCli = false)
    {
        try {
            if (isset($_SERVER['REQUEST_URI']) && Kwf_Setup::hasDb() && Kwf_Registry::get('dao')->getDbConfig()) {
                $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
                $target = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Redirects')
                    ->findRedirectUrl('path', $_SERVER['REQUEST_URI'], $host);
                if ($target) {
                    header('Location: '.$target, true, 301);
                    exit;
                }
            }
        } catch (Exception $e) {
            Kwf_Debug::handleException($e);
        }

        parent::render($ignoreCli);
    }
}
