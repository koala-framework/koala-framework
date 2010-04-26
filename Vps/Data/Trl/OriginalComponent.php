<?php
class Vps_Data_Trl_OriginalComponent extends Vps_Data_Abstract
{
    public function load($row)
    {
        $pk = $row->getModel()->getPrimaryKey();
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->$pk, array('ignoreVisible'=>true));

        $fieldname = $this->getFieldname();
        return $c->chained
            ->getComponent()
            ->getRow()
            ->{$fieldname};
    }
}
