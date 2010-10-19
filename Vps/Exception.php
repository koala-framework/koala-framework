<?php
require_once 'Vps/Exception/NoLog.php';
class Vps_Exception extends Vps_Exception_NoLog
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
            return;
        }
        throw $this;
    }

    public function log()
    {
        $user = "guest";
        try {
            if ($u = Zend_Registry::get('userModel')->getAuthedUser()) {
                $user = "$u, id $u->id, $u->role";
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

        $path = 'application/log/error/' . date('Y-m-d');

        $filename = date('H_i_s') . '_' . uniqid() . '.txt';

        return $this->_writeLog($path, $filename, $body);
    }
}
