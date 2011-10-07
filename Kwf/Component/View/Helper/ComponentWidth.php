<?php
class Kwf_Component_View_Helper_ComponentWidth extends Kwf_Component_View_Helper_Abstract
{
    public function componentWidth(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getContentWidth();
    }
}
