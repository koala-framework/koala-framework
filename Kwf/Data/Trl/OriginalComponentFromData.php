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

    public function load($row)
    {
        $pk = $row->getModel()->getPrimaryKey();
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->$pk, array('ignoreVisible'=>true));
        if (!empty($this->_overrideFieldname)) {
            $fieldname = $this->_overrideFieldname;
        } else {
            $fieldname = $this->getFieldname();
        }
        return $c->chained->row
            ->{$fieldname};
    }
}
