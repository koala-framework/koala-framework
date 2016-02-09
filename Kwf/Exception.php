<?php
class Kwf_Exception extends Kwf_Exception_NoLog
{
    /**
     * Informiert den Entwickler über diese Exception
     */
    public function notify()
    {
        if ($this->log()) {
            return;
        }
        if (php_sapi_name() == 'cli') {
            echo 'WARNING: '.$this->getMessage()."\n";
        } else if (
            Zend_Registry::get('config')->debug->firephp &&
            class_exists('FirePHP') &&
            FirePHP::getInstance() &&
            FirePHP::getInstance()->detectClientExtension()
        ) {
            p($this->getMessage(), 'WARNING');
        }
    }
    /**
     * Online: Schreibt die Exception nur ins log
     * Lokal: wirft die exception
     */
    public function logOrThrow()
    {
        if ($this->log()) {
            if (php_sapi_name() == 'cli') {
                file_put_contents('php://stderr', $this->__toString()."\n");
            }
            return;
        }
        throw $this;
    }

    public function log()
    {
        if (Kwf_Exception::isDebug()) {
            return false;
        }
        $body = $this->_getLogBody();
        return Kwf_Exception_Logger_Abstract::getInstance()->log($this, 'error', $body);
    }

    protected function _getLogBody()
    {
        $user = "guest";
        try {
            if (Zend_Registry::get('userModel') && $u = Zend_Registry::get('userModel')->getAuthedUser()) {
                $userName = $u->__toString();
                $user = "$userName, id $u->id, $u->role";
            }
        } catch (Exception $e) {
            $user = "error getting user";
        }
        $exception = $this->getException();

        $body = '';
        $body .= $this->_format('Exception', get_class($exception));
        $body .= $this->_format('Thrown', $exception->getFile().':'.$exception->getLine());
        $body .= $this->_format('Message', $exception->getMessage());
        $body .= $this->_format('ExceptionDetail', $exception->__toString());
        $body .= $this->_format('REQUEST_URI', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '(none)');
        $body .= $this->_format('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '(none)');
        $body .= $this->_format('User', $user);
        $body .= $this->_format('Time', date('H:i:s'));
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $body .= $this->_format('_USERAGENT', $_SERVER['HTTP_USER_AGENT']);
        }
        $body .= $this->_format('_GET', print_r($_GET, true));
        $body .= $this->_format('_POST', print_r($_POST, true));
        $body .= $this->_format('_SERVER', print_r($_SERVER, true));
        $body .= $this->_format('_FILES', print_r($_FILES, true));
        if (isset($_SESSION)) {
            $body .= $this->_format('_SESSION', print_r($_SESSION, true));
        }
        try {
            $request = Kwf_Controller_Front::getInstance()->getRequest();
            if ($request && $request instanceof Zend_Controller_Request_Http) {
                $rawBody = $request->getRawBody();
                if ($rawBody) {
                    if (defined('JSON_PRETTY_PRINT') && substr($rawBody, 0, 1) == '{' &&
                        is_object(json_decode($rawBody)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $rawBody = json_encode(json_decode($rawBody), JSON_PRETTY_PRINT);
                    }
                    $body .= $this->_format('RawBody', $rawBody);
                }
            }
        } catch (Exception $e) {
        }
        return $body;
    }
}
