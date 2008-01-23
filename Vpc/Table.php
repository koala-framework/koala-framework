<?php
class Vpc_Table extends Vps_Db_Table
{
    protected $_componentClass;
    public function __construct($config = array())
    {
        parent::__construct($config);
        if (isset($config['componentClass'])) {
            $this->setComponentClass($config['componentClass']);
        }
    }

    public function setComponentClass($c)
    {
        $this->_componentClass = $c;
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }

    public function findRow($id)
    {
        $parts = Vpc_Abstract::parseId($id);
        return $this->find($parts['dbId'], $parts['componentKey'])->current();
    }
    public function createRow(array $data = array())
    {
        if (!$this->_componentClass) {
            throw new Vps_Exception("componentClass is required for Vpc_Table in config");
        }
        $defaultValues = Vpc_Abstract::getSetting($this->_componentClass, 'default');
        if (is_array($defaultValues)) {
            $data = array_merge($defaultValues, $data);
        }
        return parent::createRow($data);
    }
}
