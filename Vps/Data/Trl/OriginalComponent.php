<?php
class Vps_Data_Trl_OriginalComponent extends Vps_Data_Abstract
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
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->$pk, array('ignoreVisible'=>true));
        if (!empty($this->_overrideFieldname)) {
            $fieldname = $this->_overrideFieldname;
        } else {
            $fieldname = $this->getFieldname();
        }
        if (!$c->chained->getComponent()->getRow()) return null;
        return $c->chained
            ->getComponent()
            ->getRow()
            ->{$fieldname};
    }
}
