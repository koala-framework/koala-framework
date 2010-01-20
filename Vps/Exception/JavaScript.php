<?php
class Vps_Exception_JavaScript extends Vps_Exception
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
        $user = "guest";
        try {
            if ($u = Zend_Registry::get('userModel')->getAuthedUser()) {
                $user = "$u, id $u->id, $u->role";
            }
        } catch (Exception $e) {
            $user = "error getting user";
        }

        $body = '';
        $body .= $this->_format('Exception', get_class($this));
        $body .= $this->_format('Thrown', $this->_url.':'.$this->_lineNumber);
        $body .= $this->_format('Message', $this->getMessage());
        $body .= $this->_format('stack', $this->_stack);
        $body .= $this->_format('REQUEST_URI', $this->_location);
        $body .= $this->_format('HTTP_REFERER', $this->_referrer ? $this->_referrer : '(none)');
        $body .= $this->_format('User', $user);
        $body .= $this->_format('Time', date('H:i:s'));

        $path = 'application/log/error/' . date('Y-m-d');

        $filename = date('H_i_s') . '_' . uniqid() . '.txt';

        return $this->_writeLog($path, $filename, $body, true);
    }
}
