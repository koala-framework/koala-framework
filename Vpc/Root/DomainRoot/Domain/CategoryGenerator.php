<?php
class Vpc_Root_DomainRoot_Domain_CategoryGenerator extends Vpc_Root_CategoryGenerator
{
    protected function _getParentDataByRow($row)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getChildComponent('-' . $row->id);
    }
}
