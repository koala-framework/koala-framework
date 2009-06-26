<?php
class Vpc_Shop_Products_Directory_Generator extends Vps_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        $constraints = array();
        if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
            $constraints['subroot'] = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
        }
        if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
            $constraints['ignoreVisible'] = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
        }
        return Vps_Component_Data_Root::getInstance()->getComponentsByClass(
            'Vpc_Shop_Products_Directory_Component',
            $constraints
        );
    }
}
