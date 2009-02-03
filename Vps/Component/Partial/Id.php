<?php
class Vps_Component_Partial_Id extends
    Vps_Component_Partial_Paging
{
    public function getIds()
    {
        $component = Vps_Component_Data_Root::getInstance()->getComponentByDbId($this->getParam('componentId'));
        return $component->getComponent()->getItemIds();
    }

}