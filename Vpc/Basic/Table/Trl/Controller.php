<?php
class Vpc_Basic_Table_Trl_Controller extends Vpc_Basic_Table_Controller
{
    protected function _getClass()
    {
        return $this->_getChainedComponent()->componentClass;
    }

    protected function _getComponentId()
    {
        return $this->_getChainedComponent()->componentId;
    }

    private function _getChainedComponent()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentById($this->_getParam('componentId'), array('ignoreVisible' => true))
            ->chained;
    }
}
