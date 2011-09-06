<?php
class Vps_Component_Abstract_Events extends Vps_Component_Events
{
    public static function getGetInstanceClass($componentClass)
    {
        return Vpc_Admin::getComponentClass($componentClass, 'Events');
    }

    protected function _getComponentsByDbIdOwnClass($dbId)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentsByDbId($dbId, array('componentClass'=>$this->_class));
    }
}
