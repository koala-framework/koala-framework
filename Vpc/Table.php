<?php
class Vpc_Table extends Vps_Db_Table
{
    protected $_rowClass = 'Vpc_Row';
    protected $_componentClass;
    public function __construct($config = array())
    {
        parent::__construct($config);
        if (!isset($config['componentClass'])) {
            throw new Vps_Exception(trlVps("componentClass is required for Vpc_Table in config"));
        }
        $this->setComponentClass($config['componentClass']);
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
        return $this->find($id)->current();
    }
    public function createRow(array $data = array())
    {
        $defaultValues = Vpc_Abstract::getSetting($this->_componentClass, 'default');
        if (is_array($defaultValues)) {
            $data = array_merge($defaultValues, $data);
        }
        return parent::createRow($data);
    }
}
