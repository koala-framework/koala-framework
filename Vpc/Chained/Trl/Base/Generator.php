<?php
class Vpc_Chained_Trl_Base_Generator extends Vpc_Chained_Trl_Generator
{
    protected function _getChainedChildComponents($parentData, $select)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByClass(Vpc_Abstract::getSetting($this->_class, 'masterComponentClass'))->getChildComponents($select);
    }
}
