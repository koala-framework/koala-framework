<?php
class Vpc_Root_DomainRoot_Domain_CategoryGenerator extends Vpc_Root_CategoryGenerator
{
    protected function _getParentDataByRow($row)
    {
        throw new Vps_Exception('Not supported, do you really need it?');
    }
}
