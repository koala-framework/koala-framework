<?php
class Kwf_Data_Trl_OriginalComponentFromData extends Kwf_Data_Abstract
{
    protected $_overrideFieldname;

    public function __construct($overrideFieldname = null)
    {
        if (!is_null($overrideFieldname)) {
            $this->_overrideFieldname = $overrideFieldname;
        }
    }

    public function load($row, array $info = array())
    {
        return $this->_getChainedRow($row)->{$this->getFieldname()};
    }

    protected function _getChainedRow($row)
    {
        $pk = $row->getModel()->getPrimaryKey();
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->$pk, array('ignoreVisible'=>true));
        return $c->chained->row;
    }

    public function getFieldname()
    {
        if (!empty($this->_overrideFieldname)) {
            $fieldname = $this->_overrideFieldname;
        } else {
            $fieldname = $this->getFieldname();
        }
        return $fieldname;
    }
}
