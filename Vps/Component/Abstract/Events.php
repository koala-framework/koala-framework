<?php
class Vps_Component_Abstract_Events extends Vps_Component_Events
{
    protected function _getComponentsByDbIdOwnClass($dbId)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentsByDbId($dbId, array('componentClass'=>$this->_class));
    }
}
