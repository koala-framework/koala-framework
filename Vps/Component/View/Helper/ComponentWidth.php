<?php
class Vps_Component_View_Helper_ComponentWidth extends Vps_Component_View_Helper_Abstract
{
    public function componentWidth(Vps_Component_Data $data)
    {
        return $data->getComponent()->getContentWidth();
    }
}
