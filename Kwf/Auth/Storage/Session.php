<?php
class Kwf_Auth_Storage_Session extends Zend_Auth_Storage_Session
{
    public function __construct($namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT)
    {
        $this->_namespace = $namespace;
        $this->_member    = $member;
    }

    private function _getSession()
    {
        if (!isset($this->_session)) {
            $this->_session   = new Kwf_Session_Namespace($this->_namespace);
        }
        return $this->_session;
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !isset($this->_getSession()->{$this->_member});
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return mixed
     */
    public function read()
    {
        if (!Zend_Session::isStarted() && !Zend_Session::sessionExists()) {
            return array();
        }
        return $this->_getSession()->{$this->_member};
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @param  mixed $contents
     * @return void
     */
    public function write($contents)
    {
        $this->_getSession()->{$this->_member} = $contents;
    }

    /**
     * Defined by Zend_Auth_Storage_Interface
     *
     * @return void
     */
    public function clear()
    {
        unset($this->_getSession()->{$this->_member});
    }
}
