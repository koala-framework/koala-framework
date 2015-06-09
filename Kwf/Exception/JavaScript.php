<?php
class Kwf_Exception_JavaScript extends Kwf_Exception
{
    private $_url;
    private $_lineNumber;
    private $_stack;
    private $_location;
    private $_referrer;

    public function setUrl($v)
    {
        $this->_url = $v;
    }
    public function setLineNumber($v)
    {
        $this->_lineNumber = $v;
    }

    public function setStack($v)
    {
        $this->_stack = $v;
    }
    public function setLocation($v)
    {
        $this->_location = $v;
    }
    public function setReferrer($v)
    {
        $this->_referrer = $v;
    }

    public function log()
    {
        //don't check for Kwf_Exception::isDebug()
        //because we can't display the error anyway

        $user = "guest";
        try {
            if (Zend_Registry::get('userModel')) {
                if ($u = Zend_Registry::get('userModel')->getAuthedUser()) {
                    $user = "$u, id $u->id, $u->role";
                }
            }
        } catch (Exception $e) {
            $user = "error getting user";
        }

        $body = '';
        $body .= $this->_format('Exception', get_class($this));
        $body .= $this->_format('Thrown', $this->_url.':'.$this->_lineNumber);
        $body .= $this->_format('Message', $this->getMessage());
        $body .= $this->_format('stack', print_r($this->_stack, true));
        $body .= $this->_format('REQUEST_URI', $this->_location);
        $body .= $this->_format('HTTP_REFERER', $this->_referrer ? $this->_referrer : '(none)');
        $body .= $this->_format('HTTP_USER_AGENT', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $body .= $this->_format('User', $user);
        $body .= $this->_format('Time', date('H:i:s'));

        Kwf_Exception_Logger_Abstract::getInstance()->log($this, 'error', $body);

    }
}
