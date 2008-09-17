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
        return $this->find($id)->current();
    }

    public function find($id)
    {
        $ret = parent::find($id);
        if (!$ret->count()) {
            $defaults = array_combine($this->_cols, array_fill(0, count($this->_cols), null));
            $ret = new $this->_rowsetClass(array(
                'table'     => $this,
                'data'      => array($defaults),
                'readyOnly' => false,
                'rowClass'  => $this->_rowClass,
                'stored'    => false
            ));
            $ret->current()->component_id = $id;
            $ret->current()->setFromArray($this->_getDefaultValues($ret->current()));
        }
        return $ret;
    }

    public function createRow(array $data = array())
    {
        $ret = parent::createRow();
        $ret->setFromArray($this->_getDefaultValues($ret));
        $ret->setFromArray($data);
        return $ret;
    }

    protected function _getDefaultValues($row)
    {
        if (Vpc_Abstract::hasSetting($this->_componentClass, 'default')) {
            return Vpc_Abstract::getSetting($this->_componentClass, 'default');
        }
        return array();
    }
}
