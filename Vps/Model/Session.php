<?php
class Vps_Model_Session extends Vps_Model_Data_Abstract
{
    protected $_namespace;
    protected $_defaultData = array();

    public function __construct(array $config = array())
    {
        if (isset($config['namespace'])) $this->_namespace = $config['namespace'];
        parent::__construct($config);
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            if (!isset($this->_namespace)) {
                throw new Vps_Exception("namespace is required for Model_Session");
            }
            $ns = new Zend_Session_Namespace($this->_namespace);
            $this->_data = $ns->data;
            if (!$this->_data) $this->_data = $this->_defaultData;
        }
        return $this->_data;
    }

    public function reloadSession()
    {
        $data = file_get_contents(session_save_path().'/sess_'.Zend_Session::getId());
        $vars = preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
                $data, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $data = array();
        for($i=0; isset($vars[$i]); $i++) {
            $data[$vars[$i++]]
                = unserialize($vars[$i]);
        }
        $_SESSION = $data;

        $this->_data = null;
        $this->_rows = array();
    }

    public function getUniqueIdentifier()
    {
        return $this->_namespace;
    }

    protected function _afterDataUpdate()
    {
        $ns = new Zend_Session_Namespace($this->_namespace);
        $ns->data = $this->_data;
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        if ($other instanceof Vps_Model_Session && $other->_namespace == $this->_namespace) {
            return true;
        }
        return false;
    }
}
