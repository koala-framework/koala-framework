<?php
/**
 * @package Model
 */
class Kwf_Model_Session extends Kwf_Model_Data_Abstract
{
    protected $_namespace;
    protected $_defaultData = array();

    public function __construct(array $config = array())
    {
        if (isset($config['namespace'])) $this->_namespace = $config['namespace'];
        if (isset($config['defaultData'])) $this->_defaultData = $config['defaultData'];
        parent::__construct($config);
    }

    public function getData()
    {
        if (!isset($this->_data)) {
            if (!isset($this->_namespace)) {
                throw new Kwf_Exception("namespace is required for Model_Session");
            }
            $ns = new Kwf_Session_Namespace($this->_namespace);
            if (!isset($ns->data)) {
                $this->_data = $this->_defaultData;
            } else {
                $this->_data = $ns->data;
            }
        }
        return $this->_data;
    }

    public function resetData()
    {
        $this->_data = $this->_defaultData;
        $this->_afterDataUpdate();
    }

    public function getUniqueIdentifier()
    {
        return $this->_namespace;
    }

    protected function _afterDataUpdate()
    {
        $ns = new Kwf_Session_Namespace($this->_namespace);
        $ns->data = $this->_data;
    }

    public function isEqual(Kwf_Model_Interface $other)
    {
        if ($other instanceof Kwf_Model_Session && $other->_namespace == $this->_namespace) {
            return true;
        }
        return false;
    }
}
