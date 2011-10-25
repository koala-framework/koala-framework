<?php
class Kwc_Shop_Products_Directory_Generator extends Kwf_Component_Generator_Page_Table
{
    protected function _getParentDataByRow($row, $select)
    {
        $constraints = array();
        if ($select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {
            $constraints['subroot'] = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
        }
        if ($select->hasPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
            $constraints['ignoreVisible'] = $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
        }
        return Kwf_Component_Data_Root::getInstance()->getComponentsByClass(
            'Kwc_Shop_Products_Directory_Component',
            $constraints
        );
    }
}
