<?php
class Vpc_Table extends Vps_Db_Table
{
    protected $_rowClass = 'Vpc_Row';
    protected $_componentClass;
    public function __construct($config = array())
    {
        parent::__construct($config);
        if (!isset($config['componentClass'])) {
            throw new Vps_Exception("componentClass is erforderlich für Vpc_Table in config");
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
        $row = $this->find($id)->current();
        if (!$row) {
            $row = $this->createRow();
        }
        return $row;
    }
    public function find($id) {
        $ret = parent::find($id);
        if (!$ret->count()) {
            $data = $this->createRow()->toArray();
            $data['component_id'] = $id;
            $ret = new $this->_rowsetClass(array(
                'table'     => $this,
                'data'      => array($data),
                'readyOnly' => false,
                'rowClass'  => $this->_rowClass,
                'stored'    => false
            ));
        }
        return $ret;
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
